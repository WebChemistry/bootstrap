<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

final class Env
{

	/** @var array<string, string> */
	private array $env = [];

	/**
	 * @param array<string, string> $env
	 */
	public function __construct(array $env)
	{
		foreach ($env as $key => $value) {
			$this->env[strtoupper($key)] = $value;
		}
	}

	public static function fromNative(): Env
	{
		return new Env(getenv());
	}

	/**
	 * @param string|string[] $names
	 */
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
