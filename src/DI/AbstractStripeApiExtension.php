<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\DI;

use NAttreid\StripeApi\Control\IStripePayButtonFactory;
use NAttreid\StripeApi\Control\IStripePaymentFactory;
use NAttreid\StripeApi\Control\StripePayButton;
use NAttreid\StripeApi\Control\StripePayment;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use NAttreid\StripeApi\StripeClient;
use Nette\DI\CompilerExtension;

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
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$stripeApi = $this->prepareConfig($config);

		$builder->addDefinition($this->prefix('client'))
			->setType(StripeClient::class)
			->setArguments([$stripeApi]);

		$builder->addDefinition($this->prefix('payButton'))
			->setImplement(IStripePayButtonFactory::class)
			->setFactory(StripePayButton::class)
			->setArguments([$stripeApi]);

		$builder->addDefinition($this->prefix('payment'))
			->setImplement(IStripePaymentFactory::class)
			->setFactory(StripePayment::class)
			->setArguments([$stripeApi]);
	}

	protected function prepareConfig(array $config)
	{
		$builder = $this->getContainerBuilder();
		return $builder->addDefinition($this->prefix('config'))
			->setFactory(StripeApiConfig::class)
			->addSetup('$publishableApiKey', [$config['publishableApiKey']])
			->addSetup('$secretApiKey', [$config['secretApiKey']]);
	}
}