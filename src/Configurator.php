<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use Nette\Configurator as NetteConfigurator;

/**
 * @internal
 */
final class Configurator extends NetteConfigurator
{

	private string $appDir;

	private string $wwwDir;

	private string $vendorDir;

	public function __construct(string $appDir, string $wwwDir, string $vendorDir)
	{
		$this->appDir = $appDir;
		$this->wwwDir = $wwwDir;
		$this->vendorDir = $vendorDir;

		parent::__construct();
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
