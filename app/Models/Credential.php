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
        'status',
    ];

    protected $casts = [
        'issued_on' => 'date',
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
}
