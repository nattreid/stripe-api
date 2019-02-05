<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use NAttreid\StripeApi\Helpers\PaymentRequest;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use Nette\Application\UI\Control;
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

	/** @var PaymentRequest */
	private $payment;

	public function __construct(StripeApiConfig $config)
	{
		parent::__construct();
		$this->config = $config;
	}

	public function handleReceiveToken(): void
	{
		$json = $this->presenter->getHttpRequest()->getPost('token');
		try {
			$token = Json::decode($json);
			$this->onSuccess($token);
		} catch (\Exception $ex) {
			$this->onError($ex);
		}
	}

	public function setPayment(PaymentRequest $payment): self
	{
		$this->payment = $payment;
		return $this;
	}

	public function render(): void
	{
		$this->template->key = $this->config->publishableApiKey;
		$this->template->payment = $this->payment->getData();
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}
}

interface IStripePayButtonFactory
{
	public function create(): StripePayButton;
}