<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use Nette\Configurator as NetteConfigurator;
use Nette\DI\ContainerLoader;

/**
 * @internal
 */
final class Configurator extends NetteConfigurator
{

	private string $appDir;

	private string $wwwDir;

	private string $vendorDir;

	private bool $productionContainer = false;

	public function __construct(string $appDir, string $wwwDir, string $vendorDir)
	{
		$this->appDir = $appDir;
		$this->wwwDir = $wwwDir;
		$this->vendorDir = $vendorDir;

		parent::__construct();
	}

	public function setProductionContainer(bool $value = true): self
	{
		$this->productionContainer = $value;

		return $this;
	}

	public function isProductionContainer(): bool
	{
		return $this->productionContainer;
	}

	public function loadContainer(): string
	{
		if ($this->productionContainer) {
			$loader = new ContainerLoader($this->getCacheDirectory() . '/nette.configurator');

			return $loader->load([$this, 'generateContainer']);
		}

		return parent::loadContainer();
	}

	/**
	 * @return mixed[]
	 */
	protected function getDefaultParameters(): array
	{
		return [
			'appDir' => $this->appDir,
			'wwwDir' => $this->wwwDir,
			'vendorDir' => $this->vendorDir,
			'debugMode' => false,
			'productionMode' => true,
			'consoleMode' => PHP_SAPI === 'cli',
		];
	}

}
