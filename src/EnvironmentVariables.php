<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use Nette\Utils\Arrays;

final class EnvironmentVariables
{

	/** @var array<string, string> */
	private array $env;

	/**
	 * @param array<string, string> $env
	 */
	public function __construct(?array $env = null)
	{
		if ($env === null) {
			$env = getenv();

			if (!is_array($env)) {
				$env = [];
			}
		}

		$values = [];
		foreach ($env as $key => $value) {
			$values[strtoupper($key)] = $value;
		}

		$this->env = $env;
	}

	public function get(string $name, string $default): string
	{
		return $this->env[strtoupper($name)] ?? $default;
	}

	public function getNullable(string $name): ?string
	{
		return $this->env[strtoupper($name)] ?? null;
	}

	/**
	 * @param string[] $names
	 */
	public function getOne(array $names, string $default): string
	{
		foreach ($names as $name) {
			if ($this->has($name)) {
				return $this->get($name, $default);
			}
		}

		return $default;
	}

	/**
	 * @param string[] $names
	 */
	public function getOneNullable(array $names): ?string
	{
		foreach ($names as $name) {
			if ($this->has($name)) {
				return $this->getNullable($name);
			}
		}

		return null;
	}

	public function has(string $name): bool
	{
		return isset($this->env[strtoupper($name)]);
	}

	/**
	 * @param mixed[] $names
	 */
	public function hasOne(array $names): bool
	{
		return Arrays::some($names, fn (string $name) => $this->has($name));
	}

}
