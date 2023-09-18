# Moneris API

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-build]][link-action]
[![Total Downloads][ico-downloads]][link-downloads]
 
## Requirements

PHP 8.1 and up.

## Composer

To get started, install the package via the Composer package manager:

```php
composer require dbt/moneris-api
```

## Instantiation

Creating a new Moneris instance is quite easy and straightforward:

```php
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Values\Environment;

$moneris = new Moneris(
    id: 'store2',
    token: 'yesguy',
    environment: Environment::testing(),
    avs: true, // defaults to false 
    cvd: true, // defaults to false 
    cof: true, // defaults to false 
);

$gateway = $moneris->connect();
```

Or via the `connect()` static method:

```php
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Values\Environment;

$gateway = Moneris::connect(
    id: 'store2',
    token: 'yesguy',
    environment: Environment::testing(),
    avs: true, // defaults to false 
    cvd: true, // defaults to false 
    cof: true, // defaults to false 
);
```

## Transactions

Making a purchase, preauthorizing a card, voiding a transaction, etc., is straightforward once you have your Gateway instantiated ([see above](#instantiation)).

### Purchase

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => '4242424242424242',
    'expiry_month' => '12',
    'expiry_year' => '20',
];

$response = $gateway->purchase($params);
```

### Pre-Authorization

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => '4242424242424242',
    'expiry_month' => '12',
    'expiry_year' => '20',
];

$response = $gateway->preauth($params);
```

### Capture (Pre-Authorization Completion)

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => '4242424242424242',
    'expiry_month' => '12',
    'expiry_year' => '20',
];

$response = $gateway->preauth($params);

$response = $gateway->capture($response->transaction);
```

### Void (Purchase Correction)

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => '4242424242424242',
    'expiry_month' => '12',
    'expiry_year' => '20',
];

$response = $gateway->purchase($params);

$response = $gateway->void($response->transaction);
```

### Refund

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => '4242424242424242',
    'expiry_month' => '12',
    'expiry_year' => '20',
];

$response = $gateway->purchase($params);

$response = $gateway->refund($response->transaction);
```

### Card Verification

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => '4242424242424242',
    'expiry_month' => '12',
    'expiry_year' => '20',
];

$response = $gateway->verify($params);
```

### CVD and AVS

