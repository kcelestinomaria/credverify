<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Credential extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institution_id',
        'full_name',
        'credential_type',
        'issued_by',
        'issued_on',
        'credential_file_path',
        'hash',
        'verification_code',
        'json_path',
        'qr_code_path',
        'batch_id',
        'merkle_proof',
        'digital_signature',
        'signature_algorithm',
        'signed_at',
        'blockchain_anchored',
        'anchored_at',
        'blockcerts_metadata',
        'status',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'merkle_proof' => 'array',
        'signed_at' => 'datetime',
        'blockchain_anchored' => 'boolean',
        'anchored_at' => 'datetime',
        'blockcerts_metadata' => 'array',
    ];

    /**
     * Get the user who issued this credential.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the institution that issued this credential.
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the blockchain anchor for this credential's batch
     */
    public function blockchainAnchor()
    {
        return $this->belongsTo(BlockchainAnchor::class, 'batch_id', 'batch_id');
    }

    /**
     * Get the revocation registry entry for this credential
     */
    public function revocationRegistry()
    {
        return $this->hasOne(RevocationRegistry::class);
    }

    /**
     * Get the employer verifications for this credential.
     */
    public function employerVerifications()
    {
        return $this->hasMany(EmployerVerification::class);
    }

    /**
     * Check if the credential is verified.
     */
    public function isVerified()
    {
        return $this->status === 'Verified';
    }

    /**
     * Check if the credential is revoked.
     */
    public function isRevoked()
    {
        return $this->status === 'Revoked';
    }

    /**
     * Generate a unique verification code.
     */
    public static function generateVerificationCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Check if credential is digitally signed
     */
    public function isSigned()
    {
        return !empty($this->digital_signature) && !empty($this->signed_at);
    }

    /**
     * Check if credential is anchored to blockchain
     */
    public function isBlockchainAnchored()
    {
        return $this->blockchain_anchored && !empty($this->anchored_at);
    }

    /**
     * Check if credential has Merkle proof
     */
    public function hasMerkleProof()
    {
        return !empty($this->merkle_proof);
    }

    /**
     * Check if credential is fully Blockcerts compliant
     */
    public function isBlockcertsCompliant()
    {
        return $this->isSigned() && 
               $this->isBlockchainAnchored() && 
               $this->hasMerkleProof() &&
               !empty($this->blockcerts_metadata);
    }

    /**
     * Check if credential is revoked on blockchain
     */
    public function isRevokedOnBlockchain()
    {
        $revocation = $this->revocationRegistry;
        return $revocation && $revocation->isConfirmed();
    }

    /**
     * Get blockchain verification status
     */
    public function getBlockchainStatus()
    {
        if ($this->isRevokedOnBlockchain()) {
            return 'revoked';
        }

        if (!$this->isBlockchainAnchored()) {
            return 'not_anchored';
        }

        $anchor = $this->blockchainAnchor;
        if (!$anchor) {
            return 'anchor_missing';
        }

        if ($anchor->isPending()) {
            return 'pending';
        }

        if ($anchor->isFailed()) {
            return 'failed';
        }

        if ($anchor->isConfirmed()) {
            return 'confirmed';
        }

        return 'unknown';
    }
}
