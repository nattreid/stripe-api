<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use NAttreid\StripeApi\Helpers\StripeException;
use Nette\Http\SessionSection;
use Nette\Utils\Json;

/**
 * Class StripePayment
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripePayment extends AbstractControl
{
	private function getSession(): SessionSection
	{
		return $this->presenter->getSession('nattreid/stripe');
	}

	public function handleAuthorize(): void
	{
		$session = $this->getSession();

		$json = $this->presenter->request->getParameter('json');

		try {
			$source = Json::decode($json);
			$session->source = $source->id;
			$session->client_secret = $source->client_secret;
			$session->livemode = $source->livemode;

			$message = 'OK';
		} catch (\Exception $ex) {
			$this->onError($ex);
			$message = 'ERROR';
		}

		$this->presenter->payload->message = $message;
		$this->presenter->sendPayload();
	}

	public function handleCharge(): void
	{
		$session = $this->getSession();

		$client_secret = $this->presenter->request->getParameter('client_secret');
		$livemode = $this->presenter->request->getParameter('livemode');
		$source = $this->presenter->request->getParameter('source');

		try {
			if (
				$session->source === $source &&
				$session->client_secret === $client_secret &&
				$session->livemode === ($livemode === 'true')
			) {
				$session->remove();

				$charge = $this->charge($source);

				switch ($charge->status) {
					default:
						throw new StripeException('Charge status: ' . $charge->status);
					case 'succeeded':
						$this->onSuccess($charge);
						$url = $this->successUrl;

				}
			} else {
				throw new StripeException('Incorrect credentials');
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

		$template->setFile(__DIR__ . '/templates/payment.latte');
		parent::render();
	}
}

interface IStripePaymentFactory
{
	public function create(): StripePayment;
}