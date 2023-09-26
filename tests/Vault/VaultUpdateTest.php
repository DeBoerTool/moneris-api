<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Cards\Visa;
use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\Transactable\AddCardData;
use CraigPaul\Moneris\Data\Transactable\PeekData;
use CraigPaul\Moneris\Data\Transactable\UpdateCardData;
use CraigPaul\Moneris\Data\Transactable\UpdateDetailsData;
use CraigPaul\Moneris\Data\Transactable\VerificationData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\TestSupport\Enums\TestCard;

class VaultUpdateTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function updating_non_card_number_data(CardInterface $testCard): void
    {
        // Card Verification //

        $verifyResponse = $this->gateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $testCard,
                cvdData: new CvdData('123'),
                cofData: new CofVerificationData(),
            ),
        );

        $this->assert->verificationSuccess($testCard, $verifyResponse);

        // Card Add //

        $addResponse = $this->gateway()->getVault()->add(
            new AddCardData(
                cardData: new CardData($testCard),
                cofData: new CofAddOrUpdateCardData(
                    $verifyResponse->getReceipt()->getIssuerId() ?? 'null'
                ),
            ),
        );

        $this->assert->isSuccessful($addResponse);
        $dataKey = $addResponse->getReceipt()?->read('key');

        // Peek Before Update //

        $beforeUpdatePeekResponse = $this->gateway()->getVault()->peek(
            new PeekData($dataKey),
        );

        // Since the card was added with no customer data or AVS data, the
        // ResolveData should include empty customer and AVS details.
        $this->assert->hasNoCardCustomer($beforeUpdatePeekResponse);
        $this->assert->hasNoAvsData($beforeUpdatePeekResponse);

        // Card Update //

        // Here we're updating the card with full customer data and a new
        // expiry. Since we're not updating the actual card number, we don't
        // have to verify the card again or send CoF data.
        $cardCustomerData = $this->fixtures->cardCustomerData();
        $avsData = $this->fixtures->avsData();
        $expiry = Expiry::fromMonthAndYear('01', '26');
        $updateData = new UpdateDetailsData(
            dataKey: $dataKey,
            cardData: new CardData(
                card: $testCard->cloneWithExpiry($expiry),
                customer: $cardCustomerData,
            ),
            avsData: $avsData,
        );

        $updateResponse = $this->gateway()->getVault()->update($updateData);

        $this->assert->isSuccessful($updateResponse);
        $this->assert->isComplete($updateResponse);
        $this->assert->hasCardCustomer($updateResponse, $cardCustomerData);
        $this->assert->hasExpiry($updateResponse, $expiry);
        $this->assert->hasAvsData($updateResponse, $avsData);

        // Peek After Update //

        $afterUpdatePeekResponse = $this->gateway()->getVault()->peek(
            new PeekData($dataKey),
        );

        // After updating the card with full customer data and a new expiry,
        // the peek ResolveData should include the updated card customer and
        // expiry.
        $this->assert->hasCardCustomer(
            $afterUpdatePeekResponse,
            $cardCustomerData,
        );
        $this->assert->hasExpiry($afterUpdatePeekResponse, $expiry);
        $this->assert->hasAvsData($afterUpdatePeekResponse, $avsData);
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function updating_card_number_only(CardInterface $testCard): void
    {
        $verifyResponse1 = $this->gateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $testCard,
                cvdData: new CvdData('123'),
                cofData: new CofVerificationData(),
            ),
        );

        $this->assert->verificationSuccess($testCard, $verifyResponse1);

        $addResponse = $this->gateway()->getVault()->add(
            new AddCardData(
                cardData: new CardData($testCard),
                cofData: new CofAddOrUpdateCardData(
                    $verifyResponse1->getReceipt()->getIssuerId() ?? 'null'
                ),
            ),
        );

        $this->assert->isSuccessful($addResponse);
        $dataKey = $addResponse->getReceipt()?->read('key');

        $updatedNumber = new Pan(TestCard::Visa->value);
        $updatedCard = new Visa((string) $updatedNumber, $testCard->getExpiry());

        $verifyResponse2 = $this->gateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $updatedCard,
                cvdData: new CvdData('123'),
                cofData: new CofVerificationData(),
            ),
        );

        $this->assert->verificationSuccess($testCard, $verifyResponse2);
        $issuerId = $verifyResponse2->getReceipt()->getIssuerId() ?? 'null';

        $updateData = new UpdateCardData(
            dataKey: $dataKey,
            cardNumber: $updatedNumber,
            cofData: new CofAddOrUpdateCardData($issuerId),
        );

        $updateResponse = $this->gateway()->getVault()->update($updateData);

        $this->assert->isSuccessful($updateResponse);
        $this->assert->isComplete($updateResponse);
        $this->assert->hasVaultKey($updateResponse, $dataKey);

        $afterUpdatePeekResponse = $this->gateway()->getVault()->peek(
            new PeekData($dataKey),
        );

        $this->assert->hasMaskedCardNumber(
            $afterUpdatePeekResponse,
            $updatedNumber,
        );
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function updating_everything(CardInterface $testCard): void
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

        $addResponse = $this->gateway()->getVault()->add(
            new AddCardData(
                cardData: new CardData($testCard),
                cofData: new CofAddOrUpdateCardData(
                    $verifyResponse->getReceipt()->getIssuerId() ?? 'null'
                ),
            ),
        );

        $this->assert->isSuccessful($addResponse);
        $dataKey = $addResponse->getReceipt()?->read('key');

        $beforeUpdatePeekResponse = $this->gateway()->getVault()->peek(
            new PeekData($dataKey),
        );

        // Since the card was added with no customer data or AVS data, the
        // ResolveData should include empty customer and AVS details.
        $this->assert->hasNoCardCustomer($beforeUpdatePeekResponse);
        $this->assert->hasNoAvsData($beforeUpdatePeekResponse);

        // Here we're updating the card with full customer data and a new
        // expiry. Since we're not updating the actual card number, we don't
        // have to verify the card again or send CoF data.
        $cardCustomerData = $this->fixtures->cardCustomerData();
        $avsData = $this->fixtures->avsData();
        $expiry = Expiry::fromMonthAndYear('01', '26');
        $updatedNumber = new Pan(TestCard::Visa->value);
        $updatedCard = new Visa((string) $updatedNumber, $testCard->getExpiry());

        $verifyResponse2 = $this->gateway()->verify(
            new VerificationData(
                orderId: $this->uid(),
                creditCard: $updatedCard,
                cvdData: new CvdData('123'),
                cofData: new CofVerificationData(),
            ),
        );

        $this->assert->verificationSuccess($testCard, $verifyResponse2);
        $issuerId = $verifyResponse2->getReceipt()->getIssuerId() ?? 'null';

        $updateData = new UpdateCardData(
            dataKey: $dataKey,
            cardNumber: $updatedNumber,
            cofData: new CofAddOrUpdateCardData($issuerId),
            expiryDate: $expiry,
            cardCustomerData: $cardCustomerData,
            avsData: $avsData,
        );

        $updateResponse = $this->gateway()->getVault()->update($updateData);

        $this->assert->isSuccessful($updateResponse);
        $this->assert->isComplete($updateResponse);
        $this->assert->hasVaultKey($updateResponse, $dataKey);
        $this->assert->hasCardCustomer($updateResponse, $cardCustomerData);
        $this->assert->hasMaskedCardNumber($updateResponse, $updatedNumber);

        $afterUpdatePeekResponse = $this->gateway()->getVault()->peek(
            new PeekData($dataKey),
        );

        // After updating the card, the peek ResolveData should include the
        // updated information.
        $this->assert->hasCardCustomer(
            $afterUpdatePeekResponse,
            $cardCustomerData,
        );
        $this->assert->hasExpiry($afterUpdatePeekResponse, $expiry);
        $this->assert->hasAvsData($afterUpdatePeekResponse, $avsData);
        $this->assert->hasMaskedCardNumber($updateResponse, $updatedNumber);
    }
}
