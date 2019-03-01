<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Hooks;

use Nette\SmartObject;

/**
 * Class StripeApiConfig
 *
 * @property string|null $publishableApiKey Publish API key
 * @property string|null $secretApiKey Secret API key
 * @property string|null $appleDomainAssocFile Domain association file content
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

	/** @var string|null */
	private $appleDomainAssocFile;

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

	protected function setSecretApiKey(?string $secretApiKey): void
	{
		$this->secretApiKey = $secretApiKey;
	}

	protected function getAppleDomainAssocFile(): ?string
	{
		return $this->appleDomainAssocFile;
	}

	protected function setAppleDomainAssocFile(?string $appleDomainAssocFile): void
	{
		$this->appleDomainAssocFile = $appleDomainAssocFile;
	}
}