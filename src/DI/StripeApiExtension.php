<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\StripeApi\Hooks\StripeApiConfig;
use NAttreid\StripeApi\Hooks\StripeApiHook;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\Statement;

if (trait_exists('NAttreid\Cms\DI\ExtensionTranslatorTrait')) {
class StripeApiExtension extends AbstractStripeApiExtension
{
	use ExtensionTranslatorTrait;

	protected function prepareConfig(array $config)
	{
		$builder = $this->getContainerBuilder();
		$hook = $builder->getByType(HookService::class);
		if ($hook) {
			$builder->addDefinition($this->prefix('hook'))
				->setType(StripeApiHook::class);

			$this->setTranslation(__DIR__ . '/../lang/', [
				'webManager'
			]);

			return new Statement('?->stripeApi \?: new ' . StripeApiConfig::class, ['@' . Configurator::class]);
		} else {
			return parent::prepareConfig($config);
		}
	}
}
} else {
	class StripeApiExtension extends AbstractStripeApiExtension
	{
	}
}