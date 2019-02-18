<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers;

use Nette\InvalidStateException;

/**
 * Class Payment
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class Payment extends AbstractPayment
{
	/** @var string */
	private $orderId;

	/** @var string */
	private $owner;

	public abstract function getPaymentName(): string;

	public function setOrderId(string $orderId): Payment
	{
		$this->orderId = $orderId;
		return $this;
	}

	public function setOwner(string $owner): Payment
	{
		$this->owner = $owner;
		return $this;
	}

	public function getPaymentData(): array
	{
		if ($this->orderId === null) {
			throw new InvalidStateException('OrderId is not set.');
		}
		if ($this->owner === null) {
			throw new InvalidStateException('Owner is not set.');
		}
		list($currency, $amount) = $this->checkAmount();

		return [
			'type' => $this->getPaymentName(),
			'amount' => $amount,
			'currency' => $currency,
			'statement_descriptor' => 'ORDER ' . $this->orderId,
			'owner' => [
				'name' => $this->owner,
			]
		];
	}
}