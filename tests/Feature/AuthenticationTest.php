<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard()
    {
        // Create institution
        $institution = Institution::create([
            'name' => 'Test University',
            'slug' => 'test-university',
            'domain' => 'test.edu',
            'contact_email' => 'admin@test.edu',
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution->id,
            'is_active' => true,
            'permissions' => ['manage-users', 'manage-credentials', 'view-reports'],
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_employer_cannot_access_admin_dashboard()
    {
        // Create institution
        $institution = Institution::create([
            'name' => 'Test University',
            'slug' => 'test-university',
            'domain' => 'test.edu',
            'contact_email' => 'admin@test.edu',
        ]);

        // Create employer user
        $employer = User::create([
            'name' => 'Employer User',
            'email' => 'employer@example.com',
            'password' => bcrypt('password'),
            'role' => 'employer',
            'institution_id' => $institution->id,
            'is_active' => true,
            'permissions' => ['verify-credentials'],
        ]);

        $response = $this->actingAs($employer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_manage_users()
    {
        // Create institution
        $institution = Institution::create([
            'name' => 'Test University',
            'slug' => 'test-university',
            'domain' => 'test.edu',
            'contact_email' => 'admin@test.edu',
        ]);

        // Create admin user without manage-users permission
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution->id,
            'is_active' => true,
            'permissions' => ['manage-credentials'], // No manage-users permission
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_all_institutions()
    {
        // Create multiple institutions
        $institution1 = Institution::create([
            'name' => 'University 1',
            'slug' => 'university-1',
            'domain' => 'uni1.edu',
            'contact_email' => 'admin@uni1.edu',
        ]);

        $institution2 = Institution::create([
            'name' => 'University 2',
            'slug' => 'university-2',
            'domain' => 'uni2.edu',
            'contact_email' => 'admin@uni2.edu',
        ]);

        // Create super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@jasiri.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution1->id,
            'is_active' => true,
            'permissions' => ['manage-all-institutions', 'manage-users', 'manage-credentials'],
        ]);

        // Test that super admin can access admin dashboard
        $response = $this->actingAs($superAdmin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_inactive_user_cannot_access_system()
    {
        // Create institution
        $institution = Institution::create([
            'name' => 'Test University',
            'slug' => 'test-university',
            'domain' => 'test.edu',
            'contact_email' => 'admin@test.edu',
        ]);

        // Create inactive admin user
        $admin = User::create([
            'name' => 'Inactive Admin',
            'email' => 'inactive@test.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution->id,
            'is_active' => false, // Inactive user
            'permissions' => ['manage-users', 'manage-credentials'],
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_user_permissions_are_correctly_loaded()
    {
        // Create institution
        $institution = Institution::create([
            'name' => 'Test University',
            'slug' => 'test-university',
            'domain' => 'test.edu',
            'contact_email' => 'admin@test.edu',
        ]);

        // Create user with specific permissions
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution->id,
            'is_active' => true,
            'permissions' => ['manage-users', 'manage-credentials', 'view-reports'],
        ]);

        // Test that permissions are correctly loaded
        $this->assertTrue($user->hasPermission('manage-users'));
        $this->assertTrue($user->hasPermission('manage-credentials'));
        $this->assertTrue($user->hasPermission('view-reports'));
        $this->assertFalse($user->hasPermission('manage-system')); // Not in permissions
        $this->assertTrue($user->hasAnyPermission(['manage-users', 'manage-system'])); // Has one
        $this->assertFalse($user->hasAllPermissions(['manage-users', 'manage-system'])); // Doesn't have all
    }

    public function test_user_role_methods_work_correctly()
    {
        // Create institution
        $institution = Institution::create([
            'name' => 'Test University',
            'slug' => 'test-university',
            'domain' => 'test.edu',
            'contact_email' => 'admin@test.edu',
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution->id,
            'is_active' => true,
            'permissions' => ['manage-users'],
        ]);

        // Create employer user
        $employer = User::create([
            'name' => 'Employer User',
            'email' => 'employer@example.com',
            'password' => bcrypt('password'),
            'role' => 'employer',
            'institution_id' => $institution->id,
            'is_active' => true,
            'permissions' => ['verify-credentials'],
        ]);

        // Test role methods
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isEmployer());
        $this->assertFalse($admin->isSuperAdmin());

        $this->assertTrue($employer->isEmployer());
        $this->assertFalse($employer->isAdmin());
        $this->assertFalse($employer->isSuperAdmin());
    }

    public function test_user_activity_status_works()
    {
        // Create institution
        $institution = Institution::create([
            'name' => 'Test University',
            'slug' => 'test-university',
            'domain' => 'test.edu',
            'contact_email' => 'admin@test.edu',
        ]);

        // Create active user
        $activeUser = User::create([
            'name' => 'Active User',
            'email' => 'active@test.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution->id,
            'is_active' => true,
            'permissions' => ['manage-users'],
        ]);

        // Create inactive user
        $inactiveUser = User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@test.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => $institution->id,
            'is_active' => false,
            'permissions' => ['manage-users'],
        ]);

        // Test activity status
        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
    }
} 