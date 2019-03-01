<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Presenters;

use NAttreid\StripeApi\Hooks\StripeApiConfig;
use Nette\Application\UI\Presenter;

/**
 * Class StripePresenter
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripePresenter extends Presenter
{
	/** @var StripeApiConfig */
	private $config;

	public function setConfig(StripeApiConfig $config)
	{
		$this->config = $config;
	}

	public function renderAppleDomain(): void
	{
		if ($this->config->appleDomainAssocFile !== null) {
			header('Content-Type: text/plain');
			echo $this->config->appleDomainAssocFile;
			$this->terminate();
		} else {
			$this->error();
		}
	}
}