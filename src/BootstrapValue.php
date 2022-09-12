<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

abstract class BootstrapValue
{

	abstract public function getValue(Env $env): ?string;

	public static function fromEnv(string $name): BootstrapEnvValue
	{
		return new BootstrapEnvValue($name);
	}

	public static function fromString(string $value): BootstrapStringValue
	{
		return new BootstrapStringValue($value);
	}

}
