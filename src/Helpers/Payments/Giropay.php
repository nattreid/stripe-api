<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers\Payments;

use NAttreid\StripeApi\Helpers\Payment;

/**
 * Class Giropay
 *
 * @author Attreid <attreid@gmail.com>
 */
class Giropay extends Payment
{

	public function getPaymentName(): string
	{
		return 'giropay';
	}
}