<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevocationRegistry extends Model
{
    use HasFactory;

    protected $fillable = [
        'credential_id',
        'revocation_transaction_hash',
        'reason',
        'revoked_at',
        'revoked_by',
        'blockchain',
        'network',
        'block_number',
        'status',
        'confirmation_count',
        'error_message',
        'revocation_data',
    ];

    protected $casts = [
        'revoked_at' => 'datetime',
        'revocation_data' => 'array',
        'confirmation_count' => 'integer',
        'block_number' => 'integer',
    ];

    /**
     * Get the credential that was revoked
     */
    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class);
    }

    /**
     * Check if the revocation is confirmed on blockchain
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed' && 
               $this->confirmation_count >= config('blockchain.confirmation_blocks', 12);
    }

    /**
     * Check if the revocation is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the revocation failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get the blockchain explorer URL for this revocation transaction
     */
    public function getExplorerUrl(): ?string
    {
        if (!$this->revocation_transaction_hash) {
            return null;
        }

        $baseUrls = [
            'mainnet' => 'https://etherscan.io/tx/',
            'sepolia' => 'https://sepolia.etherscan.io/tx/',
            'goerli' => 'https://goerli.etherscan.io/tx/',
        ];

        $baseUrl = $baseUrls[$this->network] ?? $baseUrls['sepolia'];
        
        return $baseUrl . $this->revocation_transaction_hash;
    }

    /**
     * Mark revocation as confirmed
     */
    public function markAsConfirmed(int $blockNumber = null, int $confirmationCount = null): void
    {
        $this->update([
            'status' => 'confirmed',
            'block_number' => $blockNumber ?? $this->block_number,
            'confirmation_count' => $confirmationCount ?? config('blockchain.confirmation_blocks', 12),
        ]);
    }

    /**
     * Mark revocation as failed
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Increment confirmation count
     */
    public function incrementConfirmations(): void
    {
        $this->increment('confirmation_count');
        
        if ($this->confirmation_count >= config('blockchain.confirmation_blocks', 12)) {
            $this->markAsConfirmed();
        }
    }

    /**
     * Scope for confirmed revocations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for pending revocations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed revocations
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
