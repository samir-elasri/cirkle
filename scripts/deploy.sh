#!/usr/bin/env bash
#
# Cirkle — manual deploy to node14 (cirkleservices.com)
# ----------------------------------------------------------------------------
# Version control is git; deployment is THIS script, run explicitly. No CI/CD.
#
#   Usage:  bash scripts/deploy.sh
#
# What it does, in order:
#   1. Verifies you are in the repo with a CLEAN git working tree (aborts otherwise).
#   2. Prints exactly what will change on the server (rsync dry-run summary).
#   3. Requires you to type "yes" before anything is transferred.
#   4. rsync-pushes the app to node14 (excludes .env, vendor, node_modules,
#      storage, .git, docs, *.sql, *.log — see EXCLUDES below).
#   5. Over SSH: composer install --no-dev, migrate --force, cache view/config/route.
#   6. Smoke-checks the homepage for HTTP 200.
#
# Notes:
#   - Assets are NOT rebuilt here. The committed public_html/dist/compiled/*
#     are authoritative (the SCSS vendor/ rebuild is a separate, deferred task).
#   - rsync runs WITHOUT --delete on purpose, so server-side uploads
#     (public_html/medias, files, imagecache, storage) are never clobbered.
# ----------------------------------------------------------------------------

set -euo pipefail

# ── Config ──────────────────────────────────────────────────────────────────
# Preferred: the `cirkle` SSH alias from ~/.ssh/config (encodes host/port/user/key).
# If you don't have the alias, set SSH_HOST to the explicit form below.
SSH_HOST="${CIRKLE_SSH_HOST:-cirkle}"
# Explicit fallback (uncomment / export CIRKLE_SSH_HOST to override):
#   ssh -p 5022 -i ~/.ssh/cirkle_n0c yymcsmwb@node14-ca.n0c.com
REMOTE_PATH="${CIRKLE_REMOTE_PATH:-/home/yymcsmwb}"
SMOKE_URL="${CIRKLE_SMOKE_URL:-https://cirkleservices.com/fr}"

# Resolve repo root from this script's location, and always run from there.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
cd "${REPO_ROOT}"

EXCLUDES=(
  --exclude='.git/'
  --exclude='.github/'
  --exclude='.idea/'
  --exclude='.vscode/'
  --exclude='.env'
  --exclude='.env.*'
  --exclude='vendor/'
  --exclude='node_modules/'
  --exclude='storage/'
  --exclude='public_html/storage'   # symlink target lives in storage/ (excluded)
  --exclude='public_html/lang/'     # server-owned CMS translation overrides (admin UI edits)
  --exclude='public_html/dist/compiled/lang/'  # regenerated remotely by locales:compile
  --exclude='docs/'
  --exclude='*.sql'
  --exclude='*.log'
  --exclude='.DS_Store'
  --exclude='Homestead.*'
  --exclude='.phpunit.result.cache'
)

