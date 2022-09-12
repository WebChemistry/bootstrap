<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

final class BootstrapStringValue extends BootstrapValue
{

	public function __construct(
		private string $value,
	)
	{
	}

	public function getValue(Env $env): ?string
	{
		return $this->value;
	}

}
