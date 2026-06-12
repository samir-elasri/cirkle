#!/usr/bin/env bash
#
# Cirkle — manual deploy to node14 (cirkleservices.com)
# ----------------------------------------------------------------------------
# Version control is git; deployment is THIS script, run explicitly. No CI/CD.
#
#   Usage:  bash scripts/deploy.sh
#
# Transport: git-driven tar-over-ssh (NOT rsync — Git Bash on this machine has
# no rsync). Only files changed since the last deployed commit are sent:
#   - the server stores the deployed commit in ~/.cirkle_deployed_sha
#   - first deploy falls back to BASELINE_SHA (the initial commit, which IS
#     the server's tree: the repo was created from the Nov 2025 server pull)
#   - the exact file list (sends + deletions) is shown before the "yes" prompt
#   - deploys ship the committed state: the clean-tree check guarantees the
#     working tree (which tar reads) is identical to HEAD
#
# What it does, in order:
#   1. Verifies you are in the repo with a CLEAN git working tree (aborts otherwise).
#   2. Computes changed/deleted files since the server's deployed commit.
#   3. Prints them and requires you to type "yes" before anything is transferred.
#   4. tar-over-ssh the changed files; explicit rm for deletions.
#      Never sent: .env*, vendor/, node_modules/, storage/, docs/, *.sql,
#      public_html/lang/ (CMS overrides), public_html/dist/compiled/lang/
#      (regenerated remotely by locales:compile).
#   5. Over SSH: composer install --no-dev, migrate --force, locales:compile,
#      then view/config/route CLEARS (this app breaks under route:cache /
#      config:cache — see section 6).
#   6. Smoke-checks the homepage for HTTP 200, then records the deployed SHA.
#
# Notes:
#   - Assets are NOT rebuilt here. The committed public_html/dist/compiled/*
#     are authoritative (the SCSS vendor/ rebuild is a separate, deferred task).
#   - Working-tree text files are CRLF on Windows; PHP/Blade/JSON tolerate it.
#     *.sh stays LF via .gitattributes (eol=lf).
# ----------------------------------------------------------------------------

set -euo pipefail

# ── Config ──────────────────────────────────────────────────────────────────
SSH_HOST="${CIRKLE_SSH_HOST:-cirkle}"          # ~/.ssh/config alias (host/port/user/key)
REMOTE_PATH="${CIRKLE_REMOTE_PATH:-/home/yymcsmwb}"
SMOKE_URL="${CIRKLE_SMOKE_URL:-https://cirkleservices.com/fr}"
SHA_FILE=".cirkle_deployed_sha"                # lives in the remote $HOME
BASELINE_SHA="34083c2"                         # initial commit == server tree (Nov 2025 pull)

# Paths that must never reach the server (git pathspec excludes).
# .env*, vendor/, node_modules/, storage/ are untracked, so git never lists them.
EXCLUDE_PATHSPECS=(
  ':(exclude)docs'
  ':(exclude)*.sql'
  ':(exclude).env*'
  ':(exclude)public_html/lang'
  ':(exclude)public_html/dist/compiled/lang'
)

# Resolve repo root from this script's location, and always run from there.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
cd "${REPO_ROOT}"

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
HEAD_SHA="$(git rev-parse HEAD)"
HEAD_SHORT="$(git rev-parse --short HEAD)"
HEAD_MSG="$(git log -1 --pretty=%s)"

bold "──────────────────────────────────────────────"
bold " Cirkle deploy → ${SSH_HOST}:${REMOTE_PATH}"
bold "──────────────────────────────────────────────"
green "✓ Clean working tree"
echo  "  Branch : ${BRANCH}"
echo  "  Commit : ${HEAD_SHORT}  ${HEAD_MSG}"
echo

# ── 2. SSH reachability + last deployed commit ──────────────────────────────
yellow "Checking SSH connectivity to ${SSH_HOST}…"
# -n everywhere stdin isn't needed: otherwise ssh would swallow a piped/typed
# confirmation meant for the "yes" prompt below.
ssh -n -o BatchMode=yes -o ConnectTimeout=10 "${SSH_HOST}" 'echo ok' >/dev/null 2>&1 \
  || die "Cannot reach ${SSH_HOST} over SSH (check ~/.ssh/config alias 'cirkle' or set CIRKLE_SSH_HOST)."
green "✓ SSH OK"

LAST_SHA="$(ssh -n -o BatchMode=yes "${SSH_HOST}" "cat ~/${SHA_FILE} 2>/dev/null" || true)"
if [[ -z "${LAST_SHA}" ]]; then
  yellow "No ${SHA_FILE} on the server — using baseline ${BASELINE_SHA} (initial pull)."
  LAST_SHA="${BASELINE_SHA}"
