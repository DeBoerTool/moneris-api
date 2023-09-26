<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\Transactable\AddCardData;
use CraigPaul\Moneris\Data\Transactable\VerificationData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class VaultAddTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function adding_a_card(CardInterface $testCard): void
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

        $response = $this->gateway()->getVault()->add($addCardData);

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasVaultKey($response);
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function adding_a_card_with_customer_data(CardInterface $testCard): void
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
        $customer = $this->fixtures->cardCustomerData();

        $response = $this->gateway()->getVault()->add(
            new AddCardData(
                cardData: new CardData($testCard, $customer),
                cofData: new CofAddOrUpdateCardData($issuerId),
            ),
        );

        $this->assert->isSuccessful($response);
        $this->assert->hasVaultKey($response);
        $this->assert->isComplete($response);
        $this->assert->hasCardCustomer($response, $customer);
    }

    /**
     * @test
     *
     * @dataProvider avsCardProvider
     */
    public function adding_a_card_with_avs(CardInterface $testCard): void
    {
        $verifyResponse = $this->avsGateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $testCard,
                cvdData: new CvdData('123'),
                avsData: $this->fixtures->avsData(),
                cofData: new CofVerificationData(),
            ),
        );

        if (!$testCard->supportsCof()) {
            $this->assert->isSuccessful($verifyResponse);
            $this->assert->hasAvsResult($verifyResponse, AvsCode::Y);

            $issuerId = 'null';
        } else {
            $this->assert->hasIssuerId($verifyResponse);
            $issuerId = $verifyResponse->getReceipt()->getIssuerId();
        }

        $avsData = $this->fixtures->avsData();

        $addData = new AddCardData(
            cardData: new CardData($testCard),
            cofData: new CofAddOrUpdateCardData($issuerId),
            cvdData: new CvdData('123'),
            avsData: $avsData,
        );

        $addResponse = $this->avsGateway()->getVault()->add($addData);

        $this->assert->isSuccessful($addResponse);
        $this->assert->isComplete($addResponse);
        $this->assert->hasVaultKey($addResponse);
        $this->assert->hasAvsData($addResponse, $avsData);
    }
}
