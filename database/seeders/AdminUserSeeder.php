<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default institution
        $institution = Institution::firstOrCreate(
            ['slug' => 'jasiri-university'],
            [
                'name' => 'Jasiri University',
                'contact_email' => 'admin@jasiri.com',
                'description' => 'Leading academic institution for digital credentials',
                'logo_url' => null,
            ]
        );

        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@jasiri.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'institution_id' => $institution->id,
                'is_active' => true,
                'permissions' => [
                    'manage-all-institutions',
                    'manage-credentials',
                    'manage-users',
                    'view-reports',
                    'export-data',
                    'view-audit-logs',
                    'manage-system',
                ],
                'created_by' => 'system',
            ]
        );

        // Create demo admin user
        $demoAdmin = User::firstOrCreate(
            ['email' => 'demo@jasiri.com'],
            [
                'name' => 'Demo Administrator',
                'password' => Hash::make('demo123'),
                'role' => 'admin',
                'institution_id' => $institution->id,
                'is_active' => true,
                'permissions' => [
                    'manage-credentials',
                    'manage-users',
                    'view-reports',
                    'manage-institutions',
                ],
                'created_by' => 'system',
            ]
        );

        // Create demo employer user
        $demoEmployer = User::firstOrCreate(
            ['email' => 'employer@example.com'],
            [
                'name' => 'Demo Employer',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'company' => 'Demo Company Ltd',
                'is_active' => true,
                'permissions' => [
                    'verify-credentials',
                    'view-verification-history',
                    'bulk-verify',
                ],
                'created_by' => 'system',
            ]
        );

        // Create additional demo employer
        $employer2 = User::firstOrCreate(
            ['email' => 'hr@safaricom.co.ke'],
            [
                'name' => 'Safaricom HR',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'company' => 'Safaricom PLC',
                'is_active' => true,
                'permissions' => [
                    'verify-credentials',
                    'view-verification-history',
                    'bulk-verify',
                ],
                'created_by' => 'system',
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('Super Admin: admin@jasiri.com / admin123');
        $this->command->info('Demo Admin: demo@jasiri.com / demo123');
        $this->command->info('Demo Employer: employer@example.com / password');
        $this->command->info('Safaricom HR: hr@safaricom.co.ke / password');
    }
}