To take advantage of the Card Verification Digits and/or Address Verification Service provided by Moneris, you need to tell Moneris that upon instantiation ([as shown above](#instantiation)).

When making a CVD secured purchase, pre-authorization or card verification, you need to pass the following parameter to the Gateway method you are utilizing.

```php
$params = [
    // `cvd` needs to be included in your transaction parameters.
    'cvd' => '111',
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => $this->visa,
    'expdate' => '2012',
];

$response = $gateway->verify($params); // could be purchase, preauth, etc.
```

When making an AVS secured purchase, pre-authorization or card verification, you need to pass the following parameters to the Gateway method you are utilizing.

```php
$params = [
    // `avs_*` keys need to be included in your transaction parameters.
    'avs_street_number' => '123',
    'avs_street_name' => 'Fake Street',
    'avs_zipcode' => 'X0X0X0',
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => $this->visa,
    'expdate' => '2012',
];

$response = $gateway->verify($params); // could be purchase, preauth, etc.
```

> Note: When making an AVS or CVD secured transaction, even if AVS or CVD fails, you will still have to void the transaction. There are two easy ways around this.

Verify the card first. Using this method, there is one additional caveat: Your verification transaction and purchase transaction must have different `order_id` parameters. One solution could be to prepend a specific prefix to the front of verification order ids.

```php
$response = $gateway->verify($params);

if ($response->isSuccessful()) {
    $response = $gateway->purchase($params);
    
    if ($response->isSuccessful()) {
        $receipt = $response->receipt();
    } else {
        $errors = $response->errors;
    }
}
```

Void the transaction.

```php
/** @var \CraigPaul\Moneris\Response $response */
$response = $gateway->purchase($params);

if (!$response->isSuccessful() && $response->getError()->isAvsOrCvd()) {
    $response = $gateway->void($response->getTransaction());
}
```

### Credential On File

The credential on file is part of the new Visa requirements to pass the CVD/CVV2 data for transactions. 

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'data_key' => $key,
    'payment_indicator' => 'U',
    'payment_information' => '2',
    'issuer_id' => $issuer_id // this is optional
];

$response = $vault->purchase($params); // could be purchase, preauth, etc.
```

## Vault

The Moneris Vault allows you to create and maintain credit card profiles on the Moneris servers instead of your own. To access the Vault, you will need to have your instantiated Gateway ([see above](#instantiation)).

```php
use CraigPaul\Moneris\Moneris;

$vault = Moneris::vault(
    id: 'store2',
    token: 'yesguy',
    environment: Environment::testing(),
    avs: true, // defaults to false 
    cvd: true, // defaults to false 
    cof: true, // defaults to false 
);

// Or if you already have the Gateway instantiated:

$vault = $gateway->cards();

// Or

$value = $gateway->vault();
```

### Add a Card

> **Note:** The expiry passed into the credit card is in the format of YYMM as that is how Moneris accepts it. 

```php
use CraigPaul\Moneris\CreditCard;

...

$card = CreditCard::create('4242424242424242', '2012');

$response = $vault->add($card);
```

### Update a Card

In order to maintain your credit card profiles, Moneris will send back a unique key for the profile that will allow you to keep track in your own database. You can retrieve the key once you have received your Receipt ([see more below](#responses-and-receipts))

```php
$card = CreditCard::create('4242424242424242', '2012');

$response = $vault->add($card);
$key = $response->receipt()->read('key');

$card->expiry = '2112';

$response = $vault->update($key, $card);
```

### Delete a Card

```php
$card = CreditCard::create('4242424242424242', '2012');

$response = $vault->add($card);
$key = $response->receipt()->read('key');

$response = $vault->delete($key);
```

### Attaching a Customer

In order to sync your customer information with the credit cards stored in the Vault, we can attach a basic `Customer` object to the `CreditCard`.

#### Add a Card

```php
use CraigPaul\Moneris\Customer;

...

$params = [
    'id' => uniqid('customer-', true),
    'email' => 'example@email.com',
    'phone' => '555-555-5555',
    'note' => 'Customer note',
];

$customer = Customer::create($params);
$card = CreditCard::create('4242424242424242', '2012');
$card = $card->attach($customer);

$response = $vault->add($card);
```

#### Update a Card and Customer

```php
use CraigPaul\Moneris\Customer;

...

$params = [
    'id' => uniqid('customer-', true),
    'email' => 'example@email.com',
    'phone' => '555-555-5555',
    'note' => 'Customer note',
];

$customer = Customer::create($params);
$card = CreditCard::create('4242424242424242', '2012');
$card = $card->attach($customer);

$response = $vault->add($card);
$key = $response->receipt()->read('key');

$card->customer->email = 'example2@email.com';

$response = $vault->update($card, $key);
```

### Tokenize a Previous Transaction

Used to create a credit card profile based on a previous transaction.

```php
$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'credit_card' => '4242424242424242',
    'expiry_month' => '12',
    'expiry_year' => '20',
];

$response = $gateway->purchase($params);

$response = $vault->tokenize($response->transaction);
```

### Peek Into The Vault

If you have the need to look up the masked credit card number you can peek into the Vault.

```php
$card = CreditCard::create('4242424242424242', '2012');

$response = $vault->add($card);
$key = $response->receipt()->read('key');

$response = $vault->peek($key);
$receipt = $response->receipt();

