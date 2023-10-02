<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Transactables\AddCard;
use CraigPaul\Moneris\Transactables\DeleteCard;
use CraigPaul\Moneris\Transactables\VerifyCard;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class DeleteCardTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function deleting_a_card(CardInterface $testCard): void
    {
        $verifyBeforeAdd = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $issuerId = $this->submit($verifyBeforeAdd)
            ->isSuccessful()
            ->isComplete()
            ->hasIssuerId($testCard)
            ->getResponse()
            ->getIssuerId();

        $addCard = new AddCard(
            creditCard: $testCard,
            cofData: new CofAddOrUpdateCardData($issuerId),
        );

        $dataKey = $this->submit($addCard)
            ->isSuccessful()
            ->isComplete()
            ->hasDataKey()
            ->getResponse()
            ->getDataKey();

        $deleteCard = new DeleteCard(dataKey: $dataKey);

        $this->submit($deleteCard)
            ->isSuccessful()
            ->isComplete();
    }
}
