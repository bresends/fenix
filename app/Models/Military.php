<?php

namespace App\Models;

use App\Enums\DivisionEnum;
use App\Enums\PlatoonEnum;
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
        'sort',
    ];

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
        if ($this->rg < 10000) {
            $formatted_rg = '0' . substr((string)$this->rg, 0, 1) . '.' . substr((string)$this->rg, 1);
        } else {
            $formatted_rg = substr((string)$this->rg, 0, 2) . '.' . substr((string)$this->rg, 2);
        }

        return Attribute::make(
            get: fn() => $this->rank->value . ' ' . $this->division->value . ' ' . $formatted_rg . ' ' . $this->name
        );
    }

    public function platoon(): Attribute
    {
        return Attribute::make(
            get: function () {
                $user = User::where('name', $this->name)->first();
                return $user ? $user->platoon : PlatoonEnum::ADMINISTRACAO->value;
            }
        );
    }

    protected function casts(): array
    {
        return [
            'rank' => RankEnum::class,
            'division' => DivisionEnum::class,
        ];
    }
}
