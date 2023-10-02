<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Transactables\AddCard;
use CraigPaul\Moneris\Transactables\VerifyCard;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class AddCardTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function adding_a_card(CardInterface $testCard): void
    {
        $verify = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $issuerId = $this->submit($verify)
            ->isComplete()
            ->isSuccessful()
            ->hasIssuerId($testCard)
            ->getResponse()
            ->getIssuerId();

        $add = new AddCard(
            creditCard: $testCard,
            cofData: new CofAddOrUpdateCardData($issuerId),
        );

        $this->submit($add)
            ->isComplete()
            ->isSuccessful()
            ->hasDataKey();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function adding_a_card_with_customer_data(CardInterface $testCard): void
    {
        $verify = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $verifyResponse = $verify->submit($this->http(), $this->credentials());
        $this->assert->response($verifyResponse)->hasIssuerId($testCard);
        $issuerId = $verifyResponse->getIssuerId() ?? '';
        $cardCustomerData = $this->fixtures->cardCustomerData();

        $add = new AddCard(
            creditCard: $testCard,
            cofData: new CofAddOrUpdateCardData($issuerId),
            cardCustomerData: $cardCustomerData,
        );

        $this->assert
            ->response($add->submit($this->http(), $this->credentials()))
            ->isSuccessful()
            ->isComplete()
            ->hasDataKey()
            ->hasCardCustomerData($cardCustomerData);
    }

    /**
     * @test
     *
     * @dataProvider avsCardProvider
     */
    public function adding_a_card_with_avs(CardInterface $testCard): void
    {
        $verify = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            cvdData: $this->fixtures->cvdData(),
            avsData: $this->fixtures->avsData(),
            cofData: new CofVerificationData(),
        );

        $verifyResponse = $verify->submit($this->http(), $this->credentials());

        $this->assert->response($verifyResponse)
            ->isSuccessful()
            ->isComplete()
            ->hasAvsResult(AvsCode::Y)
            ->hasIssuerId($testCard);

        $issuerId = $verifyResponse->getIssuerId() ?? '';

        $avsData = $this->fixtures->avsData();

        $add = new AddCard(
            creditCard: $testCard,
            cofData: new CofAddOrUpdateCardData($issuerId),
            cvdData: $this->fixtures->cvdData(),
            avsData: $avsData,
        );

        $this->assert
            ->response($add->submit($this->http(), $this->credentials()))
            ->isSuccessful()
            ->isComplete()
            ->hasDataKey()
            ->hasAvsData($avsData);
    }
}
