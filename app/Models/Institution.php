<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_email',
        'slug',
        'logo_url',
        'description',
    ];

    /**
     * Get the users that belong to this institution.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the credentials issued by this institution.
     */
    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
