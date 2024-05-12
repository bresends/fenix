<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Military extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'email',
        'rank',
        'division',
        'blood_type',
        'tel',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fosIssued(): HasMany
    {
        return $this->hasMany(Fo::class, 'issuer');
    }
}
