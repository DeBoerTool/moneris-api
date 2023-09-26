<?php

namespace CraigPaul\Moneris\Enums;

/**
 * An enumeration of AVS codes and their meanings.
 *
 * From the Moneris documentation:
 *
 * The responses that are received from AVS verifications are intended to
 * provide added security and fraud prevention, but the response itself will
 * not affect the issuer’s approval of a transaction. Upon receiving a
 * response, the choice to proceed with a transaction is left entirely to the
 * merchant.
 *
 * Please note that all responses coming back from these verification methods
 * are not direct indicators of whether a merchant should complete any
 * particular transaction. The responses should not be used as a strict
 * guideline of which transaction will approve or decline.
 *
 * The Address Verification Service (AVS) value refers to the cardholder’s
 * street number, street name and zip/postal code as it would appear on their
 * statement.
 *
 * @see https://developer.moneris.com/More/Testing/AVS%20Result%20Codes
 */
enum AvsCode: string
{
    case A = 'A';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case K = 'K';
    case L = 'L';
    case M = 'M';
    case N = 'N';
    case O = 'O';
    case R = 'R';
    case S = 'S';
    case T = 'T';
    case U = 'U';
    case W = 'W';
    case X = 'X';
    case Y = 'Y';
    case Z = 'Z';
}
