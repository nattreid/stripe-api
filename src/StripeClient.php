<?php

declare(strict_types=1);

namespace NAttreid\StripeApi;

use NAttreid\StripeApi\Hooks\StripeApiConfig;

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
	}
}