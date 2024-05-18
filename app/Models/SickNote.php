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
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dayBack(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->date_issued)->addDays($this->days_absent)->toDateString(),
        );
    }

    public function userRank(): Attribute
    {
        return Attribute::make(
            get: function () {
                $user = $this->user;
                if ($user && $user->rg) {
                    return Military::where('id', $user->rg)->first()?->rank;
                }

                return null;
            },
        );
    }
}
