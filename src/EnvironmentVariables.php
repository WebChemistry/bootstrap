<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

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

		$this->env = $values;
	}

	public function get(string|array $names, ?string $default): ?string
	{
		foreach ((array) $names as $name) {
			$name = strtoupper($name);

			if (isset($this->env[$name])) {
				return $this->env[$name];
			}
		}

		return $default;
	}

	public function has(string $name): bool
	{
		return isset($this->env[strtoupper($name)]);
	}

}
