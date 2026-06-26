<?php

namespace App\Console\Commands;

use App\Models\Core\Subscriber;
use App\Models\Core\User;
use Illuminate\Console\Command;

/**
 * Crée / réinitialise deux comptes de DÉMO partageables (pour Denis) :
 *   - un compte ADMIN (tableau de bord)            → guard « users »
 *   - un compte FOURNISSEUR de test en français    → guard « subscribers »
 *
 * Idempotent : relancer la commande ne crée pas de doublon, ça remet juste le mot de passe.
 * Le fournisseur démo a member_number = NULL (n'affecte pas le compteur de membres) et
 * is_public = false (n'apparaît pas dans le catalogue public).
 */
class MakeDemoAccounts extends Command
{
    protected $signature = 'cirkle:demo-accounts {--password=CirkleDemo2026}';
    protected $description = 'Crée/réinitialise les comptes démo admin + fournisseur (Denis)';

    public const ADMIN_EMAIL = 'demo.admin@cirkle.test';
    public const SUPPLIER_EMAIL = 'demo.fournisseur@cirkle.test';

    public function handle(): int
    {
        $password = (string) $this->option('password');

        // ── Compte ADMIN (table users) ──
        $admin = User::firstOrNew(['email' => self::ADMIN_EMAIL]);
        $admin->first_name = 'Démo';
        $admin->last_name = 'Admin';
        $admin->admin = true;
        $admin->active = true;
        $admin->email_verified_at = now();
        $admin->password = $password; // UserBase::setPasswordAttribute hache automatiquement
        $admin->save();

        // ── Compte FOURNISSEUR de test (table subscribers, français) ──
        $supplier = Subscriber::firstOrNew(['email' => self::SUPPLIER_EMAIL]);
        $supplier->first_name = 'Démo';
        $supplier->last_name = 'Fournisseur';
        $supplier->company_name = 'Entreprise Démo (test)';
        $supplier->owner_names = 'Démo Fournisseur';
        $supplier->preference_language = 'fr';
        $supplier->is_provider = true;
        $supplier->is_public = false;          // caché du catalogue public
        $supplier->active = true;
        $supplier->accept_condition = true;
        $supplier->email_validated = true;
        $supplier->registration_completed = true;
        $supplier->street = '123 rue de la Démo';
        $supplier->city = 'Montréal';
        $supplier->postal_code = 'H2X 1Y4';
        $supplier->phone = '514-000-0000';
        $supplier->password = $password;
        $supplier->save();

        // Pas de numéro de membre : n'affecte pas le compteur de la page d'accueil.
        if (!is_null($supplier->member_number)) {
            $supplier->member_number = null;
            $supplier->save();
        }

        $this->info('Comptes démo prêts :');
        $this->line('  ADMIN       : ' . self::ADMIN_EMAIL . '  /  ' . $password);
        $this->line('  FOURNISSEUR : ' . self::SUPPLIER_EMAIL . '  /  ' . $password);

        return self::SUCCESS;
    }
}
