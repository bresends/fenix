<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusExamEnum: string implements HasLabel, HasColor, HasIcon
{
    case EM_ANDAMENTO = 'Em andamento';
    case ENCAMINHAMENTO_DEFERIDO = 'Encaminhamento deferido';
    case ENCAMINHAMENTO_INDEFERIDO = 'Encaminhamento indeferido';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'Em andamento',
            self::ENCAMINHAMENTO_DEFERIDO => 'Encaminhamento deferido',
            self::ENCAMINHAMENTO_INDEFERIDO => 'Encaminhamento indeferido',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'warning',
            self::ENCAMINHAMENTO_DEFERIDO => 'success',
            self::ENCAMINHAMENTO_INDEFERIDO => 'danger',
        };

    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'heroicon-s-exclamation-triangle',
            self::ENCAMINHAMENTO_DEFERIDO => 'heroicon-s-check-badge',
            self::ENCAMINHAMENTO_INDEFERIDO => 'heroicon-s-x-circle',
        };

    }
}
