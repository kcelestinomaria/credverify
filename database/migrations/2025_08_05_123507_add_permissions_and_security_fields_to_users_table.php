<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Permissions and role management
            $table->json('permissions')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('permissions');
            
            // Security and audit fields
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->string('last_login_user_agent')->nullable()->after('last_login_ip');
            
            // Account security
            $table->timestamp('password_changed_at')->nullable()->after('last_login_user_agent');
            $table->integer('failed_login_attempts')->default(0)->after('password_changed_at');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            
            // Multi-factor authentication
            $table->string('mfa_secret')->nullable()->after('locked_until');
            $table->boolean('mfa_enabled')->default(false)->after('mfa_secret');
            
            // Session management
            $table->timestamp('session_expires_at')->nullable()->after('mfa_enabled');
            
            // Audit fields
            $table->string('created_by')->nullable()->after('session_expires_at');
            $table->string('updated_by')->nullable()->after('created_by');
            $table->softDeletes()->after('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'permissions',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'last_login_user_agent',
                'password_changed_at',
                'failed_login_attempts',
                'locked_until',
                'mfa_secret',
                'mfa_enabled',
                'session_expires_at',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
