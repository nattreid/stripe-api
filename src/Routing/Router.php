<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Routing;

use Nette\Application\Routers\Route;

/**
 * Router
 *
 * @author Attreid <attreid@gmail.com>
 */
class Router extends \NAttreid\Routing\Router
{

	public function createRoutes():void
	{
		$router = $this->getRouter();

		$router[] = new Route('.well-known/apple-developer-merchantid-domain-association', 'StripeApi:Stripe:appleDomain');
	}

}
