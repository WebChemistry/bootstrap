<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap\DI;

use LogicException;
use Nette\DI\CompilerExtension;
use WebChemistry\Bootstrap\ProjectDirectories;

final class BootstrapExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('projectDirectories'))
			->setFactory(ProjectDirectories::class, $this->getParameters());
	}

	private function getParameters(): array
	{
		$builder = $this->getContainerBuilder();

		$args = [];
		foreach (['wwwDir', 'vendorDir', 'appDir', 'tempDir'] as $item) {
			if (!isset($builder->parameters[$item])) {
				throw new LogicException(sprintf('Container parameter %s not exists', $item));
			}
			$args[] = $builder->parameters[$item];
		}

		if (isset($builder->parameters['logDir'])) {
			$args[] = $builder->parameters['logDir'];
		}

		return $args;
	}

}
