<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Cards\Visa;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Transactables\AddCard;
use CraigPaul\Moneris\Transactables\LookupCard;
use CraigPaul\Moneris\Transactables\UpdateCard;
use CraigPaul\Moneris\Data\Transactable\UpdateCardDetails;
use CraigPaul\Moneris\Transactables\VerifyCard;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\TestSupport\Enums\TestCard;

class UpdateCardTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function updating_non_card_number_data(CardInterface $testCard): void
    {
        // Card Verification //

        $verify = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $issuerId = $this->assert
            ->response($verify->submit($this->http(), $this->credentials()))
            ->hasIssuerId($testCard)
            ->getResponse()
            ->getIssuerId() ?? '';

        // Card Add //

        $add = new AddCard(
            creditCard: $testCard,
            cofData: new CofAddOrUpdateCardData($issuerId),
        );

        $dataKey = $this->assert
            ->response($add->submit($this->http(), $this->credentials()))
            ->isSuccessful()
            ->isComplete()
            ->hasDataKey()
            ->getResponse()
            ->getDataKey();

        // Lookup Before Update //

        $lookup = new LookupCard($dataKey);

        // Since the card was added with no customer data or AVS data, the
        // ResolveData should include empty customer and AVS details.
        $this->assert
            ->response($lookup->submit($this->http(), $this->credentials()))
            ->hasNoCardCustomerData()
            ->hasNoAvsData();

        // Card Update //

        // Here we're updating the card with full customer data and a new
        // expiry. Since we're not updating the actual card number, we don't
        // have to verify the card again or send CoF data.
        $cardCustomerData = $this->fixtures->cardCustomerData();
        $avsData = $this->fixtures->avsData();
        $expiry = Expiry::fromMonthAndYear('01', '26');
        $update = new UpdateCardDetails(
            dataKey: $dataKey,
            expiry: $expiry,
            customerData: $cardCustomerData,
            avsData: $avsData,
        );

        $this->assert
            ->response($update->submit($this->http(), $this->credentials()))
            ->isSuccessful()
            ->isComplete()
            ->hasCardCustomerData($cardCustomerData)
            ->hasAvsData($avsData)
            ->hasExpiry($expiry);

        // Lookup After Update //

        $lookup = new LookupCard($dataKey);

        // Since the card was added with no customer data or AVS data, the
        // ResolveData should include empty customer and AVS details.
        $this->assert
            ->response($lookup->submit($this->http(), $this->credentials()))
            ->hasCardCustomerData($cardCustomerData)
            ->hasAvsData($avsData)
            ->hasExpiry($expiry);
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function updating_card_number_only(CardInterface $testCard): void
    {
        $verifyBeforeAdd = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $issuerId1 = $this->submit($verifyBeforeAdd)
            ->isSuccessful()
            ->getResponse()
            ->getIssuerId();

        $add = new AddCard(
            creditCard: $testCard,
            cofData: new CofAddOrUpdateCardData($issuerId1 ?? ''),
        );

        $dataKey = $this->submit($add)
            ->isSuccessful()
            ->isComplete()
            ->getResponse()
            ->getDataKey();

        $updatedNumber = new Pan(TestCard::Visa->value);
        $updatedCard = new Visa((string) $updatedNumber, $testCard->getExpiry());

        // Because we're updating the credit card number, we need to get the
        // new issuer ID by verifying the new card number.
        $verifyBeforeUpdate = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $updatedCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $issuerId2 = $this->submit($verifyBeforeUpdate)
            ->isSuccessful()
            ->getResponse()
            ->getIssuerId();

        $update = new UpdateCard(
            dataKey: $dataKey,
            cardNumber: $updatedNumber,
            cofData: new CofAddOrUpdateCardData($issuerId2 ?? ''),
        );

        $this->submit($update)
            ->isSuccessful()
            ->isComplete()
            ->hasDataKey($dataKey);

        $lookup = new LookupCard($dataKey);

        $this->submit($lookup)
            ->isSuccessful()
            ->isComplete()
            ->hasMaskedCardNumber($updatedNumber);
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function updating_everything(CardInterface $testCard): void
    {
        $verifyBeforeAdd = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $issuerId1 = $this->submit($verifyBeforeAdd)
            ->isSuccessful()
            ->getResponse()
            ->getIssuerId();

        $add = new AddCard(
            creditCard: $testCard,
            cofData: new CofAddOrUpdateCardData($issuerId1 ?? ''),
        );

        $dataKey1 = $this->submit($add)
            ->isSuccessful()
            ->isComplete()
            ->getResponse()
            ->getDataKey();

        $lookupBeforeUpdate = new LookupCard($dataKey1);

        $this->submit($lookupBeforeUpdate)
            ->isSuccessful()
            ->isComplete()
            ->hasNoCardCustomerData()
            ->hasNoAvsData();

        $cardCustomerData = $this->fixtures->cardCustomerData();
        $avsData = $this->fixtures->avsData();
        $expiry = $this->fixtures->expiry();
        $updatedNumber = new Pan(TestCard::Visa->value);
        $updatedCard = new Visa((string) $updatedNumber, $testCard->getExpiry());

        $verifyBeforeUpdate = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $updatedCard,
            cvdData: $this->fixtures->cvdData(),
            cofData: new CofVerificationData(),
        );

        $issuerId2 = $this->submit($verifyBeforeUpdate)
            ->isSuccessful()
            ->isComplete()
            ->hasIssuerId($updatedCard)
            ->getResponse()
            ->getIssuerId();

        $updateCard = new UpdateCard(
            dataKey: $dataKey1,
            cardNumber: $updatedNumber,
            cofData: new CofAddOrUpdateCardData($issuerId2),
            expiryDate: $expiry,
            cardCustomerData: $cardCustomerData,
            avsData: $avsData,
        );

        $dataKey2 = $this->submit($updateCard)
            ->isSuccessful()
            ->isComplete()
            ->hasDataKey($dataKey1)
            ->hasCardCustomerData($cardCustomerData)
            ->hasMaskedCardNumber($updatedNumber)
            ->getResponse()
            ->getDataKey();

        // The data key should be the same before and after the update,
        // regardless of whether or not the card number was updated.
        $this->assertTrue($dataKey1->equals($dataKey2));

        // However the issuer id will be different, since the issuer id is
        // a value returned from the card issuer, and is based on the card
        // number.
        //
        // These will be null for issuers that don't support CoF, so we need
        // to check if they're null before comparing them.
        if ($issuerId1 && $issuerId2) {
            $this->assertFalse($issuerId1->equals($issuerId2));
        }

        // After updating the card, the lookup's ResolveData should include the
        // updated information.
        $this->submit(new LookupCard($dataKey1))
            ->isSuccessful()
            ->isComplete()
            ->hasExpiry($expiry)
            ->hasCardCustomerData($cardCustomerData)
            ->hasAvsData($avsData)
            ->hasMaskedCardNumber($updatedNumber);
    }
}
