<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SickNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'file',
        'date_issued',
        'days_absent',
        'motive',
        'restrictions',
        'archived',
        'received',
        'observation',
        'user_id',
        'csau',
        'ratified',
        'evaluated_by',
        'evaluated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dayBack(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->date_issued)->addDays($this->days_absent)->toDateString(),
        );
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
