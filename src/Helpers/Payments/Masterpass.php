<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers\Payments;

use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\Utils\Number;
use NAttreid\Utils\Strings;
use Nette\InvalidStateException;

/**
 * Class Masterpass
 *
 * @author Attreid <attreid@gmail.com>
 */
class Masterpass extends AbstractPayment
{

	/** @var string */
	private $cartId;

	public function setCartId(string $cartId): self
	{
		$this->cartId = $cartId;
		return $this;
	}

	protected function checkAmount(): array
	{
		parent::checkAmount();
		return [Strings::upper($this->currency), number_format($this->price, 2)];
	}

	public function getSource(string $verifier): array
	{
		list($currency, $amount) = $this->checkAmount();
		return [
			'type' => 'card',
			'currency' => $currency,
			'amount' => $amount,
			'card' => [
				'masterpass' => [
					'cart_id' => $this->cartId,
					'transaction_id' => $verifier
				]
			]
		];
	}

	public function getPaymentData(): array
	{
		if ($this->cartId === null) {
			throw new InvalidStateException('CartId is not set.');
		}
		list($currency, $amount) = $this->checkAmount();

		return [
			'allowedCardTypes' => ['master, amex, visa'],
			'amount' => $amount,
			'currency' => $currency,
			'cartId' => $this->cartId
		];
	}
}