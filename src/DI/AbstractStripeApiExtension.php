<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\DI;

use NAttreid\Routing\RouterFactory;
use NAttreid\StripeApi\Control\IStripePayButtonFactory;
use NAttreid\StripeApi\Control\IStripePaymentFactory;
use NAttreid\StripeApi\Control\StripePayButton;
use NAttreid\StripeApi\Control\StripePayment;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use NAttreid\StripeApi\Hooks\StripeApiHook;
use NAttreid\StripeApi\Presenters\StripePresenter;
use NAttreid\StripeApi\Routing\Router;
use NAttreid\StripeApi\StripeClient;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\DI\MissingServiceException;

/**
 * Class AbstractStripeApiExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class AbstractStripeApiExtension extends CompilerExtension
{
	private $defaults = [
		'publishableApiKey' => null,
		'secretApiKey' => null,
		'appleDomainAssocFile' => null,
		'tempDir' => '%tempDir%'
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$config['tempDir'] = Helpers::expand($config['tempDir'], $builder->parameters);

		$stripeApi = $this->prepareConfig($config);

		$builder->addDefinition($this->prefix('client'))
			->setType(StripeClient::class)
			->setArguments([$config['tempDir'], $stripeApi]);

		$builder->addDefinition($this->prefix('payButton'))
			->setImplement(IStripePayButtonFactory::class)
			->setFactory(StripePayButton::class)
			->setArguments([$stripeApi]);

		$builder->addDefinition($this->prefix('payment'))
			->setImplement(IStripePaymentFactory::class)
			->setFactory(StripePayment::class)
			->setArguments([$stripeApi]);

		$builder->addDefinition($this->prefix('router'))
			->setType(Router::class);

		$builder->addDefinition($this->prefix('presenter'))
			->setType(StripePresenter::class)
			->addSetup('setConfig', [$stripeApi]);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		try {
			$router = $builder->getByType(RouterFactory::class);
			$builder->getDefinition($router)
				->addSetup('addRouter', ['@' . $this->prefix('router'), RouterFactory::PRIORITY_APP]);

			$builder->getDefinition('application.presenterFactory')
				->addSetup('setMapping', [
					['StripeApi' => 'NAttreid\StripeApi\Presenters\*Presenter']
				]);

			$hook = $builder->getByType(StripeApiHook::class);
			$builder->getDefinition($hook)
				->addSetup('setDependency', [
					'@' . $this->prefix('client'),
				]);
		} catch (MissingServiceException $ex) {
		}
	}

	protected function prepareConfig(array $config)
	{
		$builder = $this->getContainerBuilder();
		return $builder->addDefinition($this->prefix('config'))
			->setFactory(StripeApiConfig::class)
			->addSetup('$publishableApiKey', [$config['publishableApiKey']])
			->addSetup('$secretApiKey', [$config['secretApiKey']])
			->addSetup('$appleDomainAssocFile', [$config['appleDomainAssocFile']]);
	}
}