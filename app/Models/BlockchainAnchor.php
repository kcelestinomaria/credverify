<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockchainAnchor extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'merkle_root',
        'transaction_hash',
        'blockchain',
        'network',
        'block_number',
        'anchored_at',
        'status',
        'transaction_data',
        'confirmation_count',
        'error_message',
    ];

    protected $casts = [
        'anchored_at' => 'datetime',
        'transaction_data' => 'array',
        'confirmation_count' => 'integer',
        'block_number' => 'integer',
    ];

    /**
     * Get the credentials associated with this blockchain anchor
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class, 'batch_id', 'batch_id');
    }

    /**
     * Check if the anchor is confirmed on blockchain
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed' && 
               $this->confirmation_count >= config('blockchain.confirmation_blocks', 12);
    }

    /**
     * Check if the anchor is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the anchor failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get the blockchain explorer URL for this transaction
     */
    public function getExplorerUrl(): ?string
    {
        if (!$this->transaction_hash) {
            return null;
        }

        $baseUrls = [
            'mainnet' => 'https://etherscan.io/tx/',
            'sepolia' => 'https://sepolia.etherscan.io/tx/',
            'goerli' => 'https://goerli.etherscan.io/tx/',
        ];

        $baseUrl = $baseUrls[$this->network] ?? $baseUrls['sepolia'];
        
        return $baseUrl . $this->transaction_hash;
    }

    /**
     * Mark as confirmed
     */
    public function markAsConfirmed(int $blockNumber = null, int $confirmationCount = null): void
    {
        $this->update([
            'status' => 'confirmed',
            'block_number' => $blockNumber ?? $this->block_number,
            'confirmation_count' => $confirmationCount ?? config('blockchain.confirmation_blocks', 12),
            'anchored_at' => $this->anchored_at ?? now(),
        ]);
    }

    /**
     * Mark as failed
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
     * Scope for confirmed anchors
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for pending anchors
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed anchors
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
