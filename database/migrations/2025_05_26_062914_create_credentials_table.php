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
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('credential_type');
            $table->string('issued_by');
            $table->date('issued_on');
            $table->string('credential_file_path');
            $table->string('hash');
            $table->string('verification_code')->unique();
            $table->string('json_path')->nullable();
            $table->enum('status', ['Verified', 'Revoked'])->default('Verified');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
