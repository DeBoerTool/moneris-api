<?php

namespace CraigPaul\Moneris\Sources;

use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Values\CvdResult;
use InvalidArgumentException;

class CvdResultSource
{
    public static function resolve(CvdCode $cvdCode): CvdResult
    {
        foreach ((new self())->data() as $result) {
            if ($result['code'] === $cvdCode->value) {
                return new CvdResult(
                    cvdCode: $cvdCode,
                    message: $result['message'],
                );
            }
        }

        throw new InvalidArgumentException('No CVD result found.');
    }

    public static function data(): array
    {
        return [
            [
                'code' => 'M',
                'message' => 'Match',
            ],
            [
                'code' => 'N',
                'message' => 'No Match',
            ],
            [
                'code' => 'P',
                'message' => 'Not Processed',
            ],
            [
                'code' => 'S',
                'message' => 'CVD should be on the card, but Merchant has indicated that CVD is not present.',
            ],
            [
                'code' => 'U',
                'message' => 'Issuer is not a CVD participant',
            ],
            [
                'code' => 'Y',
                'message' => 'Match for AmEx/JCB only',
            ],
            [
                'code' => 'D',
                'message' => 'Invalid security code for AmEx/JCB',
            ],
            [
                'code' => 'Other',
                'message' => 'Invalid response code',
            ],
        ];
    }
}
