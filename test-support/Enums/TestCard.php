<?php

namespace CraigPaul\Moneris\TestSupport\Enums;

use CraigPaul\Moneris\Cards\Amex;
use CraigPaul\Moneris\Cards\Discover;
use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\MasterCard;
use CraigPaul\Moneris\Cards\Visa;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Interfaces\CardInterface;

enum TestCard: string
{
    case MasterCard = '5454545454545454';
    case Visa = '4242424242424242';
    case Amex = '373599005095005';
    case Discover = '6011000992927602';

    public static function getCards(Expiry|string|null $expiry = null): array
    {
        $expiry = $expiry ?? new Expiry('2512');

        return [
            new MasterCard(self::MasterCard->value, $expiry),
            new Visa(self::Visa->value, $expiry),
            new Amex(self::Amex->value, $expiry),
            new Discover(self::Discover->value, $expiry),
        ];
    }

    public function toCard(Expiry $expiry): CardInterface
    {
        return match ($this->name) {
            'MasterCard' => new MasterCard($this->value, $expiry),
            'Visa' => new Visa($this->value, $expiry),
            'Amex' => new Amex($this->value, $expiry),
            'Discover' => new Discover($this->value, $expiry),
        };
    }

    public function getCardType(): CardType
    {
        return match ($this->name) {
            'MasterCard' => CardType::MasterCard,
            'Visa' => CardType::Visa,
            'Amex' => CardType::AmericanExpress,
            'Discover' => CardType::Discover,
        };
    }
}
