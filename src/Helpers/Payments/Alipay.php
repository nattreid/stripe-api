<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Helpers\Payments;

use NAttreid\StripeApi\Helpers\Payment;

/**
 * Class Alipay
 *
 * @author Attreid <attreid@gmail.com>
 */
class Alipay extends Payment
{

	public function getPaymentName(): string
	{
		return 'alipay';
	}
}