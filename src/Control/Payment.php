<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use InvalidArgumentException;
use NAttreid\StripeApi\Helpers\AbstractPayment;
use NAttreid\StripeApi\Helpers\StripeException;
use Nette\Application\AbortException;
use Nette\Http\SessionSection;
use Nette\Utils\Json;

/**
 * Class Payment
 *
 * @author Attreid <attreid@gmail.com>
 */
class Payment extends AbstractControl
{
	private function getSession(): SessionSection
	{
		return $this->presenter->getSession('nattreid/stripe');
	}

	public function setPayment(AbstractPayment $payment): AbstractControl
	{
		if (!$payment instanceof \NAttreid\StripeApi\Helpers\Payment) {
			throw new InvalidArgumentException("Payment must be '\NAttreid\StripeApi\Helpers\Payment' class");
		}
		return parent::setPayment($payment);
	}

	/**
	 * @throws AbortException
	 */
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

	/**
	 * @throws AbortException
	 */
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

interface IPaymentFactory
{
	public function create(): Payment;
}