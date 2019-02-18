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
/** @var \NAttreid\StripeApi\Control\IStripePayButtonFactory @inject */
public $payButtonFactory;

/** @var \NAttreid\StripeApi\Control\IStripePaymentFactory @inject */
public $paymentFactory;

protected function createComponentButton()
{
    $control = $this->payButtonFactory->create();

    $payment = new \NAttreid\StripeApi\Helpers\PaymentRequest();
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

    $control->setSuccessUrl($this->presenter->link('/success'));
    $control->setErrorUrl($this->presenter->link('/error'));

    $control->onSuccess[] = function ($charge) {

    };

    $control->onError[] = function (\Exception $exception) {
       
    };

    return $control;
}
```

```latte
{control button, 'Pay', [class => button]}

{control payment}
```
