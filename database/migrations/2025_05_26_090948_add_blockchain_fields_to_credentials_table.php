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
        Schema::table('credentials', function (Blueprint $table) {
            $table->string('batch_id')->nullable()->after('verification_code');
            $table->json('merkle_proof')->nullable()->after('batch_id');
            $table->text('digital_signature')->nullable()->after('merkle_proof');
            $table->string('signature_algorithm', 20)->default('RS256')->after('digital_signature');
            $table->timestamp('signed_at')->nullable()->after('signature_algorithm');
            $table->boolean('blockchain_anchored')->default(false)->after('signed_at');
            $table->timestamp('anchored_at')->nullable()->after('blockchain_anchored');
            $table->json('blockcerts_metadata')->nullable()->after('anchored_at');
            
            $table->index(['batch_id']);
            $table->index(['blockchain_anchored']);
            $table->index(['anchored_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropIndex(['blockchain_anchored']);
            $table->dropIndex(['anchored_at']);
            
            $table->dropColumn([
                'batch_id',
                'merkle_proof',
                'digital_signature',
                'signature_algorithm',
                'signed_at',
                'blockchain_anchored',
                'anchored_at',
                'blockcerts_metadata'
            ]);
        });
    }
};
