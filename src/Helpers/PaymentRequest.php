<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers;

use Nette\InvalidStateException;

/**
 * Class PaymentRequest
 *
 * @author Attreid <attreid@gmail.com>
 */
class PaymentRequest extends AbstractPayment
{
	/** @var string */
	private $country;

	public function setCountry(string $code): self
	{
		$this->country = $code;
		return $this;
	}

	public function getPaymentData(): array
	{
		if ($this->country === null) {
			throw new InvalidStateException('Country is not set.');
		}
		list($currency, $amount) = $this->checkAmount();

		return [
			'country' => $this->country,
			'currency' => $currency,
			'total' => [
				'label' => 'Total',
				'amount' => $amount,
			],
			'requestPayerName' => true,
			'requestPayerEmail' => true
		];
	}
}