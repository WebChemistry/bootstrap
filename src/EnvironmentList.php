<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

/**
 * @internal
 */
final class EnvironmentList 
{

	/** @var string[] */
	private array $environments;

	private ?string $forceValue = null;
	
	/** @var string[] */
	private array $values = [];
	
	private ?string $default = null;

	/**
	 * @param string[] $names
	 */
	public function __construct(array $names = [], ?string $default = null) 
	{
		$this->environments = $names;
	}

	public function setDefault(?string $default): self
	{
		$this->default = $default;
		
		return $this;
	}

	public function setForceValue(string $value): self
	{
		$this->forceValue = $value; 
		
		return $this;
	}
	
	public function setStaticEnvironments(array $values): self
	{
		$this->values = $values;
		
		return $this;
	}

	/**
	 * @return static
	 */
	public function addEnvironmentName(string $name) {
		$this->environments[] = $name;

		return $this;
	}

	public function resolve(): ?string {
		if ($this->forceValue) {
			return $this->forceValue;
		}
		
		foreach ($this->environments as $name) {
			if (isset($this->values[$name])) {
				return $this->values[$name];
			}
			
			$value = getenv($name);

			if ($value !== false) {
				return $value;
			}
		}

		return $this->default;
	}

	/**
	 * @return string[]
	 */
	public function getNames(): array
	{
		return $this->environments;
	}

}
