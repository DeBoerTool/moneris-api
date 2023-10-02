<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Cof\CofPurchaseData;
use CraigPaul\Moneris\Data\Transactable\VaultPurchase;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\Amount;

class PurchaseTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function vault_purchase(CardInterface $testCard): void
    {
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);

        $purchase = new VaultPurchase(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $this->fixtures->approvedAmount(),
            cofData: new CofPurchaseData($issuerId),
        );

        $this->submit($purchase)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function vault_purchase_with_customer(CardInterface $testCard): void
    {
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);

        $purchase = new VaultPurchase(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $this->fixtures->approvedAmount(),
            cofData: new CofPurchaseData($issuerId),
            customerData: $this->fixtures->customerData(),
        );

        /**
         * 2023-10-02 - Verified data is present in the Moneris test account.
         */
        $this->submit($purchase)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider avsAndCvdPurchaseProvider
     */
    public function purchasing_with_cvd_and_avs(
        CardInterface $testCard,
        Amount $amount,
        CvdCode $cvdCode,
        AvsCode $avsCode,
    ): void {
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);

        $purchase = new VaultPurchase(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $amount,
            cofData: new CofPurchaseData($issuerId),
            cvdData: $this->fixtures->cvdData(),
            avsData: $this->fixtures->avsData(),
        );

        $this->submit($purchase)
            ->isComplete()
            ->isSuccessful()
            ->isApproved()
            ->hasCvdResult($cvdCode)
            ->hasAvsResult($avsCode);
    }
}
