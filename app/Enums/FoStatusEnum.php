<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum FoStatusEnum: string implements HasLabel, HasColor
{
    case EM_ANDAMENTO = 'Em andamento';
    case DEFERIDO = 'Deferido';
    case INDEFERIDO = 'Indeferido';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'warning',
            self::DEFERIDO => 'success',
            self::INDEFERIDO => 'danger',
        };

    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'heroicon-o-exclamation-triangle',
            self::DEFERIDO => 'heroicon-o-x-circle',
            self::INDEFERIDO => 'heroicon-o-check-badge',
        };
    }
}