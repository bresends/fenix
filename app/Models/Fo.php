<?php

namespace App\Models;

use App\Enums\StatusFoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FoEnum;
use App\Enums\StatusEnum;

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
        'observation',
        'final_judgment_reason',
        'paid',
        'date_issued',
        'evaluated_by',
        'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => FoEnum::class,
            'status' => StatusFoEnum::class,
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

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