# ── Pretty output ───────────────────────────────────────────────────────────
bold() { printf '\033[1m%s\033[0m\n' "$*"; }
green() { printf '\033[32m%s\033[0m\n' "$*"; }
red() { printf '\033[31m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
die() { red "✗ $*"; exit 1; }

# ── 1. Pre-flight: git repo + clean tree ────────────────────────────────────
git rev-parse --is-inside-work-tree >/dev/null 2>&1 || die "Not inside a git repository."

if [[ -n "$(git status --porcelain)" ]]; then
  red "✗ Working tree is NOT clean. Commit or stash before deploying:"
  git status --short
  exit 1
fi

BRANCH="$(git rev-parse --abbrev-ref HEAD)"
HEAD_SHA="$(git rev-parse --short HEAD)"
HEAD_MSG="$(git log -1 --pretty=%s)"

bold "──────────────────────────────────────────────"
bold " Cirkle deploy → ${SSH_HOST}:${REMOTE_PATH}"
bold "──────────────────────────────────────────────"
green "✓ Clean working tree"
echo  "  Branch : ${BRANCH}"
echo  "  Commit : ${HEAD_SHA}  ${HEAD_MSG}"
echo

# ── 2. Verify SSH reachability ──────────────────────────────────────────────
yellow "Checking SSH connectivity to ${SSH_HOST}…"
ssh -o BatchMode=yes -o ConnectTimeout=10 "${SSH_HOST}" 'echo ok' >/dev/null 2>&1 \
  || die "Cannot reach ${SSH_HOST} over SSH (check ~/.ssh/config alias 'cirkle' or set CIRKLE_SSH_HOST)."
green "✓ SSH OK"
echo

# ── 3. Dry-run summary (what WILL change) ───────────────────────────────────
bold "Files that would change on the server (rsync dry-run):"
echo
set +e
rsync -azz --dry-run --itemize-changes --human-readable \
  "${EXCLUDES[@]}" \
  -e "ssh" \
  ./ "${SSH_HOST}:${REMOTE_PATH}/" | sed 's/^/   /'
RSYNC_DRY_RC=$?
set -e
[[ ${RSYNC_DRY_RC} -eq 0 ]] || die "rsync dry-run failed (rc=${RSYNC_DRY_RC})."
echo
yellow "Excludes: .env(.* ) · vendor/ · node_modules/ · storage/ · .git/ · docs/ · *.sql · *.log"
yellow "rsync runs WITHOUT --delete (server uploads are preserved)."
echo

# ── 4. Explicit confirmation ────────────────────────────────────────────────
bold "This will push the above to ${SSH_HOST}:${REMOTE_PATH} and run migrations + cache rebuild."
printf 'Type "yes" to proceed: '
read -r CONFIRM
[[ "${CONFIRM}" == "yes" ]] || { yellow "Aborted — nothing was transferred."; exit 0; }
echo

# ── 5. Real rsync push ──────────────────────────────────────────────────────
bold "→ Syncing files…"
rsync -azz --human-readable --info=stats1,progress2 \
  "${EXCLUDES[@]}" \
  -e "ssh" \
  ./ "${SSH_HOST}:${REMOTE_PATH}/"
green "✓ Files synced"
echo

# ── 6. Remote post-deploy ───────────────────────────────────────────────────
# IMPORTANT — clears, not caches: this app has NEVER run with route/config
# caches (verified on node14: bootstrap/cache/ has only packages/services).
#   - route:cache  → HTTP 500 (routes depend on the request locale + DB pages)
#   - config:cache → would null the raw env() calls (e.g. Stripe keys in
#     BasicCartController) since .env stops being loaded once config is cached
bold "→ Running remote post-deploy (composer / migrate / locales / cache clears)…"
ssh "${SSH_HOST}" 'bash -s' <<REMOTE
set -euo pipefail
cd "${REMOTE_PATH}"

# node14 has no system composer; install composer.phar to \$HOME once and reuse it.
if command -v composer >/dev/null 2>&1; then
  COMPOSER=composer
else
  if [ ! -f "\$HOME/composer.phar" ]; then
    echo "composer introuvable sur le serveur - installation de ~/composer.phar"
    php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
    php /tmp/composer-setup.php --install-dir="\$HOME" --filename=composer.phar --quiet
    rm -f /tmp/composer-setup.php
  fi
  COMPOSER="php \$HOME/composer.phar"
fi

\$COMPOSER install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan locales:compile
php artisan view:clear
php artisan config:clear
php artisan route:clear
REMOTE
green "✓ Remote post-deploy complete"
echo

# ── 7. Smoke check ──────────────────────────────────────────────────────────
bold "→ Smoke-checking ${SMOKE_URL} …"
HTTP_CODE="$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "${SMOKE_URL}" || echo 000)"
if [[ "${HTTP_CODE}" == "200" ]]; then
  green "✓ ${SMOKE_URL} → HTTP ${HTTP_CODE}"
else
  yellow "⚠ ${SMOKE_URL} → HTTP ${HTTP_CODE} (verify manually)"
fi

echo
green "Deploy finished: ${BRANCH}@${HEAD_SHA} → ${SSH_HOST}:${REMOTE_PATH}"
