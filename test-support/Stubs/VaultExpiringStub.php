<?php

namespace CraigPaul\Moneris\TestSupport\Stubs;

class VaultExpiringStub
{
    public function render(array $cardResponses): string
    {
        $xmlResponseString = '<?xml version="1.0"?><response><receipt>';

        /** @var \CraigPaul\Moneris\Response $cardResponse */
        foreach ($cardResponses as $cardResponse) {
            $receipt = $cardResponse->getReceipt();
            $data = $receipt->read('data');
            $expdate = $data['expiry_date']['year'] . $data['expiry_date']['month'];

            $xmlResponseString .= '<ResolveData>';
            $xmlResponseString .= '<data_key>' . $receipt->read('key') . '</data_key>';
            $xmlResponseString .= '<payment_type>cc</payment_type><cust_id></cust_id><phone></phone><email></email><note></note>';
            $xmlResponseString .= '<expdate>' . $expdate . '</expdate>';
            $xmlResponseString .= '<masked_pan>' . $data['masked_pan'] . '</masked_pan>';
            $xmlResponseString .= '<crypt_type>' . $data['crypt'] . '</crypt_type>';
            $xmlResponseString .= '</ResolveData>';
        }

        $xmlResponseString .= '<DataKey></DataKey><ReceiptId></ReceiptId><ReferenceNum></ReferenceNum>';
        $xmlResponseString .= '<ResponseCode>001</ResponseCode><ISO></ISO><AuthCode></AuthCode>';
        $xmlResponseString .= '<Message>Successfully located ' . count($cardResponses) . ' expiring cards.</Message>';
        $xmlResponseString .= '<TransTime></TransTime><TransDate>' . date('Y-m-d') . '</TransDate>';
        $xmlResponseString .= '<TransType>' . date('h:i:s') . '</TransType><Complete>true</Complete>';
        $xmlResponseString .= '<TransAmount></TransAmount><CardType></CardType><TransID></TransID><TimedOut></TimedOut>';
        $xmlResponseString .= '<CorporateCard></CorporateCard><RecurSuccess></RecurSuccess><AvsResultCode></AvsResultCode>';
        $xmlResponseString .= '<CvdResultCode></CvdResultCode><ResSuccess>true</ResSuccess><PaymentType></PaymentType>';
        $xmlResponseString .= '</receipt></response>';

        return $xmlResponseString;
    }
}
