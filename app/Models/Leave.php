<?php

namespace App\Models;

use App\Enums\FoEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_leave',
        'date_back',
        'motive',
        'missed_classes',
        'user_id',
        'accept_terms',
        'file',
        'status',
        'final_judgment_reason',
        'paid',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
