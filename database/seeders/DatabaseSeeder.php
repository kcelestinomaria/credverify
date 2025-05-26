<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institution;
use App\Models\Credential;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test institutions
        $strathmore = Institution::firstOrCreate(
            ['slug' => 'strathmore-university'],
            [
                'name' => 'Strathmore University',
                'contact_email' => 'registrar@strathmore.edu',
                'logo_url' => null,
                'description' => 'A leading private university in Kenya offering world-class education.',
            ]
        );

        $uon = Institution::firstOrCreate(
            ['slug' => 'university-of-nairobi'],
            [
                'name' => 'University of Nairobi',
                'contact_email' => 'registrar@uonbi.ac.ke',
                'logo_url' => null,
                'description' => 'Kenya\'s premier university and one of the largest universities in Kenya.',
            ]
        );

        $kca = Institution::firstOrCreate(
            ['slug' => 'kca-university'],
            [
                'name' => 'KCA University',
                'contact_email' => 'registrar@kca.ac.ke',
                'logo_url' => null,
                'description' => 'A chartered private university in Kenya.',
            ]
        );

        // Create admin users for institutions
        $strathmoreAdmin = User::firstOrCreate(
            ['email' => 'admin@strathmore.edu'],
            [
                'name' => 'Strathmore Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'institution_id' => $strathmore->id,
            ]
        );

        $uonAdmin = User::firstOrCreate(
            ['email' => 'admin@uonbi.ac.ke'],
            [
                'name' => 'UoN Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'institution_id' => $uon->id,
            ]
        );

        // Create employer users
        $employer1 = User::firstOrCreate(
            ['email' => 'hr@safaricom.co.ke'],
            [
                'name' => 'Safaricom HR',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'company' => 'Safaricom PLC',
                'institution_id' => null,
            ]
        );

        $employer2 = User::firstOrCreate(
            ['email' => 'hr@equitybank.co.ke'],
            [
                'name' => 'Equity Bank HR',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'company' => 'Equity Bank Kenya Limited',
                'institution_id' => null,
            ]
        );

        // Create sample credentials
        $credentials = [
            [
                'verification_code' => 'STR2023A',
                'user_id' => $strathmoreAdmin->id,
                'institution_id' => $strathmore->id,
                'student_name' => 'John Doe',
                'student_email' => 'john.doe@student.strathmore.edu',
                'credential_type' => 'Bachelor of Science in Computer Science',
                'issued_by' => 'School of Computing and Engineering Sciences',
                'issued_on' => '2023-12-15',
                'credential_file_path' => 'credentials/sample_certificate.pdf',
                'hash' => hash('sha256', 'sample_content_1'),
                'json_path' => null,
                'status' => 'verified',
            ],
            [
                'verification_code' => 'STR2023B',
                'user_id' => $strathmoreAdmin->id,
                'institution_id' => $strathmore->id,
                'student_name' => 'Jane Smith',
                'student_email' => 'jane.smith@student.strathmore.edu',
                'credential_type' => 'Master of Business Administration',
                'issued_by' => 'Strathmore Business School',
                'issued_on' => '2023-11-20',
                'credential_file_path' => 'credentials/sample_certificate.pdf',
                'hash' => hash('sha256', 'sample_content_2'),
                'json_path' => null,
                'status' => 'verified',
            ],
            [
                'verification_code' => 'UON2023A',
                'user_id' => $uonAdmin->id,
                'institution_id' => $uon->id,
                'student_name' => 'Michael Johnson',
                'student_email' => 'michael.johnson@students.uonbi.ac.ke',
                'credential_type' => 'Bachelor of Engineering in Civil Engineering',
                'issued_by' => 'School of Engineering',
                'issued_on' => '2023-10-30',
                'credential_file_path' => 'credentials/sample_certificate.pdf',
                'hash' => hash('sha256', 'sample_content_3'),
                'json_path' => null,
                'status' => 'verified',
            ],
            [
                'verification_code' => 'UON2023B',
                'user_id' => $uonAdmin->id,
                'institution_id' => $uon->id,
                'student_name' => 'Sarah Wilson',
                'student_email' => 'sarah.wilson@students.uonbi.ac.ke',
                'credential_type' => 'Bachelor of Arts in Economics',
                'issued_by' => 'School of Economics',
                'issued_on' => '2023-09-15',
                'credential_file_path' => 'credentials/sample_certificate.pdf',
                'hash' => hash('sha256', 'sample_content_4'),
                'json_path' => null,
                'status' => 'revoked',
            ],
        ];

        foreach ($credentials as $credentialData) {
            Credential::firstOrCreate(
                ['verification_code' => $credentialData['verification_code']],
                $credentialData
            );
        }

        // Create a super admin user (not tied to any institution)
        User::firstOrCreate(
            ['email' => 'admin@credverify.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'institution_id' => null,
            ]
        );

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Strathmore Admin: admin@strathmore.edu / password');
        $this->command->info('UoN Admin: admin@uonbi.ac.ke / password');
        $this->command->info('Employer 1: hr@safaricom.co.ke / password');
        $this->command->info('Employer 2: hr@equitybank.co.ke / password');
        $this->command->info('Super Admin: admin@credverify.com / password');
    }
}
