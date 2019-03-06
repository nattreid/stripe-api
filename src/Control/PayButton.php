<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use InvalidArgumentException;
use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\StripeApi\Helpers\Payments\PaymentRequest;
use NAttreid\StripeApi\Helpers\StripeException;
use Nette\Application\AbortException;

/**
 * Class PayButton
 *
 * @author Attreid <attreid@gmail.com>
 */
class PayButton extends AbstractControl
{

	/** @var string */
	private $unsupported = 'Your browser does not support this payment';

	/** @var string|null */
	private $type;

	/**
	 * @throws AbortException
	 */
	public function handleCharge(): void
	{
		$token = $this->presenter->request->getPost('token');
		try {
			$charge = $this->charge($token);

			switch ($charge->status) {
				default:
					throw new StripeException('Charge status: ' . $charge->status);
				case 'succeeded':
					$this->onSuccess($charge);
					http_response_code(200);
			}
		} catch (\Exception $ex) {
			$this->onError($ex);
			http_response_code(500);
		}

		$this->presenter->terminate();
	}

	public function setOnlyApplePay(): self
	{
		$this->type = 'apple';
		return $this;
	}

	public function setOnlyPaymentRequest(): self
	{
		$this->type = 'paymentRequest';
		return $this;
	}

	public function setUnsupported(string $text): self
	{
		$this->unsupported = $text;
		return $this;
	}

	public function setPayment(AbstractPayment $payment): AbstractControl
	{
		if (!$payment instanceof PaymentRequest) {
			throw new InvalidArgumentException("Payment must be 'PaymentRequest' class");
		}
		return parent::setPayment($payment);
	}

	/**
	 * @throws StripeException
	 */
	public function render(): void
	{
		$template = $this->template;

		$template->unsupported = $this->unsupported;
		$template->type = $this->type;

		$template->setFile(__DIR__ . '/templates/payButton.latte');
		parent::render();
	}
}

interface IPayButtonFactory
{
	public function create(): PayButton;
}