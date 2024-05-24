<?php

namespace App\Models;

use App\Enums\FoEnum;
use App\Enums\FoStatusEnum;
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
        'status',
        'final_judgment_reason',
        'paid',
    ];

    protected function casts(): array
    {
        return [
            'status' => FoStatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requester(): Attribute
    {
        $user = User::firstWhere('id', $this->user_id);
        $military = Military::firstWhere('name', $user->name);
        $formatted_string = '0' . substr((string)$military->rg, 0, 1) . '.' . substr((string)$military->rg, 1);

        return Attribute::make(
            get: fn() => $military->rank . ' ' . $military->division . ' ' . $formatted_string . ' ' . $military->name
        );
    }
}
