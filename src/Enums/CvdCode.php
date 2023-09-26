<?php

namespace CraigPaul\Moneris\Enums;

/**
 * An enumeration of CVD codes and their meanings.
 *
 * From the Moneris documentation:
 *
 * The Card Validation Digits (CVD) value is an additional number printed on
 * credit cards that is used as an additional check when verifying cardholder
 * credentials during a transaction.
 *
 * The response that is received from CVD verification is intended to provide
 * added security and fraud prevention, but the response itself does not affect
 * the completion of a transaction. Upon receiving a response, the choice
 * whether to proceed with a transaction is left entirely to the merchant.
 *
 * The response is not a strict guideline of which transaction will approve
 * or decline.
 *
 * @see https://developer.moneris.com/More/Testing/CVD%20Result%20Codes
 */
enum CvdCode: string
{
    case M = 'M';
    case N = 'N';
    case P = 'P';
    case S = 'S';
    case U = 'U';
    case Y = 'Y';
    case D = 'D';
    case Other = 'Other';
}
