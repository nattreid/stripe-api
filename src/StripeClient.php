<?php

declare(strict_types=1);

namespace NAttreid\StripeApi;

use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use Nette\Http\Request;
use Stripe\ApiResource;
use Stripe\ApplePayDomain;
use Stripe\Charge;
use Stripe\Source;
use Stripe\Stripe;

/**
 * Class StripeClient
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripeClient
{
	private const REGISTERED = 'applePayDomainRegistered';

	/** @var string */
	private $tempDir;

	/** @var StripeApiConfig */
	private $config;

	/** @var Request */
	private $request;

	public function __construct(string $tempDir, StripeApiConfig $config, Request $request)
	{
		$this->tempDir = $tempDir;
		$this->config = $config;
		$this->request = $request;
		Stripe::setApiKey($config->secretApiKey);
	}

	public function isApplePayDomainRegistered(): bool
	{
		return file_exists($this->tempDir . DIRECTORY_SEPARATOR . self::REGISTERED);
	}

	public function registerApplePayDomain(string $domain = null): ApiResource
	{
		if ($domain === null) {
			$domain = $this->request->getUrl()->getDomain(3);
		}
		$response = ApplePayDomain::create([
			'domain_name' => $domain
		]);

		@file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . self::REGISTERED, $domain);

		return $response;
	}

	public function charge(string $source, AbstractPayment $payment): ApiResource
	{
		$data = $payment->getChargeData();
		$data['source'] = $source;
		return Charge::create($data);
	}

	public function createSource(string $verifier, AbstractPayment $payment): ApiResource
	{
		return Source::create($payment->getSource($verifier));
	}
}