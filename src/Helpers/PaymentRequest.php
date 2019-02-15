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
		return $this;
	}

	public function setCountry(string $code): self
	{
		$this->country = $code;
		return $this;
	}

	public function setPrice(float $price): self
	{
		$this->price = $price;
		return $this;
	}

	private function check(): array
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

		$currency = Strings::lower($this->currency);
		switch ($currency) {
			default:
				$price = $this->price;
				break;
			case 'czk':
			case 'usd':
			case 'eur':
			case 'pln':
			case 'gbp':
				$price = $this->price * 100;
		}
		return [$currency, (int) $price];
	}

	public function getPaymentData(): array
	{
		list($currency, $price) = $this->check();

		return [
			'country' => $this->country,
			'currency' => $currency,
			'total' => [
				'label' => 'Total',
				'amount' => $price,
			],
			'requestPayerName' => true,
			'requestPayerEmail' => true
		];
	}

	public function getChargeData()
	{
		list($currency, $price) = $this->check();

		return [
			'amount' => $price,
			'currency' => $currency,
			'description' => 'Charge',
		];
	}
}