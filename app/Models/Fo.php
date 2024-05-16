<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FoEnum;

class Fo extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'issuer',
        'reason',
        'excuse',
        'status',
        'final_judgment_reason',
        'paid',
        'date_issued',
    ];

    protected function casts(): array
    {
        return [
            'status' => FoEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Military::class, 'issuer');
    }
}
