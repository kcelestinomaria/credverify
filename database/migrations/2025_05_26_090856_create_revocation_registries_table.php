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
        Schema::create('revocation_registries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credential_id')->constrained()->onDelete('cascade');
            $table->string('revocation_transaction_hash', 66)->nullable();
            $table->string('reason')->default('revoked');
            $table->timestamp('revoked_at');
            $table->string('revoked_by')->nullable(); // User who revoked
            $table->string('blockchain', 20)->default('ethereum');
            $table->string('network', 20)->default('sepolia');
            $table->bigInteger('block_number')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->integer('confirmation_count')->default(0);
            $table->text('error_message')->nullable();
            $table->json('revocation_data')->nullable();
            $table->timestamps();
            
            $table->index(['credential_id', 'status']);
            $table->index(['revocation_transaction_hash']);
            $table->index(['revoked_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revocation_registries');
    }
};
