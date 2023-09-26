<?php

namespace CraigPaul\Moneris\TestSupport\Cases;

use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\Transactable\VerificationData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\Response;
use Ramsey\Uuid\Uuid;

class VaultTestCase extends TestCase
{
    protected function verifyCard(CardInterface $testCard): Response
    {
        $response = $this->gateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $testCard,
                cvdData: new CvdData('123'),
                cofData: new CofVerificationData(),
            ),
        );

        $this->assert->isSuccessful($response);

        // Cards that don't support CoF (Amex) will fail if we don't do this.
        if ($testCard->supportsCof()) {
            $this->assert->hasIssuerId($response);
        }

        return $response;
    }

    protected function params(array $with = [], string $pre = ''): array
    {
        return array_merge(
            [
                'order_id' => $pre
                    ? sprintf('%s-%s', $pre, Uuid::uuid4())
                    : Uuid::uuid4(),
                'amount' => '1.00',
            ],
            $with,
        );
    }
}
