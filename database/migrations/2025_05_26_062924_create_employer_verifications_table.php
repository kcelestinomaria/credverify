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
        Schema::create('employer_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('credential_id')->constrained()->onDelete('cascade');
            $table->timestamp('searched_at');
            $table->string('ip_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_verifications');
    }
};
