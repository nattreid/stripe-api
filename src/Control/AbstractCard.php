<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use InvalidArgumentException;
use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\StripeApi\Helpers\Payments\Card;
use NAttreid\StripeApi\Helpers\StripeException;
use Nette\Application\AbortException;

/**
 * Class CardPayment
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class AbstractCard extends AbstractControl
{

	/**
	 * @throws AbortException
	 */
	public function handleCharge(): void
	{
		$token = $this->presenter->request->getPost('stripeToken');
		try {
			$charge = $this->charge($token);

			switch ($charge->status) {
				default:
					throw new StripeException('Charge status: ' . $charge->status);
				case 'succeeded':
					$this->onSuccess($charge);
					$url = $this->successUrl;
			}

		} catch (\Exception $ex) {
			$this->onError($ex);
			$url = $this->errorUrl;
		}
		$this->presenter->redirectUrl($url);
	}

	public function setPayment(AbstractPayment $payment): AbstractControl
	{
		if (!$payment instanceof Card) {
			throw new InvalidArgumentException("Payment must be 'Card' class");
		}
		return parent::setPayment($payment);
	}
}