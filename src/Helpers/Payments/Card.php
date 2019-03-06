<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers\Payments;

use NAttreid\StripeApi\Helpers\AbstractPayment;

/**
 * Class CardPayment
 *
 * @author Attreid <attreid@gmail.com>
 */
class Card extends AbstractPayment
{
	/** @var string */
	private $email;

	public function setEmail(string $email): self
	{
		$this->email = $email;
		return $this;
	}

	public function getPaymentData(): array
	{
		list($currency, $amount) = $this->checkAmount();

		return [
			'amount' => (string) $amount,
			'currency' => $currency,
			'email' => $this->email,
		];
	}
}