<?php

namespace CraigPaul\Moneris\TestSupport\Cases;

use CraigPaul\Moneris\Cards\Amex;
use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\MasterCard;
use CraigPaul\Moneris\Cards\Visa;
use CraigPaul\Moneris\Config\HttpConfig;
use CraigPaul\Moneris\Config\Credentials;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Cof\CofPurchaseData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\Transactable\Capture;
use CraigPaul\Moneris\Data\Transactable\Preauth;
use CraigPaul\Moneris\Data\Transactable\Purchase;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Gateway;
use CraigPaul\Moneris\Http;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use CraigPaul\Moneris\Support\Http\HttpConfigInterface;
use CraigPaul\Moneris\Support\Http\HttpInterface;
use CraigPaul\Moneris\Support\Transactables\TransactableInterface;
use CraigPaul\Moneris\TestSupport\Assert;
use CraigPaul\Moneris\TestSupport\AssertResponse;
use CraigPaul\Moneris\TestSupport\Data\TransactionAndOrderIds;
use CraigPaul\Moneris\TestSupport\Enums\TestCard;
use CraigPaul\Moneris\TestSupport\Fixtures;
use CraigPaul\Moneris\TestSupport\Verification\AmexSource;
use CraigPaul\Moneris\TestSupport\Verification\MasterCardSource;
use CraigPaul\Moneris\TestSupport\Verification\VisaSource;
use CraigPaul\Moneris\Transactables\AddCard;
use CraigPaul\Moneris\Transactables\VaultPreauth;
use CraigPaul\Moneris\Transactables\VerifyCard;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\DataKey;
use CraigPaul\Moneris\Values\IssuerId;
use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionId;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Ramsey\Uuid\Uuid;

class TestCase extends PHPUnitTestCase
{
    protected string $storeId;

    protected string $apiToken;

    protected readonly Assert $assert;

    protected readonly Fixtures $fixtures;

    public function setUp(): void
    {
        parent::setUp();

        $this->assert = new Assert();
        $this->fixtures = new Fixtures($this->faker());
    }

    protected function faker(): Generator
    {
        return Factory::create();
    }

    protected function testHttpConfig(): HttpConfigInterface
    {
        return new HttpConfig(host: 'esqa.moneris.com');
    }

    protected function credentials(): CredentialsInterface
    {
        return new Credentials(
            $this->testHttpConfig(),
            storeId: $_ENV['STORE_ID'],
            apiToken: $_ENV['STORE_TOKEN'],
        );
    }

    protected function avsCredentials(): CredentialsInterface
    {
        return new Credentials(
            $this->testHttpConfig(),
            storeId: $_ENV['AVS_STORE_ID'],
            apiToken: $_ENV['AVS_STORE_TOKEN'],
        );
    }

    public function submit(
        TransactableInterface $transactable,
        bool $avs = false,
    ): AssertResponse {
        return new AssertResponse(
            $transactable->submit(
                http: $this->http(),
                credentials: $avs
                    ? $this->avsCredentials()
                    : $this->credentials(),
            )
        );
    }

    protected function http(): HttpInterface
    {
        return new Http(
            config: $this->testHttpConfig(),
            guzzle: new Client(),
        );
    }

    /**
     * Get the gateway for non-avs testing.
     */
    protected function gateway(): Gateway
    {
        return new Gateway(
            new Credentials(
                $this->testHttpConfig(),
                storeId: $_ENV['STORE_ID'],
                apiToken: $_ENV['STORE_TOKEN'],
            )
        );
    }

    /**
     * Get the gateway for avs testing. Moneris provides only 1 store instance
     * with AVS enabled. Using the non-AVS gateway will result in a null AVS
     * result.
     */
    protected function avsGateway(): Gateway
    {
        return new Gateway(
            new Credentials(
                $this->testHttpConfig(),
                storeId: $_ENV['AVS_STORE_ID'],
                apiToken: $_ENV['AVS_STORE_TOKEN'],
            )
        );
    }

    protected function uid(): string
    {
        return Uuid::uuid4()->toString();
    }

    // Data Providers //

    public function basicCardProvider(): array
    {
        return array_map(
            fn (CardInterface $card) => [$card],
            TestCard::getCards(),
        );
    }

    public function avsCardProvider(): array
    {
        $expiry = new Expiry('2608');

        return [
            [new Amex(AmexSource::getApprovedMatch(avs: AvsCode::Y), $expiry)],
            [new Visa(VisaSource::getApprovedMatch(avs: AvsCode::Y), $expiry)],
            [
                new MasterCard(
                    MasterCardSource::getApprovedMatch(avs: AvsCode::Y),
                    $expiry,
                ),
            ],
        ];
    }

