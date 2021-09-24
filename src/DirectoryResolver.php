<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use InvalidArgumentException;
use LogicException;
use Nette\Utils\FileSystem;
use function Swoole\Coroutine\Http\request;

final class DirectoryResolver
{

	private EnvironmentList $environments;

	public function __construct()
	{
		$this->environments = new EnvironmentList();
	}

	public function setDefault(string $default): self
	{
		$this->environments->setDefault($default);

		return $this;
	}

	public function setForceValue(string $value): self
	{
		$this->environments->setForceValue($value);

		return $this;
	}

	public function setStaticEnvironments(array $values): self
	{
		$this->environments->setStaticEnvironments($values);

		return $this;
	}

	public function addEnvironmentName(string $name): self
	{
		$this->environments->addEnvironmentName($name);

		return $this;
	}

	public function resolve(): string
	{
		$value = $this->environments->resolve();
		if (!$value) {
			throw new LogicException('Default directory or fixed directory or environment is not set.');
		}
		
		if (!FileSystem::isAbsolute($value)) {
			throw new InvalidArgumentException(sprintf('Path must be an absolute path, %s given', $value));
		}

		return $value;
	}

	public static function create(): self
	{
		return new self();
	}

}
