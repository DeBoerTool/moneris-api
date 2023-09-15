<?php

namespace CraigPaul\Moneris\Validation;

use CraigPaul\Moneris\Exceptions\UnsupportedTransactionException;
use CraigPaul\Moneris\Validation\Errors\EmptyError;
use CraigPaul\Moneris\Validation\Errors\NotSetError;
use CraigPaul\Moneris\Validation\Errors\UnsupportedTransactionError;

class Validator extends ValidatorAbstract
{
    protected function validate(): void
    {
        if (!count($this->params)) {
            $this->errors->push(new EmptyError());

            return;
        }

        /**
         * The parameters absolutely must include the key "type".
         */
        if (!isset($this->params['type'])) {
            $this->errors->push(new NotSetError('type'));

            return;
        }

        /**
         * Match the correct validator for the transaction type. If one isn't
         * found, convert the generated exception to an error.
         */
        try {
            $validator = $this->getValidator();

            if (!$validator->passes()) {
                $this->errors = $this->errors->merge($validator->errors());
            }

            return;
        } catch (UnsupportedTransactionException) {
            $this->errors->push(new UnsupportedTransactionError());

            return;
        }
    }

    /**
     * @throws \CraigPaul\Moneris\Exceptions\UnsupportedTransactionException
     */
    protected function getValidator(): ValidatorInterface
    {
        $fqcn = match ($this->params['type']) {
            'res_get_expiring' => new PassthroughValidator(),

            'card_verification' => CardVerificationValidator::class,
            'preauth',
            'purchase' => PurchaseValidator::class,
            'res_tokenize_cc',
            'purchasecorrection' => PurchaseCorrectionValidator::class,
            'completion' => CompletionValidator::class,
            'refund' => RefundValidator::class,
            'res_add_cc' => AddCardValidator::class,
            'res_update_cc' => UpdateCardValidator::class,
            'res_delete',
            'res_lookup_full',
            'res_lookup_masked' => DeleteCardValidator::class,
            'res_preauth_cc',
            'res_purchase_cc' => PreauthValidator::class,

            default => throw new UnsupportedTransactionException(
                $this->params['type'],
            ),
        };

        return new $fqcn($this->gateway, $this->params);
    }
}
