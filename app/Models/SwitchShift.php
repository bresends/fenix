<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwitchShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'first_shift_date',
        'first_shift_place',
        'first_shift_paying_military',
        'first_shift_receiving_military',
        'second_shift_date',
        'second_shift_place',
        'second_shift_paying_military',
        'second_shift_receiving_military',
        'motive',
        'file',
        'accepted',
        'status',
        'final_judgment_reason',
        'paid',
        'evaluated_by',
        'evaluated_at',
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

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
