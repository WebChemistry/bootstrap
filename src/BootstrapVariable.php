<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use LogicException;

final class BootstrapVariable
{

	/** @var BootstrapValue[] */
	private array $stack = [];

	private bool $disabled = false;

	public function __construct(
		private string $name,
		private Env $env,
	)
	{
	}

	public function setDisabled(bool $disabled = true): self
	{
		$this->disabled = $disabled;

		return $this;
	}

	public function getValue(): string
	{
		if ($this->disabled) {
			throw new LogicException(sprintf('Required %s is disabled.', $this->name));
		}

		return $this->getValueNullable() ?? throw new LogicException(sprintf('Cannot resolve %s.', $this->name));
	}

	public function getValueNullable(): ?string
	{
		if ($this->disabled) {
			return null;
		}

		foreach ($this->stack as $value) {
			$result = $value->getValue($this->env);

			if ($result !== null) {
				return $result;
			}
		}

		return null;
	}

	public function append(BootstrapValue $value): self
	{
		$this->stack[] = $value;

		return $this;
	}

	public function prepend(BootstrapValue $value): self
	{
		array_unshift($this->stack, $value);

		return $this;
	}

	public static function create(string $name, Env $env): self
	{
		return new self($name, $env);
	}

}
