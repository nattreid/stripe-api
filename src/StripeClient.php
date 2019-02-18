<?php

declare(strict_types=1);

namespace NAttreid\StripeApi;

use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use Stripe\ApiResource;
use Stripe\Charge;
use Stripe\Stripe;

/**
 * Class StripeClient
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripeClient
{
	/** @var StripeApiConfig */
	private $config;

	public function __construct(StripeApiConfig $config)
	{
		$this->config = $config;
		Stripe::setApiKey($config->secretApiKey);
	}

	public function charge(string $source, AbstractPayment $payment): ApiResource
	{
		$data = $payment->getChargeData();
		$data['source'] = $source;
		return Charge::create($data);
	}
}