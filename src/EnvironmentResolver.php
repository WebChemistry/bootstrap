<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

final class EnvironmentResolver
{

	private bool $development;

	private bool $production;

	public function __construct(?string $value)
	{
		$this->production = $value ? in_array($value, ['production', 'prod', 'produc'], true) : false;
		$this->development = !$this->production;
	}

	public function isDevelopment(): bool
	{
		return $this->development;
	}

	public function isProduction(): bool
	{
		return $this->production;
	}

}
