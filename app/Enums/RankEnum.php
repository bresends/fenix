<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RankEnum: string implements HasColor, HasLabel
{
    case AL_SD = 'Al Sd';
    case SD_2_CLASSE = 'Sd 2ª Classe';
    case SD_1_CLASSE = 'Sd 1ª Classe';
    case CB = 'Cb';
    case PRIMEIRO_SGT = '1º Sgt';
    case SEGUNDO_SGT = '2º Sgt';
    case TERCEIRO_SGT = '3º Sgt';
    case ST = 'ST';
    case AL_OF_ADM = 'Al Of Adm';
    case CAD = 'Cad';
    case ASP_OF = 'Asp Of';
    case SEGUNDO_TEN = '2º Ten';
    case PRIMEIRO_TEN = '1º Ten';
    case CAP = 'Cap';
    case MAJOR = 'Maj';
    case TC = 'TC';
    case CEL = 'Cel';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AL_SD => 'Al Sd',
            self::SD_2_CLASSE => 'Sd 2ª Classe',
            self::SD_1_CLASSE => 'Sd 1ª Classe',
            self::CB => 'Cb',
            self::PRIMEIRO_SGT => '1º Sgt',
            self::SEGUNDO_SGT => '2º Sgt',
            self::TERCEIRO_SGT => '3º Sgt',
            self::ST => 'ST',
            self::AL_OF_ADM => 'Al Of Adm',
            self::CAD => 'Cad',
            self::ASP_OF => 'Asp Of',
            self::SEGUNDO_TEN => '2º Ten',
            self::PRIMEIRO_TEN => '1º Ten',
            self::CAP => 'Cap',
            self::MAJOR => 'Maj',
            self::TC => 'TC',
            self::CEL => 'Cel',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AL_SD, self::SD_1_CLASSE, self::SD_2_CLASSE, self::CB => 'success',
            self::PRIMEIRO_SGT, self::SEGUNDO_SGT, self::TERCEIRO_SGT, self::ST, self::AL_OF_ADM, self::CAD, self::ASP_OF => 'info',
            self::SEGUNDO_TEN, self::PRIMEIRO_TEN, self::CAP => 'warning',
            self::MAJOR, self::TC, self::CEL => 'danger',
        };

    }
}
