<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use LogicException;

final class BootstrapValue
{

	/** @var string[] */
	private array $environments = [];

	private bool $disabled = false;

	private ?string $default = null;

	private ?string $value = null;

	private EnvironmentVariables $environment;

	public function __construct(
		private string $name,
		?EnvironmentVariables $environment = null,
	)
	{
		$this->environment = $environment ?? new EnvironmentVariables();
	}

	public function setDisabled(bool $disabled = true): self
	{
		$this->disabled = $disabled;

		return $this;
	}

	public function addEnvironment(string $name): self
	{
		$this->environments[] = $name;

		return $this;
	}

	public function clearEnvironments(): self
	{
		$this->environments = [];

		return $this;
	}

	public function setDefault(?string $default): self
	{
		$this->default = $default;

		return $this;
	}

	public function setValue(?string $value): self
	{
		$this->value = $value;

		return $this;
	}

	public function getValue(): string
	{
		if ($this->disabled) {
			throw new LogicException(sprintf('Required %s is disabled.', $this->name));
		}

		return $this->getValueNullable() ?? throw new LogicException(sprintf('Cannot resolve %s from ENV.', $this->name));
	}

	public function getValueNullable(): ?string
	{
		if ($this->disabled) {
			return null;
		}
		
		if ($this->value) {
			return $this->value;
		}

		return $this->environment->get($this->environments, $this->default);
	}

	public static function create(string $name, ?EnvironmentVariables $environment = null): self
	{
		return new self($name, $environment);
	}

}
