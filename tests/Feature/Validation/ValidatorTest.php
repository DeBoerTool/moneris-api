<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation;

use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\Errors\EmptyError;
use CraigPaul\Moneris\Validation\Errors\NotSetError;
use CraigPaul\Moneris\Validation\Errors\UnsupportedTransactionError;
use CraigPaul\Moneris\Validation\Validator;
use CraigPaul\Moneris\Validation\ValidatorAbstract;

/**
 * Most of the validation tests here are to make sure that the resolution logic
 * for matching types to validators works properly. Individual validation
 * logic is tested separately, per class.
 *
 * @covers \CraigPaul\Moneris\Validation\Validator
 * @covers \CraigPaul\Moneris\Validation\ValidatorAbstract
 * @covers \CraigPaul\Moneris\Validation\AddCardValidator
 * @covers \CraigPaul\Moneris\Validation\CardVerificationValidator
 * @covers \CraigPaul\Moneris\Validation\CompletionValidator
 * @covers \CraigPaul\Moneris\Validation\DeleteCardValidator
 * @covers \CraigPaul\Moneris\Validation\PreauthValidator
 * @covers \CraigPaul\Moneris\Validation\PurchaseCorrectionValidator
 * @covers \CraigPaul\Moneris\Validation\PurchaseValidator
 * @covers \CraigPaul\Moneris\Validation\RefundValidator
 * @covers \CraigPaul\Moneris\Validation\UpdateCardValidator
 */
class ValidatorTest extends FeatureTestCase
{
    /** @test */
    public function static_constructor(): void
    {
        $validator = Validator::of($this->gateway(), []);

        $this->assertInstanceOf(ValidatorAbstract::class, $validator);
    }

    /** @test */
    public function failing_with_empty_params(): void
    {
        $validator = new Validator($this->gateway(), []);

        $this->assertFalse($validator->passes());
        $this->assertCount(1, $validator->errors());
        $this->assertInstanceOf(
            EmptyError::class,
            $validator->errors()->get(0)
        );
    }

    /** @test */
    public function failing_with_no_type_set(): void
    {
        $validator = new Validator($this->gateway(), ['foo' => 'bar']);

        $this->assertFalse($validator->passes());
        $this->assertCount(1, $validator->errors());
        $this->assertInstanceOf(
            NotSetError::class,
            $validator->errors()->get(0)
        );
        $this->assertSame(
            'type',
            $validator->errors()->get(0)->field()
        );
    }

    /** @test */
    public function failing_with_unsupported_type(): void
    {
        $validator = new Validator($this->gateway(), ['type' => 'foo']);

        $this->assertFalse($validator->passes());
        $this->assertCount(1, $validator->errors());
        $this->assertInstanceOf(
            UnsupportedTransactionError::class,
            $validator->errors()->get(0)
        );
    }

    /** @test */
    public function get_expiring(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_get_expiring',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function card_verification(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'card_verification',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'card_verification',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function card_verification_with_avs(): void
    {
        $validator = new Validator($this->gateway(avs: true), [
            'type' => 'card_verification',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(avs: true), [
            'type' => 'card_verification',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'avs_street_number' => '',
            'avs_street_name' => '',
            'avs_zipcode' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function card_verification_with_cvd(): void
    {
        $validator = new Validator($this->gateway(cvd: true), [
            'type' => 'card_verification',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cvd: true), [
            'type' => 'card_verification',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'cvd' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function card_verification_with_cof(): void
    {
        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'card_verification',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'card_verification',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'payment_indicator' => '',
            'payment_information' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function purchase(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'purchase',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'purchase',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function preauth(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'preauth',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'preauth',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function preauth_with_avs(): void
    {
        $validator = new Validator($this->gateway(avs: true), [
            'type' => 'preauth',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(avs: true), [
            'type' => 'preauth',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
            'avs_street_number' => '',
            'avs_street_name' => '',
            'avs_zipcode' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function preauth_with_cvd(): void
    {
        $validator = new Validator($this->gateway(cvd: true), [
            'type' => 'preauth',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cvd: true), [
            'type' => 'preauth',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
            'cvd' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function preauth_with_cof(): void
    {
        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'preauth',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'preauth',
            'order_id' => '',
            'pan' => '',
            'expdate' => '',
            'amount' => '',
            'payment_indicator' => '',
            'payment_information' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function tokenize(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_tokenize_cc',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'res_tokenize_cc',
            'order_id' => '',
            'txn_number' => '',
        ]);

        $this->assertTrue($validator->passes());

    }

    /** @test */
    public function purchase_correction(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'purchasecorrection',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'purchasecorrection',
            'order_id' => '',
            'txn_number' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function completion(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'completion',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'completion',
            'comp_amount' => '',
            'order_id' => '',
            'txn_number' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function refund(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'refund',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'refund',
            'amount' => '',
            'order_id' => '',
            'txn_number' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function add_card(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_add_cc',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'res_add_cc',
            'pan' => '',
            'expdate' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function add_card_with_cof(): void
    {
        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'res_add_cc',
            'pan' => '',
            'expdate' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'res_add_cc',
            'pan' => '',
            'expdate' => '',
            'issuer_id' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function update_card(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_update_cc',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'res_update_cc',
            'pan' => '',
            'expdate' => '',
            'data_key' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function update_card_with_cof(): void
    {
        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'res_update_cc',
            'pan' => '',
            'expdate' => '',
            'data_key' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'res_update_cc',
            'pan' => '',
            'expdate' => '',
            'data_key' => '',
            'issuer_id' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function delete_card(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_delete',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'res_delete',
            'data_key' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function lookup_full_card(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_lookup_full',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'res_lookup_full',
            'data_key' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function lookup_masked_card(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_lookup_masked',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'res_lookup_masked',
            'data_key' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function res_preauth(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_preauth_cc',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(), [
            'type' => 'res_preauth_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function res_preauth_with_avs(): void
    {
        $validator = new Validator($this->gateway(avs: true), [
            'type' => 'res_preauth_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(avs: true), [
            'type' => 'res_preauth_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
            'avs_street_number' => '',
            'avs_street_name' => '',
            'avs_zipcode' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function res_preauth_with_cvd(): void
    {
        $validator = new Validator($this->gateway(cvd: true), [
            'type' => 'res_preauth_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cvd: true), [
            'type' => 'res_preauth_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
            'cvd' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function res_preauth_with_cof(): void
    {
        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'res_preauth_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
        ]);

        $this->assertFalse($validator->passes());

        $validator = new Validator($this->gateway(cof: true), [
            'type' => 'res_preauth_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
            'payment_indicator' => '',
            'payment_information' => '',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function passing_res_purchase(): void
    {
        $validator = new Validator($this->gateway(), [
            'type' => 'res_purchase_cc',
            'data_key' => '',
            'order_id' => '',
            'amount' => '',
        ]);

        $this->assertTrue($validator->passes());
    }
}