fi
git cat-file -e "${LAST_SHA}^{commit}" 2>/dev/null \
  || die "Deployed commit ${LAST_SHA} is unknown locally — fetch/pull first."

if [[ "$(git rev-parse "${LAST_SHA}")" == "${HEAD_SHA}" ]]; then
  green "Server is already at ${HEAD_SHORT} — nothing to deploy."
  exit 0
fi
echo "  Server is at : $(git log -1 --pretty='%h  %s' "${LAST_SHA}")"
echo

# ── 3. Compute + show the delta ─────────────────────────────────────────────
# core.quotepath=off keeps accented filenames literal so tar -T can read them.
SEND_LIST="$(mktemp)"
DEL_LIST="$(mktemp)"
trap 'rm -f "${SEND_LIST}" "${DEL_LIST}"' EXIT

git -c core.quotepath=off diff --name-only --diff-filter=ACMR \
  "${LAST_SHA}" "${HEAD_SHA}" -- . "${EXCLUDE_PATHSPECS[@]}" > "${SEND_LIST}"
git -c core.quotepath=off diff --name-only --diff-filter=D \
  "${LAST_SHA}" "${HEAD_SHA}" -- . "${EXCLUDE_PATHSPECS[@]}" > "${DEL_LIST}"

N_SEND="$(grep -c . "${SEND_LIST}" || true)"
N_DEL="$(grep -c . "${DEL_LIST}" || true)"

if [[ "${N_SEND}" -eq 0 && "${N_DEL}" -eq 0 ]]; then
  green "Delta is empty after exclusions — nothing to deploy."
  exit 0
fi

bold "Files to SEND (${N_SEND}):"
sed 's/^/   + /' "${SEND_LIST}"
if [[ "${N_DEL}" -gt 0 ]]; then
  bold "Files to DELETE on the server (${N_DEL}):"
  sed 's/^/   - /' "${DEL_LIST}"
fi
echo
yellow "Never sent: .env* · vendor/ · node_modules/ · storage/ · docs/ · *.sql · public_html/lang/ · public_html/dist/compiled/lang/"
echo

# ── 4. Explicit confirmation ────────────────────────────────────────────────
bold "This will push the above to ${SSH_HOST}:${REMOTE_PATH} and run migrations + cache clears."
printf 'Type "yes" to proceed: '
read -r CONFIRM
[[ "${CONFIRM}" == "yes" ]] || { yellow "Aborted — nothing was transferred."; exit 0; }
echo

# ── 5. Transfer ─────────────────────────────────────────────────────────────
if [[ "${N_SEND}" -gt 0 ]]; then
  bold "→ Sending ${N_SEND} file(s)…"
  tar -czf - -T "${SEND_LIST}" | ssh "${SSH_HOST}" "tar -xzf - -C '${REMOTE_PATH}'"
  green "✓ Files sent"
fi

if [[ "${N_DEL}" -gt 0 ]]; then
  bold "→ Deleting ${N_DEL} file(s) on the server…"
  # File list goes over stdin; one path per line, removed relative to REMOTE_PATH.
  ssh "${SSH_HOST}" "cd '${REMOTE_PATH}' && while IFS= read -r f; do rm -f -- \"\$f\"; done" < "${DEL_LIST}"
  green "✓ Deletions done"
fi
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

# NO --no-dev: app/helpers/debug.php instantiates Faker\Factory at file scope
# and is autoloaded on every request (composer.json "files"), so the app cannot
# boot without dev dependencies. Running --no-dev took prod down on 2026-06-12.
# Revisit only after debug.php stops hard-requiring Faker.
\$COMPOSER install --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan locales:compile
php artisan view:clear
php artisan config:clear
php artisan route:clear
REMOTE
green "✓ Remote post-deploy complete"
echo

# ── 7. Smoke check, then record the deployed commit ─────────────────────────
bold "→ Smoke-checking ${SMOKE_URL} …"
HTTP_CODE="$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "${SMOKE_URL}" || echo 000)"
if [[ "${HTTP_CODE}" == "200" ]]; then
  green "✓ ${SMOKE_URL} → HTTP ${HTTP_CODE}"
else
  yellow "⚠ ${SMOKE_URL} → HTTP ${HTTP_CODE} (verify manually before trusting this deploy)"
fi

ssh -n "${SSH_HOST}" "echo '${HEAD_SHA}' > ~/${SHA_FILE}"
green "✓ Recorded deployed commit ${HEAD_SHORT} on the server"

echo
green "Deploy finished: ${BRANCH}@${HEAD_SHORT} → ${SSH_HOST}:${REMOTE_PATH}"
