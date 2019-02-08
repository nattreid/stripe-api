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

protected function createComponentButton()
{
    $control = $this->payButtonFactory->create();

    $payment = new PaymentRequest();
    $payment->setCurrency('usd');
    $payment->setCountry('US');
    $payment->setPrice(1000);

    $control->setPayment($payment);
    $control->setOnlyApplePay();        # platby pouze apple pay
    $control->setOnlyPaymentRequest();  # pouze ostatni platby, krome apple pay
    $control->setUnsupported('Nepodporuje');         # text nepodporovane platby v prohlizeci
    
    $control->setSuccessUrl('/success');
    $control->setErrorUrl('/error');

    $control->onSuccess[] = function ($token) {
        
    };

    $control->onError[] = function (\Exception $exception) {
        
    };

    return $control;
}
```

```latte
{control bPayment, 'Pay', [class => button]}
```
