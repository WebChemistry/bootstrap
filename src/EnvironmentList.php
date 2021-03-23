<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use Nette\SmartObject;

/**
 * @internal
 */
final class EnvironmentList {

	use SmartObject;

	/** @var string[] */
	private array $environments;

	/** @var string[] */
	private array $values = [];

	/**
	 * @param string[] $names
	 */
	public function __construct(array $names = []) {
		$this->environments = $names;
	}

	public function setValues(array $values): self
	{
		$this->values = $values;

		return $this;
	}

	/**
	 * @return static
	 */
	public function addEnvironment(string $name) {
		$this->environments[] = $name;

		return $this;
	}

	public function resolve(): ?string {
		foreach ($this->environments as $name) {
			$value = getenv($name);

			if ($value !== false) {
				return $value;
			} elseif (isset($this->values[$name])) {
				return $this->values[$name];
			}
		}

		return null;
	}

}
