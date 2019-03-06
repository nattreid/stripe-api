<?php

declare(strict_types=1);

namespace NAttreid\StripeApi\Control;

/**
 * Class CardPayment
 *
 * @author Attreid <attreid@gmail.com>
 */
class CardPayment extends AbstractCard
{
	/** @var string */
	private $title;

	/** @var string */
	private $description;

	/** @var bool */
	private $allowRemember = true;

	public function setTitle(string $title): self
	{
		$this->title = $title;
		return $this;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	public function setAllowRemember(bool $allowRemember): void
	{
		$this->allowRemember = $allowRemember;
	}

	public function render(string $text = 'Pay with Card'): void
	{
		$template = $this->template;

		$template->title = $this->title;
		$template->description = $this->description;
		$template->allowRemember = $this->allowRemember;
		$template->text = $text;

		$template->setFile(__DIR__ . '/templates/cardPayment.latte');

		parent::render();
	}
}

interface ICardPaymentFactory
{
	public function create(): CardPayment;
}