$masked = $receipt->read('data')['masked_pan'];
```

### Retrieve Expiring Cards

This transaction can be performed no more then 2 times on any given calendar day as per the Moneris API.

```php
$response = $vault->expiring();
```

### Transactions

Credit cards stored in the Moneris Vault have a slightly different flow for purchasing and pre-authorization. Any of the other transactions work exactly the same as shown above. 

#### Vault Purchase

```php
$card = CreditCard::create('4242424242424242', '2012');

$response = $vault->add($card);
$key = $response->receipt()->read('key');

$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'data_key' => $key,
];

$response = $vault->purchase($params); // 
```

> **Note:** The Vault is used for the transaction here instead of the base Gateway object.

#### Vault Pre-Authorization

```php
$card = CreditCard::create('4242424242424242', '2012');

$response = $vault->add($card);
$key = $response->receipt()->read('key');

$params = [
    'order_id' => uniqid('1234-56789', true),
    'amount' => '1.00',
    'data_key' => $key,
];

$response = $vault->preauth($params); // 
```

## Responses and Receipts

The `Response` and `Receipt` objects allow you to understand how everything went with your API call. After a transaction returns from being processed the `Response` will get validated and return all the relevant information for you.

### Response

The information available to you on the `Response` object is as follows:

#### Transaction ErrorList

After validation a transaction, you can get a list of validation errors:

```php
$errors = $response->transaction()->getErrorList();
```

Any errors that might occur during your transaction will be available from the returned `ErrorList`. You can then filter through them and deal with them as required.

```php
use CraigPaul\Moneris\Validation\Errors\ErrorList

// The following example would be returned when you forget to set the `order_id` on your transaction. 

$errors = new Errorlist(
    new \CraigPaul\Moneris\Validation\Errors\NotSetError('order_id')
);
```

#### Moneris Errors

Errors that occur after submitting the `Request` will be set on the `Request` object.

```php
$error = $response->getError();
```

If there are no errors, this will return null. Otherwise, you will get an instance of `ResponseErrorEnum`, an integer backed enumeration.

#### Success

```php
$success = $response->isSuccessful();
```

The successful method simply lets you know if your transaction has been processed successfully.

### Receipt

The `Receipt` object is your record of any information relevant to your transaction you have submitted. To retrieve your receipt once you have a response see the following.

```php
$response = $gateway->purchase($params);

$receipt = $response->receipt();
```

Depending on that type of transaction, you will have different items on your `Receipt` that you have available to read.
 
```php
$amount = $receipt->read('amount');
```

For a full list of possible readable receipt items, see below.

```php
amount - The amount of the transaction. (string)
authorization - The authorization code for the transaction. (string)
avs_result - The avs result code for the transaction. (string)
card - The card type used for the transaction. (string)
code - The response code for the transaction. (string)
complete - Whether the transaction had completed correctly or not. (boolean)
cvd_result - The cvd result code.  (string)
data - The data related to the customer and card for the transaction. (array)
date - The date of the transaction. (string)
id - The Moneris id of the receipt. (string)
iso - The ISO code for the transaction. (string)
key - The data key used for vault transactions. (string)
message - Any relevant message provided for the transaction. (string)
reference - The reference number for the transaction. (string)
time - The time of the transaction. (string)
transaction - The Moneris id of the transaction. (string)
type - The transaction type. (string)
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Dan Deboer][link-dan]
- [Craig Paul][link-craig]
- [All Contributors][link-contributors]

## License

Moneris API is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

[ico-version]: https://img.shields.io/github/v/release/DeBoerTool/moneris-api
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-build]: https://img.shields.io/github/workflow/status/DeBoerTool/moneris-api/phpunit.yml?branch=trunk
[ico-downloads]: https://img.shields.io/packagist/dt/dbt/moneris-api.svg

[link-packagist]: https://packagist.org/packages/dbt/moneris-api
[link-downloads]: https://packagist.org/packages/dbt/moneris-api
[link-action]: https://github.com/DeBoerTool/moneris-api

[link-craig]: https://github.com/craigpaul
[link-dan]: https://github.com/danielsdeboer
[link-contributors]: ../../contributors
