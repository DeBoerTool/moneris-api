<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\Transactable\VaultPreauthData;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class VaultPreauthTest extends TestCase
{
    /** @test */
    public function preauth(): void
    {
        $key = $this->addCard();

        $data = new VaultPreauthData(
            dataKey: $key,
            data: $this->params(),
        );

        $response = $this->gateway()->preauth($data);

        $this->assert->isSuccessful($response);
        $this->assert->hasVaultKey($response, $key);
        $this->assert->isComplete($response);
    }

    /** @test */
    public function preauth_with_customer(): void
    {
        $key = $this->addCard();

        // 2023-09-20 - Verified data is present in the Moneris test account.
        $fullCustomerData = new VaultPreauthData(
            dataKey: $key,
            data: $this->params(),
            customer: $this->fixtures->customerData(),
        );

        $fullCustomerResponse = $this->gateway()->preauth($fullCustomerData);

        $this->assert->isSuccessful($fullCustomerResponse);
        $this->assert->hasVaultKey($fullCustomerResponse, $key);
        $this->assert->isComplete($fullCustomerResponse);

        // 2023-09-20 - Verified data is present in the Moneris test account.
        $emptyCustomerData = new VaultPreauthData(
            dataKey: $key,
            data: $this->params(),
            customer: $this->fixtures->customerDataEmpty(),
        );

        $emptyCustomerResponse = $this->gateway()->preauth($emptyCustomerData);

        $this->assert->isSuccessful($emptyCustomerResponse);
        $this->assert->hasVaultKey($emptyCustomerResponse, $key);
        $this->assert->isComplete($emptyCustomerResponse);

        // 2023-09-20 - Verified data is present in the Moneris test account.
        $minCustomerData = new VaultPreauthData(
            dataKey: $key,
            data: $this->params(),
            customer: $this->fixtures->customerDataMin(),
        );

        $minCustomerResponse = $this->gateway()->preauth($minCustomerData);

        $this->assert->isSuccessful($minCustomerResponse);
        $this->assert->hasVaultKey($minCustomerResponse, $key);
        $this->assert->isComplete($minCustomerResponse);
    }

    /** @test */
    public function preauth_with_cvd(): void
    {
        $key = $this->addCard();

        $data = new VaultPreauthData(
            dataKey: $key,
            data: $this->params(),
            cvd: new CvdData('123'),
        );

        $response = $this->gateway(cvd: true)->preauth($data);

        $this->assert->isSuccessful($response);
        $this->assert->hasVaultKey($response, $key);
        $this->assert->isComplete($response);
        $this->assert->hasCvdResult('1M', $response);
    }
}
