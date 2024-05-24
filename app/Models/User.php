<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\PlatoonEnum;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;
    use HasPanelShield, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'platoon',
        'rg',
        'blood_type',
        'phone_number',
        'address',
        'vehicle_type',
        'vehicle_model',
        'vehicle_brand',
        'vehicle_color',
        'vehicle_licence_plate',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone_number',
        'emergency_contact_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function military(): HasOne
    {
        return $this->hasOne(Military::class);
    }

    public function sickNotes(): HasMany
    {
        return $this->hasMany(SickNote::class);
    }

    public function fos(): HasMany
    {
        return $this->hasMany(Fo::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'platoon' => PlatoonEnum::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
