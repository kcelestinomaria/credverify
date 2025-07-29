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
        Schema::create('blockchain_anchors', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique();
            $table->string('merkle_root', 64);
            $table->string('transaction_hash', 66)->nullable();
            $table->string('blockchain', 20)->default('ethereum');
            $table->string('network', 20)->default('sepolia');
            $table->bigInteger('block_number')->nullable();
            $table->timestamp('anchored_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->json('transaction_data')->nullable();
            $table->integer('confirmation_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['batch_id', 'status']);
            $table->index(['merkle_root']);
            $table->index(['transaction_hash']);
            $table->index(['anchored_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockchain_anchors');
    }
};
