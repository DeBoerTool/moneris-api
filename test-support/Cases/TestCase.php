<?php

namespace CraigPaul\Moneris\TestSupport\Cases;

use CraigPaul\Moneris\Cards\Amex;
use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\MasterCard;
use CraigPaul\Moneris\Cards\Visa;
use CraigPaul\Moneris\Config\ConnectionConfig;
use CraigPaul\Moneris\Config\GatewayConfig;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Gateway;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\Interfaces\ConnectionConfigInterface;
use CraigPaul\Moneris\TestSupport\Assert;
use CraigPaul\Moneris\TestSupport\Enums\TestCard;
use CraigPaul\Moneris\TestSupport\Fixtures;
use CraigPaul\Moneris\TestSupport\Verification\AmexSource;
use CraigPaul\Moneris\TestSupport\Verification\MasterCardSource;
use CraigPaul\Moneris\TestSupport\Verification\VisaSource;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\OrderId;
use Faker\Factory;
use Faker\Generator;
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

    protected function testConnectionConfig(): ConnectionConfigInterface
    {
        return new ConnectionConfig(host: 'esqa.moneris.com');
    }

    /**
     * Get the gateway for non-avs testing.
     */
    protected function gateway(): Gateway
    {
        return new Gateway(new GatewayConfig(
            $this->testConnectionConfig(),
            storeId: $_ENV['STORE_ID'],
            apiToken: $_ENV['STORE_TOKEN'],
        ));
    }

    /**
     * Get the gateway for avs testing. Moneris provides only 1 store instance
     * with AVS enabled. Using the non-AVS gateway will result in a null AVS
     * result.
     */
    protected function avsGateway(): Gateway
    {
        return new Gateway(new GatewayConfig(
            $this->testConnectionConfig(),
            storeId: $_ENV['AVS_STORE_ID'],
            apiToken: $_ENV['AVS_STORE_TOKEN'],
        ));
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

    public function approvedAmount(): Amount
    {
        return Amount::fromFloat(1.00);
    }

    public function approvedAmountFor(CardType $cardType): Amount
    {
        return match ($cardType) {
            CardType::Visa => new Amount('10.21'),
            CardType::AmericanExpress => new Amount('10.21'),
            CardType::MasterCard => Amount::fromFloat(1.00),
            default => throw new InvalidArgumentException(
                sprintf('Invalid card type "%s" provided.', $cardType->name)
            ),
        };
    }

    public function orderId(): OrderId
    {
        return OrderId::of($this->uid());
    }
}
