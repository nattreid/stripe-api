<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Hooks;

use NAttreid\StripeApi\StripeClient;
use NAttreid\WebManager\Services\Hooks\HookFactory;
use Nette\ComponentModel\Component;
use Nette\Forms\Controls\SubmitButton;

/**
 * Class StripeApiHook
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripeApiHook extends HookFactory
{

	/** @var IConfigurator */
	protected $configurator;

	/** @var StripeClient */
	private $client;

	public function setDependency(StripeClient $client): void
	{
		$this->client = $client;
	}

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

		$form->addText('publishableApiKey', 'webManager.web.hooks.stripeApi.publishableApiKey')
			->setDefaultValue($this->configurator->stripeApi->publishableApiKey);

		$form->addText('secretApiKey', 'webManager.web.hooks.stripeApi.secretApiKey')
			->setDefaultValue($this->configurator->stripeApi->secretApiKey);

		$form->addText('appleDomainAssocFile', 'webManager.web.hooks.stripeApi.appleDomainAssocFile')
			->setDefaultValue($this->configurator->stripeApi->appleDomainAssocFile);

		$form->addSubmit('save', 'form.save')
			->onClick[] = [$this, 'stripeApiSave'];

		$form->addSubmit('register', 'webManager.web.hooks.stripeApi.registerApplePayDomain')
			->setDisabled($this->client->isApplePayDomainRegistered())
			->onClick[] = [$this, 'registerApplePayDomain'];

		return $form;
	}

	public function stripeApiSave(SubmitButton $button): void
	{
		$values = $button->form->values;

		$config = $this->configurator->stripeApi;

		$config->publishableApiKey = $values->publishableApiKey ?: null;
		$config->secretApiKey = $values->secretApiKey ?: null;
		$config->appleDomainAssocFile = $values->appleDomainAssocFile ?: null;

		$this->configurator->stripeApi = $config;

		$this->flashNotifier->success('default.dataSaved');
	}

	public function registerApplePayDomain(): void
	{
		$this->client->registerApplePayDomain();
		$this->flashNotifier->success('webManager.web.hooks.stripeApi.applePayDomainRegistered');

		$this->onDataChange();
	}


}