<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers;

use NAttreid\Utils\Strings;
use Nette\InvalidStateException;

/**
 * Class PaymentRequest
 *
 * @author Attreid <attreid@gmail.com>
 */
class PaymentRequest
{
	/** @var string */
	private $currency;

	/** @var string */
	private $country;

	/** @var float */
	private $price;

	public function setCurrency(string $code): self
	{
		$this->currency = $code;
		$this->currency = 'eur';
		return $this;
	}

	public function setCountry(string $code): self
	{
		$this->country = $code;
		$this->country = 'DE';
		return $this;
	}

	public function setPrice(float $price): self
	{
		$this->price = $price;
		return $this;
	}

	public function getData(): array
	{
		if ($this->country === null) {
			throw new InvalidStateException('Country is not set.');
		}
		if ($this->currency === null) {
			throw new InvalidStateException('Currency is not set.');
		}
		if ($this->price === null) {
			throw new InvalidStateException('Price is not set.');
		}

		return [
			'country' => $this->country,
			'currency' => Strings::lower($this->currency),
			'total' => [
				'label' => 'Total',
				'amount' => $this->price,
			],
			'requestPayerName' => true,
			'requestPayerEmail' => true
		];
	}
}