<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Hooks;

use NAttreid\Form\Form;
use NAttreid\WebManager\Services\Hooks\HookFactory;
use Nette\ComponentModel\Component;
use Nette\Utils\ArrayHash;

/**
 * Class StripeApiHook
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripeApiHook extends HookFactory
{

	/** @var IConfigurator */
	protected $configurator;

	public function init(): void
	{
		if (!$this->configurator->stripeApi) {
			$this->configurator->stripeApi = new StripeApiConfig();
		}
	}

	/** @return Component */
	public function create(): Component
	{
		$form = $this->formFactory->create();
		$form->setAjaxRequest();

		$form->addText('publishableApiKey', 'webManager.web.hooks.stripeApi.publishableApiKey')
			->setDefaultValue($this->configurator->stripeApi->publishableApiKey);

		$form->addText('secretApiKey', 'webManager.web.hooks.stripeApi.secretApiKey')
			->setDefaultValue($this->configurator->stripeApi->secretApiKey);

		$form->addSubmit('save', 'form.save');

		$form->onSuccess[] = [$this, 'stripeApiFormSucceeded'];

		return $form;
	}

	public function stripeApiFormSucceeded(Form $form, ArrayHash $values): void
	{
		$config = $this->configurator->stripeApi;

		$config->publishableApiKey = $values->publishableApiKey ?: null;
		$config->secretApiKey = $values->secretApiKey ?: null;

		$this->configurator->stripeApi = $config;

		$this->flashNotifier->success('default.dataSaved');
	}
}