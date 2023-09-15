<?php

namespace CraigPaul\Moneris\Enums;

enum ResponseErrorEnum: int
{
    case InvalidTransactionData = 0;
    case FailedAttempt = -1;
    case CreateTransactionRecord = -2;
    case GlobalErrorReceipt = -3;
    case CvdGeneric = -4;
    case CvdNoMatch = -5;

    case CvdNotProcessed = -6;

    case CvdMissing = -7;

    case CvdNotSupported = -8;

    case AvsGeneric = -9;

    case AvsPostalCode = -10;

    case AvsAddress = -11;

    case AvsNoMatch = -12;

    case AvsTimeout = -13;
    case SystemUnavailable = -14;

    case CardExpired = -15;

    case InvalidCard = -16;

    case InsufficientFunds = -17;

    case PreauthFull = -18;

    case DuplicateTransaction = -19;

    case Declined = -20;

    case NotAuthorized = -21;

    case InvalidExpiryDate = -22;
    case GenericError = -23;

    public static function fromReceiptCode(string|null $code): self
    {
        return match ($code) {
            '050', '074', 'null' => self::SystemUnavailable,
            '051', '482', '484' => self::CardExpired,
            '075' => self::InvalidCard,
            '208', '475' => self::InvalidExpiryDate,
            '076', '079', '080', '081', '082', '083' => self::InsufficientFunds,
            '077' => self::PreauthFull,
            '078' => self::DuplicateTransaction,
            '481', '483' => self::Declined,
            '485' => self::NotAuthorized,
            '486', '487', '489', '490' => self::CvdGeneric,
            default => self::GenericError,
        };
    }

    public static function fromAvsCode(string $code): self
    {
        return match ($code) {
            'B', 'C' => self::AvsPostalCode,
            'G', 'I', 'P', 'S', 'U', 'Z' => self::AvsAddress,
            'N' => self::AvsNoMatch,
            'R' => self::AvsTimeout,
            default => self::AvsGeneric,
        };
    }

    public function isAvs(): bool
    {
        return in_array($this->value, [-9, -10, -11, -12, -13]);
    }

    public function isCvd(): bool
    {
        return in_array($this->value, [-9, -10, -11, -12, -13]);
    }
}
