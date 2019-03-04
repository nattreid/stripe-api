<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use NAttreid\StripeApi\Helpers\StripeException;
use Nette\Application\AbortException;
use Nette\Utils\Json;

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
	public function handleReceiveToken(): void
	{
		$json = $this->presenter->request->getPost('token');
		try {
			$token = Json::decode($json);
			$charge = $this->charge($token->id);

			switch ($charge->status) {
				default:
					throw new StripeException('Charge status: ' . $charge->status);
				case 'succeeded':
					$this->onSuccess($charge);
					$message = 'OK';
			}
		} catch (\Exception $ex) {
			$this->onError($ex);
			$message = 'ERROR';
		}

		$this->presenter->payload->message = $message;
		$this->presenter->sendPayload();
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