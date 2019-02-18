<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers\Payments;

use NAttreid\StripeApi\Helpers\Payment;

/**
 * Class EPS
 *
 * @author Attreid <attreid@gmail.com>
 */
class EPS extends Payment
{

	public function getPaymentName(): string
	{
		return 'eps';
	}
}