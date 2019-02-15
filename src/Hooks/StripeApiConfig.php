<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Hooks;

use Nette\SmartObject;

/**
 * Class StripeApiConfig
 *
 * @property string|null $publishableApiKey Publish API key
 * @property string|null $secretApiKey Secret API key
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripeApiConfig
{
	use SmartObject;

	/** @var string|null */
	private $publishableApiKey;

	/** @var string|null */
	private $secretApiKey;

	protected function getPublishableApiKey(): ?string
	{
		return $this->publishableApiKey;
	}

	protected function setPublishableApiKey(?string $publishableApiKey): void
	{
		$this->publishableApiKey = $publishableApiKey;
	}

	protected function getSecretApiKey(): ?string
	{
		return $this->secretApiKey;
	}

	public function setSecretApiKey(?string $secretApiKey): void
	{
		$this->secretApiKey = $secretApiKey;
	}
}