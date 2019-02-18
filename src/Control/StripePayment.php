<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

use NAttreid\StripeApi\Helpers\StripeException;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Nette\Utils\Json;
use Tracy\Debugger;

/**
 * Class StripePayment
 *
 * @author Attreid <attreid@gmail.com>
 */
class StripePayment extends AbstractControl
{
	public function handleAuthorize(): void
	{
		$response = new Response();
		try {
			$input = @file_get_contents('php://input');
			$json = Json::decode($input);
			Debugger::barDump($json);
			if ($json->source === 'chargeable') {
				$charge = $this->charge($json->source);
				$this->onSuccess($charge);
				$response->setCode(IResponse::S200_OK);
			} else {
				throw new StripeException('Charge status: ' . $json->source);
			}
		} catch (\Exception $ex) {
			$this->onError($ex);
			$response->setCode(IResponse::S500_INTERNAL_SERVER_ERROR);
		}
		$this->sendResponse($response);
	}

	/**
	 * @throws StripeException
	 */
	public function render(): void
	{
		$template = $this->template;

		$template->setFile(__DIR__ . '/templates/payment.latte.latte');
		parent::render();
	}
}

interface IStripePaymentFactory
{
	public function create(): StripePayment;
}