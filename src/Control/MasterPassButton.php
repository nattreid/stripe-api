<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use NAttreid\StripeApi\Helpers\StripeException;
use Nette\Application\AbortException;

/**
 * Class MasterPassButton
 *
 * @author Attreid <attreid@gmail.com>
 */
class MasterPassButton extends AbstractControl
{
	private const
		SANDBOX_URL = 'https://sandbox.masterpass.com/integration/merchant.js',
		PRODUCTION_URL = 'https://masterpass.com/integration/merchant.js';

	/**
	 * @throws AbortException
	 */
	public function handleCharge(): void
	{
		$oauth_verifier = $this->presenter->request->getParameter('oauth_verifier');

		try {
			$source = $this->createSource($oauth_verifier);
			$charge = $this->charge((string) $source->id);

			switch ($charge->status) {
				default:
					throw new StripeException('Charge status: ' . $charge->status);
				case 'succeeded':
					$this->onSuccess($charge);
					$url = $this->successUrl;

			}

		} catch (\Exception $ex) {
			$this->onError($ex);
			$url = $this->errorUrl;
		}
		$this->presenter->redirectUrl($url);
	}

	/**
	 * @throws StripeException
	 */
	public function render(): void
	{
		$template = $this->template;
		$template->url = $this->debug ? self::SANDBOX_URL : self::PRODUCTION_URL;

		$template->setFile(__DIR__ . '/templates/masterpassButton.latte');
		parent::render();
	}
}

interface IMasterPassButtonFactory
{
	public function create(): MasterPassButton;
}