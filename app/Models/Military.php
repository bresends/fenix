<?php

namespace App\Models;

use App\Enums\DivisionEnum;
use App\Enums\RankEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Military extends Model
{
    use HasFactory;

    protected $fillable = [
        'rg',
        'name',
        'rank',
        'division',
    ];

    protected function casts(): array
    {
        return [
            'rank' => RankEnum::class,
            'division' => DivisionEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fosIssued(): HasMany
    {
        return $this->hasMany(Fo::class, 'issuer');
    }

    public function sei(): Attribute
    {
        $formatted_string = '0'.substr((string) $this->rg, 0, 1).'.'.substr((string) $this->rg, 1);

        return Attribute::make(
            get: fn () => $this->rank->value.' '.$this->division->value.' '.$formatted_string.' '.$this->name
        );
    }
}
