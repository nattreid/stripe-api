<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Presenters;

use NAttreid\StripeApi\Hooks\StripeApiConfig;
use Nette\Application\Responses\TextResponse;
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

	public function __construct(StripeApiConfig $config)
	{
		parent::__construct();
		$this->config = $config;
	}

	public function renderDomain(): void
	{
		if ($this->config->appleDomainAssocFile !== null) {
			$this->sendResponse(new TextResponse($this->config->appleDomainAssocFile));
		} else {
			$this->error();
		}
	}
}