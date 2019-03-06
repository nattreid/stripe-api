<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

/**
 * Class CardElement
 *
 * @author Attreid <attreid@gmail.com>
 */
class CardElement extends AbstractCard
{

	public function render(string $text = 'Submit Payment', array $attrs = []): void
	{
		$template = $this->template;

		$template->setFile(__DIR__ . '/templates/cardElement.latte');

		$template->text = $text;
		$template->attrs = $attrs;

		parent::render();
	}
}

interface ICardElementFactory
{
	public function create(): CardElement;
}