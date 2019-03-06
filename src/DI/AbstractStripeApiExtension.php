<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\DI;

use NAttreid\Routing\RouterFactory;
use NAttreid\StripeApi\Control\CardElement;
use NAttreid\StripeApi\Control\CardPayment;
use NAttreid\StripeApi\Control\ICardElementFactory;
use NAttreid\StripeApi\Control\ICardPaymentFactory;
use NAttreid\StripeApi\Control\IMasterPassButtonFactory;
use NAttreid\StripeApi\Control\IPayButtonFactory;
use NAttreid\StripeApi\Control\IPaymentFactory;
use NAttreid\StripeApi\Control\MasterPassButton;
use NAttreid\StripeApi\Control\PayButton;
use NAttreid\StripeApi\Control\Payment;
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
		'debug' => false,
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
			->setImplement(IPayButtonFactory::class)
			->setFactory(PayButton::class)
			->setArguments([$config['debug'], $stripeApi]);

		$builder->addDefinition($this->prefix('payment'))
			->setImplement(IPaymentFactory::class)
			->setFactory(Payment::class)
			->setArguments([$config['debug'], $stripeApi]);

		$builder->addDefinition($this->prefix('masterPassButton'))
			->setImplement(IMasterPassButtonFactory::class)
			->setFactory(MasterPassButton::class)
			->setArguments([$config['debug'], $stripeApi]);

		$builder->addDefinition($this->prefix('cardPayment'))
			->setImplement(ICardPaymentFactory::class)
			->setFactory(CardPayment::class)
			->setArguments([$config['debug'], $stripeApi]);

		$builder->addDefinition($this->prefix('cardElement'))
			->setImplement(ICardElementFactory::class)
			->setFactory(CardElement::class)
			->setArguments([$config['debug'], $stripeApi]);

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