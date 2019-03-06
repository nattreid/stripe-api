# Stripe API pro Nette Framework
Nastavení v **config.neon**
```neon
extensions:
    stripeApi: NAttreid\StripeApi\DI\StripeApiExtension

stripeApi:
	publishableApiKey: xxxXXXxXXXXxxxx
```

### Použití
```php
/** @var \NAttreid\StripeApi\Control\IPayButtonFactory @inject */
public $payButtonFactory;

/** @var \NAttreid\StripeApi\Control\IPaymentFactory @inject */
public $paymentFactory;

/** @var \NAttreid\StripeApi\Control\IMasterPassButtonFactory @inject */
public $masterpassFactory;

/** @var \NAttreid\StripeApi\Control\ICardElementFactory @inject */
public $cardElementFactory;

/** @var \NAttreid\StripeApi\Control\ICardPaymentFactory @inject */
public $cardPaymentFactory;

protected function createComponentButton()
{
    $control = $this->payButtonFactory->create();

    $payment = new \NAttreid\StripeApi\Helpers\Payments\PaymentRequest();
    $payment->setCurrency('usd');
    $payment->setCountry('US');
    $payment->setPrice(1000);

    $control->setPayment($payment);
    $control->setOnlyApplePay();        # platby pouze apple pay
    $control->setOnlyPaymentRequest();  # pouze ostatni platby, krome apple pay
    $control->setUnsupported('Nepodporuje');         # text nepodporovane platby v prohlizeci
    
    $control->setSuccessUrl('/success');
    $control->setErrorUrl('/error');

    $control->onSuccess[] = function ($charge) {
        
    };

    $control->onError[] = function (\Exception $exception) {
        
    };

    return $control;
}

protected function createComponentPayment()
{
    $control = $this->paymentFactory->create();

    $payment = new \NAttreid\StripeApi\Helpers\Payments\Giropay();
    $payment->setCurrency('eur');
    $payment->setOrderId(5555);
    $payment->setOwner('Testing Name');
    $payment->setPrice(200);

    $control->setPayment($payment);

    $control->setSuccessUrl('/success');
    $control->setErrorUrl('/error');

    $control->onSuccess[] = function ($charge) {

    };

    $control->onError[] = function (\Exception $exception) {
       
    };

    return $control;
}

protected function createComponentMasterpass()
{
    $control = $this->masterpassFactory->create();

    $payment = new \NAttreid\StripeApi\Helpers\Payments\Masterpass();
    $payment->setCurrency('eur');
    $payment->setCartId('xxXXXxXXxXXXXxx');
    $payment->setPrice(200);

    $control->setPayment($payment);

    $control->setSuccessUrl('/success');
    $control->setErrorUrl('/error');

    $control->onSuccess[] = function ($charge) {

    };

    $control->onError[] = function (\Exception $exception) {
       
    };

    return $control;
}

protected function createComponentCardElement()
{
    $control = $this->cardElementFactory->create();

    $payment = new \NAttreid\StripeApi\Helpers\Payments\Card();
    $payment->setCurrency('eur');
    $payment->setPrice(200);

    $control->setPayment($payment);

    $control->setSuccessUrl('/success');
    $control->setErrorUrl('/error');

    $control->onSuccess[] = function ($charge) {

    };

    $control->onError[] = function (\Exception $exception) {
       
    };

    return $control;
}

protected function createComponentCardPayment()
{
    $control = $this->cardElementFactory->create();
    $control->setTitle('Payment');
    $control->setLocale('auto');
    $control->setAllowRemember(true);

    $payment = new \NAttreid\StripeApi\Helpers\Payments\Card();
    $payment->setCurrency('eur');
    $payment->setPrice(200);
    $payment->setEmail('test@test.com);

    $control->setPayment($payment);

    $control->setSuccessUrl('/success');
    $control->setErrorUrl('/error');

    $control->onSuccess[] = function ($charge) {

    };

    $control->onError[] = function (\Exception $exception) {
       
    };

    return $control;
}
```

```latte
{control button}

{control payment}

{control masterpass}

{control cardPayment, 'Pay with Card'}

{control cardElement, 'Submit Payment', [class => button]}
```
