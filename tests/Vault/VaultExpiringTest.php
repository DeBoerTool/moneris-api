<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\Transactable\AddCardData;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\TestSupport\Enums\TestCard;
use CraigPaul\Moneris\TestSupport\Stubs\VaultExpiringStub;
use CraigPaul\Moneris\Transaction;

class VaultExpiringTest extends TestCase
{
    /** @test */
    public function getting_expiring_cards(): void
    {
        $expiry = date('ym', strtotime('today + 10 days'));
        $cardAddResponses = [];
        $cards = TestCard::getCards($expiry);

        $vault = $this->gateway()->getVault();
        $cofData = new CofAddOrUpdateCardData('null');

        foreach ($cards as $card) {
            $cardAddResponses[] = $vault->add(new AddCardData(
                cardData: new CardData($card),
                cofData: $cofData,
            ));
        }

        // There doesn't seem to be a way to test this with the Moneris Test
        // API, so instead we'll mock it.
        $client = mock_handler(
            (new VaultExpiringStub())->render($cardAddResponses),
        );

        $params = ['type' => 'res_get_expiring'];
        $transaction = new Transaction($vault->getConfig(), $params);
        $processor = new Processor(
            $vault->getConfig()->getConnectionConfig(),
            $client,
        );

        $response = $processor->process($transaction);
        $receipt = $response->getReceipt();

        $this->assertTrue($response->isSuccessful());
        $this->assertGreaterThan(0, count($receipt->read('data')));

        /** @var \CraigPaul\Moneris\Response $card */
        foreach ($cardAddResponses as $index => $card) {
            /** @var \CraigPaul\Moneris\Receipt $rec */
            $rec = $card->getReceipt();

            $this->assertEquals(
                $rec->read('key'),
                $receipt->read('data')[$index]['data_key'],
            );
            $this->assertEquals(
                $rec->read('data')['masked_pan'],
                $receipt->read('data')[$index]['masked_pan'],
            );
        }
    }
}
