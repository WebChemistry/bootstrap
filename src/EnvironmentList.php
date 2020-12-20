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

	/**
	 * @param string[] $names
	 */
	public function __construct(array $names = []) {
		$this->environments = $names;
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
			}
		}

		return null;
	}

}
