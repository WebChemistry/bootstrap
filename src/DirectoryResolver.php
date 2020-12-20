<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use InvalidArgumentException;

final class DirectoryResolver
{

	private string $default;

	private EnvironmentList $environments;

	public function __construct(string $default)
	{
		$this->default = $default;
		$this->environments = new EnvironmentList();

		if (!$this->default || $this->default[0] !== '/') {
			throw new InvalidArgumentException(
				sprintf('Default path must be an non-empty absolute path, %s given', $this->default)
			);
		}
	}

	public function addEnvironment(string $name): self
	{
		$this->environments->addEnvironment($name);

		return $this;
	}

	public function resolve(): string
	{
		$value = $this->environments->resolve();
		if ($value !== null) {
			if (!$value || $value[0] !== '/') {
				throw new InvalidArgumentException(sprintf('Path must be an absolute path, %s given', $value));
			}

			return $value;
		}

		return $this->default;
	}

	public static function create(string $default): self
	{
		return new self($default);
	}

}
