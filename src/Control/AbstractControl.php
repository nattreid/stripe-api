<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\StripeApi\Helpers\StripeException;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use NAttreid\StripeApi\StripeClient;
use Nette\Application\UI\Control;
use Nette\InvalidStateException;
use Stripe\ApiResource;

/**
 * Class AbstractControl
 *
 * @author Attreid <attreid@gmail.com>
 */
class AbstractControl extends Control
{
	public $onSuccess = [];
	public $onError = [];

	/** @var StripeApiConfig */
	private $config;

	/** @var StripeClient */
	private $client;

	/** @var string */
	private $successUrl;

	/** @var string */
	private $errorUrl;

	/** @var AbstractPayment */
	private $payment;

	public function __construct(StripeApiConfig $config, StripeClient $client)
	{
		parent::__construct();
		$this->config = $config;
		$this->client = $client;
	}

	public function setSuccessUrl(string $url): self
	{
		$this->successUrl = $url;
		return $this;
	}

	public function setErrorUrl(string $url): self
	{
		$this->errorUrl = $url;
		return $this;
	}

	public function setPayment(AbstractPayment $payment): self
	{
		$this->payment = $payment;
		return $this;
	}

	protected function charge($source): ApiResource
	{
		return $this->client->charge($source, $this->payment);
	}

	/**
	 * @throws StripeException
	 */
	public function render(): void
	{
		$template = $this->template;
		$template->key = $this->config->publishableApiKey;

		try {
			$template->payment = $this->payment->getPaymentData();
		} catch (InvalidStateException $ex) {
			throw new StripeException();
		}

		if ($this->successUrl === null || $this->errorUrl === null) {
			throw new StripeException('Success and Error url must be set');
		}
		$template->successUrl = $this->successUrl;
		$template->errorUrl = $this->errorUrl;

		$template->render();
	}
}