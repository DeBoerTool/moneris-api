<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace CraigPaul\Moneris\TestSupport\Verification;

use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use Exception;

abstract class CardSourceAbstract
{
    abstract protected function cards(): array;

    public static function getApprovedMatch(
        CvdCode|null $cvd = null,
        AvsCode|null $avs = null,
    ): string {
        return self::getMatch(
            approved: true,
            cvd: $cvd,
            avs: $avs,
        );
    }

    public static function getDeclinedMatch(
        CvdCode|null $cvd = null,
        AvsCode|null $avs = null,
    ): string {
        return self::getMatch(
            approved: false,
            cvd: $cvd,
            avs: $avs,
        );
    }

    public static function getMatch(
        bool $approved,
        CvdCode|null $cvd = null,
        AvsCode|null $avs = null,
    ): string {
        foreach ((new static())->cards() as $card) {
            if (!self::isMatch($card, $approved, $cvd, $avs)) {
                continue;
            }

            return $card[0];
        }

        throw new Exception('No matching card found');
    }

    protected static function isMatch(
        array $card,
        bool $approved,
        CvdCode|null $cvd,
        AvsCode|null $avs,
    ): bool {
        return self::matchesApproved($card, $approved)
            && self::matchesCvd($card, $cvd)
            && self::matchesAvs($card, $avs);
    }

    protected static function matchesApproved(
        array $card,
        bool $approved,
    ): bool {
        return str_contains(
            $card[1],
            $approved
                ? 'Approved'
                : 'Declined'
        );
    }

    protected static function matchesAvs(
        array $card,
        AvsCode|null $avsCode,
    ): bool {
        return is_null($avsCode) || $card[2] === $avsCode->name;
    }

    protected static function matchesCvd(
        array $card,
        CvdCode|null $cvdCode,
    ): bool {
        return is_null($cvdCode) || $card[3] === $cvdCode->name;
    }
}
