<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusFoEnum: string implements HasLabel, HasColor, HasIcon
{
    case EM_ANDAMENTO = 'Em andamento';
    case DEFERIDO = 'Deferido';
    case INDEFERIDO = 'Indeferido';
    case ABONADO = 'Abonado';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'Em andamento',
            self::DEFERIDO => 'Deferido',
            self::INDEFERIDO => 'Indeferido',
            self::ABONADO => 'Abonado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'warning',
            self::DEFERIDO => 'success',
            self::INDEFERIDO => 'danger',
            self::ABONADO => 'info',
        };

    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'heroicon-s-exclamation-triangle',
            self::DEFERIDO => 'heroicon-s-check-badge',
            self::INDEFERIDO => 'heroicon-s-x-circle',
            self::ABONADO => 'heroicon-s-star',
        };

    }
}
