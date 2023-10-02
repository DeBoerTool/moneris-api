<?php

namespace CraigPaul\Moneris;

use Adbar\Dot;
use CraigPaul\Moneris\Config\Credentials;
use CraigPaul\Moneris\Support\Transactables\TransactableDataInterface;
use CraigPaul\Moneris\Xml\Reader;
use CraigPaul\Moneris\Traits\GettableTrait;
use CraigPaul\Moneris\Traits\SettableTrait;
use CraigPaul\Moneris\Validation\Errors\ErrorList;
use CraigPaul\Moneris\Validation\Validator;
use SimpleXMLElement;

/**
 * @property-read array $errors
 * @property-read \CraigPaul\Moneris\Gateway $gateway
 * @property-read array $params
 * @property \SimpleXMLElement|null $response
 */
class Transaction
{
    use GettableTrait, SettableTrait;

    protected ErrorList $errors;

    protected SimpleXMLElement|null $xmlResponse = null;

    /**
     * The extra parameters needed for Moneris.
     */
    protected array $params;

    public function __construct(
        protected Credentials $config,
        array|TransactableDataInterface $params = [],
    ) {
        $this->errors = new ErrorList();

        $this->params = $params instanceof TransactableDataInterface
            ? $params->getTransactableData()
            : $this->prepare($params);
    }

    public function getErrorList(): ErrorList
    {
        return $this->errors;
    }

    public function getConfig(): Credentials
    {
        return $this->config;
    }

    public function getXmlResponse(): SimpleXMLElement|null
    {
        return $this->xmlResponse;
    }

    public function getXmlArray(): array
    {
        $reader = new Reader($this->getXmlResponse());

        return $reader->toArray();
    }

    public function getPath(string $dotNotatedPath): mixed
    {
        return (new Dot($this->getXmlArray()))->get($dotNotatedPath);
    }

    /**
     * Retrieve the amount for the transaction. The is only available on
     * certain transaction types.
     */
    public function amount(): string|null
    {
        if (isset($this->params['amount'])) {
            return $this->params['amount'];
        }

        return null;
    }

    /**
     * Check that the required parameters have not been provided to the
     * transaction.
     */
    public function invalid(): bool
    {
        return !$this->valid();
    }

    /**
     * Retrieve the transaction number, assuming the transaction has been
     * processed.
     */
    public function number(): string|null
    {
        if (is_null($this->xmlResponse)) {
            return null;
        }

        return (string) $this->xmlResponse->receipt->TransID;
    }

    /**
     * Retrieve the order id for the transaction. The is only available on
     * certain transaction types.
     */
    public function order(): string|null
    {
        return $this->params['order_id'] ?? null;
    }

    /**
     * Validate the result of the Moneris API call.
     */
    public function validate(SimpleXMLElement $result): OldResponse
    {
        $this->xmlResponse = $result;

        $response = new OldResponse($this);
        $response->validate();

        return $response;
    }

    /**
     * Check that the required parameters have been provided to the
     * transaction.
     */
    public function valid(): bool
    {
        $validator = new Validator($this->config, $this->params);

        if (!$validator->passes()) {
            $this->errors = $validator->errors();
        }

        return $validator->passes();
    }

    /**
     * Convert the transaction parameters into an XML structure.
     *
     * @codeCoverageIgnore
     */
    public function toXml(): bool|string
    {
        $params = $this->params;

        $type = in_array($params['type'], ['txn', 'acs'])
            ? 'MpiRequest'
            : 'request';

        $xml = new SimpleXMLElement("<$type/>");
        $xml->addChild('store_id', $this->config->storeId);
        $xml->addChild('api_token', $this->config->apiToken);

        $type = $xml->addChild($params['type']);

        $eFraud = in_array(
            $params['type'],
            [
                'purchase',
                'preauth',
                'card_verification',
                'cavv_purchase',
                'cavv_preauth',
                'res_purchase_cc',
                'res_preauth_cc',
            ]
        );

        $cc_action = in_array($params['type'], [
            'res_add_cc',
            'res_update_cc',
        ]);

        unset($params['type']);

        if ($this->config->useCvd && $eFraud) {
            $cvd = $type->addChild('cvd_info');

            $cvdInfo = $params['cvd_info'];

            $cvd->addChild('cvd_indicator', $cvdInfo['cvd_indicator']);
            $cvd->addChild('cvd_value', $cvdInfo['cvd_value']);

            unset($params['cvd_indicator'], $params['cvd_value']);
        }

        if ($this->config->useAvs && $eFraud) {
            $avs = $type->addChild('avs_info');

            foreach ($params as $key => $value) {
                if (!str_starts_with($key, 'avs_')) {
                    continue;
                }

                $avs->addChild($key, $value);

                unset($params[$key]);
            }
        }

        if ($this->config->useCof && ($eFraud || $cc_action)) {
            $cofInfo = $type->addChild('cof_info');
            if (!empty($params['payment_indicator'])) {
                $cofInfo->addChild('payment_indicator', $params['payment_indicator']);
            }

            if (!empty($params['payment_information'])) {
                $cofInfo->addChild('payment_information', $params['payment_information']);
            }

            if (!empty($params['issuer_id'])) {
                $cofInfo->addChild('issuer_id', $params['issuer_id']);
            }

            unset($params['payment_indicator'], $params['payment_information'], $params['issuer_id']);
        }

        $this->append($params, $type);

        var_dump($xml->asXML());

        return $xml->asXML();
    }

    /**
     * Append elements to the XML response.
     *
     * @codeCoverageIgnore
     */
    protected function append(array $params, SimpleXMLElement $type): void
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                if ($key === 'items') {
                    foreach ($value as $item) {
                        $parent = $type->addChild('item');
                        $parent->addChild(
                            'name',
                            $item['name'] ?? ''
                        );
                        $parent->addChild(
                            'quantity',
                            $item['quantity'] ?? ''
                        );
                        $parent->addChild(
                            'product_code',
                            $item['product_code'] ?? ''
                        );
                        $parent->addChild(
                            'extended_amount',
                            $item['extended_amount'] ?? ''
                        );
                    }
                } else {
                    $parent = $type->addChild($key);

                    $this->append($value, $parent);
                }
            } else {
                $type->addChild($key, $value);
            }
        }
    }

    /**
     * Prepare the transaction parameters.
     *
     * @codeCoverageIgnore
     */
    protected function prepare(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $params[$key] = trim($value);
            }

            if ($params[$key] == '') {
                unset($params[$key]);
            }
        }

        if (isset($params['credit_card'])) {
            $params['pan'] = preg_replace('/\D/', '', $params['credit_card']);
            unset($params['credit_card']);
        }

        if (isset($params['description'])) {
            $params['dynamic_descriptor'] = $params['description'];
            unset($params['description']);
        }

        if (isset($params['expiry_month']) && isset($params['expiry_year']) && !isset($params['expdate'])) {
            $params['expdate'] = sprintf('%02d%02d', $params['expiry_year'], $params['expiry_month']);
            unset($params['expiry_year'], $params['expiry_month']);
        }

        return $params;
    }
}
