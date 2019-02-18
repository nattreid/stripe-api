<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers;

use NAttreid\Utils\Strings;
use Nette\InvalidStateException;

/**
 * Class AbstractPayment
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class AbstractPayment
{

	/** @var string */
	private $currency;

	/** @var float */
	private $price;

	protected function checkAmount(): array
	{
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

	public function setCurrency(string $code): self
	{
		$this->currency = $code;
		return $this;
	}

	public function setPrice(float $price): self
	{
		$this->price = $price;
		return $this;
	}

	public abstract function getPaymentData(): array;

	public function getChargeData(): array
	{
		list($currency, $amount) = $this->checkAmount();

		return [
			'amount' => $amount,
			'currency' => $currency,
			'description' => 'Charge',
		];
	}
}