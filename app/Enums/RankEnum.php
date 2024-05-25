<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RankEnum: string implements HasLabel
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
    case MAJOR = 'Major';
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
            self::MAJOR => 'Major',
            self::TC => 'TC',
            self::CEL => 'Cel',
        };
    }
}
