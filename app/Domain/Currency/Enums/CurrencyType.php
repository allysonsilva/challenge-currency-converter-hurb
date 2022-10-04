<?php

namespace CurrencyDomain\Enums;

enum CurrencyType: string
{
    case FIAT = 'fiat';
    case CRYPTO = 'crypto';
    case FICTITIOUS = 'fictitious';

    public function render(): string
    {
        return match ($this) {
            self::FIAT => 'Fiat',
            self::CRYPTO => 'Crypto',
            self::FICTITIOUS => 'Fictitious',
        };
    }
}
