<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployerVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_user_id',
        'credential_id',
        'searched_at',
        'ip_address',
    ];

    protected $casts = [
        'searched_at' => 'datetime',
    ];

    /**
     * Get the employer user who performed this verification.
     */
    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_user_id');
    }

    /**
     * Get the credential that was verified.
     */
    public function credential()
    {
        return $this->belongsTo(Credential::class);
    }
}
