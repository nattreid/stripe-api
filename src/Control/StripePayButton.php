<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use NAttreid\StripeApi\Helpers\PaymentRequest;
use NAttreid\StripeApi\Helpers\StripeException;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use NAttreid\StripeApi\StripeClient;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\InvalidStateException;
use Nette\Utils\Json;

/**
 * Class StripePayButton
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripePayButton extends Control
{
	public $onSuccess = [];
	public $onError = [];

	/** @var StripeApiConfig */
	private $config;

	/** @var StripeClient */
	private $client;

	/** @var PaymentRequest */
	private $payment;

	/** @var string */
	private $successUrl;

	/** @var string */
	private $errorUrl;

	/** @var string */
	private $unsupported = 'Your browser does not support this payment';

	/** @var string|null */
	private $type;

	public function __construct(StripeApiConfig $config, StripeClient $client)
	{
		parent::__construct();
		$this->config = $config;
		$this->client = $client;
	}

	/**
	 * @throws AbortException
	 */
	public function handleReceiveToken(): void
	{
		$json = $this->presenter->getHttpRequest()->getPost('token');
		try {
			$token = Json::decode($json);
			$this->client->charge($token->id, $this->payment);
			$this->onSuccess($token);
			$message = 'OK';
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

	public function setPayment(PaymentRequest $payment): self
	{
		$this->payment = $payment;
		return $this;
	}

	public function setSuccessUrl(string $url): self
	{
		$this->successUrl = $url;
		return $this;
	}

	public function setUnsupported(string $text): self
	{
		$this->unsupported = $text;
		return $this;
	}

	public function setErrorUrl(string $url): self
	{
		$this->errorUrl = $url;
		return $this;
	}

	/**
	 * @throws StripeException
	 */
	public function render(): void
	{
		$template = $this->template;
		$template->key = $this->config->publishableApiKey;
		$template->unsupported = $this->unsupported;
		$template->type = $this->type;
		try {
			$template->payment = $this->payment->getPaymentData();

			if ($this->successUrl === null || $this->errorUrl === null) {
				throw new InvalidStateException('Success and Error url must be set');
			}
			$template->successUrl = $this->successUrl;
			$template->errorUrl = $this->errorUrl;
		} catch (InvalidStateException $ex) {
			throw new StripeException();
		}
		$template->setFile(__DIR__ . '/templates/default.latte');
		$template->render();
	}
}

interface IStripePayButtonFactory
{
	public function create(): StripePayButton;
}