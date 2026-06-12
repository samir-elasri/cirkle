<?php

namespace Database\Seeders;

use App\Models\Core\Setting;
use Illuminate\Database\Seeder;

class RegistrationFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = Setting::find(1);
        
        if ($setting) {
            // Set default registration fee
            $setting->registration_fee = 25.00;
            $setting->save();
            
            // Set translatable labels
            $setting->translateOrNew('fr')->registration_fee_title = 'Frais d\'inscription';
            $setting->translateOrNew('en')->registration_fee_title = 'Registration Fee';
            $setting->save();
            
            $this->command->info('Registration fee settings have been configured.');
        } else {
            $this->command->error('Settings record not found. Please run SettingsTableSeeder first.');
        }
    }
}