    public function avsAndCvdPurchaseProvider(): array
    {
        $expiry = new Expiry('2608');

        return [
            'visa' => [
                'card' => TestCard::Visa->toCard($expiry),
                'amount' => new Amount('10.41'),
                'cvdCode' => CvdCode::M,
                'avsCode' => AvsCode::Y,
            ],
            'mastercard' => [
                'card' => TestCard::MasterCard->toCard($expiry),
                'amount' => new Amount('10.32'),
                'cvdCode' => CvdCode::M,
                'avsCode' => AvsCode::R,
            ],
            'discover' => [
                'card' => TestCard::Discover->toCard($expiry),
                'amount' => new Amount('61.27'),
                'cvdCode' => CvdCode::M,
                'avsCode' => AvsCode::Y,
            ],
            'amex' => [
                'card' => TestCard::Amex->toCard($expiry),
                'amount' => new Amount('61.30'),
                'cvdCode' => CvdCode::U,
                'avsCode' => AvsCode::Y,
            ],
        ];
    }

    /**
     * There is currently no Discover card verification simulator, so that card
     * type is not represented in this provider.
     */
    public function avsAndCvdVerificationProvider(): \Iterator
    {
        $expiry = new Expiry('2608');

        yield 'visa' => [
            'creditCard' => new Visa(
                VisaSource::getApprovedMatch(CvdCode::M, AvsCode::Y),
                $expiry,
            ),
            'cvdCode' => CvdCode::M,
            'avsCode' => AvsCode::Y,
        ];

        yield 'mastercard' => [
            'creditCard' => new Visa(
                MasterCardSource::getApprovedMatch(CvdCode::M, AvsCode::X),
                $expiry,
            ),
            'cvdCode' => CvdCode::M,
            'avsCode' => AvsCode::X,
        ];

        yield 'amex' => [
            'creditCard' => new Amex(
                AmexSource::getApprovedMatch(CvdCode::Y, AvsCode::Y),
                $expiry,
            ),
            CvdCode::Y,
            AvsCode::Y,
        ];
    }

    // Common Procedures //

    protected function verifyCard(
        CardInterface $card,
        AvsData|null $avsData = null,
    ): IssuerId|null {
        $verify = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $card,
            cvdData: $this->fixtures->cvdData(),
            avsData: $avsData,
            cofData: new CofVerificationData(),
        );

        return $this->submit($verify, avs: (bool) $avsData)
            ->isComplete()
            ->isSuccessful()
            ->hasIssuerId($card)
            ->getResponse()
            ->getIssuerId();
    }

    protected function addCard(
        CardInterface $card,
        IssuerId|null $issuerId,
        AvsData|null $avsData = null,
    ): DataKey {
        $add = new AddCard(
            creditCard: $card,
            cofData: new CofAddOrUpdateCardData($issuerId),
            avsData: $avsData,
        );

        return $this->submit($add, avs: (bool) $avsData)
            ->isComplete()
            ->isSuccessful()
            ->hasDataKey()
            ->getResponse()
            ->getDataKey();
    }

    protected function vaultPreauth(
        DataKey $dataKey,
        IssuerId|null $issuerId,
        Amount $amount,
    ): TransactionAndOrderIds {
        $orderId = $this->fixtures->orderId();

        $preauth = new VaultPreauth(
            dataKey: $dataKey,
            orderId: $orderId,
            amount: $amount,
            cofData: $issuerId
                ? new CofPurchaseData($issuerId)
                : null,
        );

        $transactionId = $this->submit($preauth)
            ->isSuccessful()
            ->isComplete()
            ->hasTransactionId()
            ->getResponse()
            ->getTransactionId();

        return new TransactionAndOrderIds($transactionId, $orderId);
    }

    protected function preauth(
        CardInterface $card,
        Amount $amount,
    ): TransactionAndOrderIds {
        $orderId = $this->fixtures->orderId();

        $preauth = new Preauth(
            orderId: $orderId,
            creditCard: $card,
            amount: $amount,
        );

        $transactionId = $this->submit($preauth)
            ->isComplete()
            ->isSuccessful()
            ->isApproved()
            ->getResponse()
            ->getTransactionId();

        return new TransactionAndOrderIds($transactionId, $orderId);
    }

    protected function purchase(
        CardInterface $card,
        Amount $amount,
    ): TransactionAndOrderIds {
        $orderId = $this->fixtures->orderId();

        $purchase = new Purchase(
            orderId: $orderId,
            creditCard: $card,
            amount: $amount,
        );

        $transactionId = $this->submit($purchase)
            ->isComplete()
            ->isSuccessful()
            ->isApproved()
            ->getResponse()
            ->getTransactionId();

        return new TransactionAndOrderIds($transactionId, $orderId);
    }

    protected function capture(
        TransactionId $transactionId,
        OrderId $orderId,
        Amount $amount,
    ): TransactionId
    {
        $capture = new Capture(
            transactionId: $transactionId,
            orderId: $orderId,
            amount: $amount,
        );

        return $this->submit($capture)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->getResponse()
            ->getTransactionId();
    }

    protected function preauthAndCapture(
        CardInterface $card,
        Amount $amount,
    ): TransactionAndOrderIds
    {
        $ids = $this->preauth($card, $amount);

        return new TransactionAndOrderIds(
            $this->capture($ids->transactionId, $ids->orderId, $amount),
            $ids->orderId,
        );
    }
}
