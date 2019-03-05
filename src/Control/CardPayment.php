<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use InvalidArgumentException;
use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\StripeApi\Helpers\Payments\Card;
use Tracy\Debugger;

/**
 * Class CardPayment
 *
 * @author Attreid <attreid@gmail.com>
 */
class CardPayment extends AbstractControl
{

	public function handleCharge(): void
	{
		$data = $this->getParameters();
		Debugger::barDump($data);
	}

	public function setPayment(AbstractPayment $payment): AbstractControl
	{
		if (!$payment instanceof Card) {
			throw new InvalidArgumentException("Payment must be 'Card' class");
		}
		return parent::setPayment($payment);
	}

	public function render(): void
	{
		$template = $this->template;

		$template->setFile(__DIR__ . '/templates/cardPayment.latte');
		parent::render();
	}
}

interface ICardPaymentFactory
{
	public function create(): CardPayment;
}