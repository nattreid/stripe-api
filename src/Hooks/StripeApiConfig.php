<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Hooks;

use Nette\SmartObject;

/**
 * Class StripeApiConfig
 *
 * @property string|null $publishableApiKey Publish API key
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripeApiConfig
{
	use SmartObject;

	/** @var string|null */
	private $publishableApiKey;

	protected function getPublishableApiKey(): ?string
	{
		return $this->publishableApiKey;
	}

	protected function setPublishableApiKey(?string $publishableApiKey): void
	{
		$this->publishableApiKey = $publishableApiKey;
	}
}