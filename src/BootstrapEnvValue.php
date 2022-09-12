<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

final class BootstrapEnvValue extends BootstrapValue
{

	public function __construct(
		private string $name,
	)
	{
	}

	public function getValue(Env $env): ?string
	{
		return $env->get($this->name, null);
	}

}
