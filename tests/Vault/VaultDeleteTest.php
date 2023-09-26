<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\Transactable\AddCardData;
use CraigPaul\Moneris\Data\Transactable\DeleteCardData;
use CraigPaul\Moneris\Data\Transactable\VerificationData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class VaultDeleteTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function deleting_a_card(CardInterface $testCard): void
    {
        $verifyResponse = $this->gateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $testCard,
                cvdData: new CvdData('123'),
                cofData: new CofVerificationData(),
            ),
        );

        $this->assert->verificationSuccess($testCard, $verifyResponse);
        $issuerId = $verifyResponse->getReceipt()->getIssuerId() ?? 'null';

        $addCardData = new AddCardData(
            cardData: new CardData($testCard),
            cofData: new CofAddOrUpdateCardData($issuerId),
        );

        $addResponse = $this->gateway()->getVault()->add($addCardData);
        $dataKey = $addResponse->getReceipt()?->read('key');

        $this->assert->isSuccessful($addResponse);
        $this->assert->isComplete($addResponse);
        $this->assert->hasVaultKey($addResponse);

        $deleteCardData = new DeleteCardData(dataKey: $dataKey);

        $deleteResponse = $this->gateway()->getVault()->delete($deleteCardData);

        $this->assert->isSuccessful($deleteResponse);
        $this->assert->isComplete($deleteResponse);
    }
}
