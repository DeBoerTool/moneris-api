<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofPurchaseData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\Transactable\AddCardData;
use CraigPaul\Moneris\Data\Transactable\VaultPurchaseData;
use CraigPaul\Moneris\Data\Transactable\VerificationData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class VaultPurchaseTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function purchasing(CardInterface $testCard): void
    {
        $verifyResponse = $this->gateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $testCard,
                cvdData: $this->fixtures->cvdData(),
                cofData: new CofVerificationData(),
            ),
        );

        $this->assert->verificationSuccess($testCard, $verifyResponse);

        $issuerId = $testCard->supportsCof()
            ? $verifyResponse->getReceipt()->getIssuerId()
            : 'null';

        $response = $this->gateway()->getVault()->add(new AddCardData(
            cardData: new CardData($testCard),
            cofData: new CofAddOrUpdateCardData($issuerId),
        ));

        $dataKey = $response->getReceipt()->getDataKey();

        $data = new VaultPurchaseData(
            dataKey: $dataKey,
            orderId: $this->orderId(),
            amount: $this->approvedAmount(),
            cofData: new CofPurchaseData($issuerId),
            cvdData: $this->fixtures->cvdData(),
        );

        $response = $this->gateway()->purchase($data);

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->isApproved($response);
    }
}
