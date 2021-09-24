<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use InvalidArgumentException;
use LogicException;
use Nette\Utils\Arrays;

final class Bootstrap
{

	private BootstrapDirectories $projectDirectories;

	private DirectoryResolver $tempDir;

	private ?DirectoryResolver $logDir;

	private bool $tracyEnabled;

	private array $environment = [];

	/** @var bool|string|string[]|null */
	private $debugMode;

	/** @var string[] */
	private array $configs = [];
	
	/** @var callable[] */
	public array $onCreateConfigurator = [];

	public function __construct(
		BootstrapDirectories $projectDirectories,
		DirectoryResolver $tempDir,
		?DirectoryResolver $logDir = null
	)
	{
		$this->tempDir = $tempDir;
		$this->logDir = $logDir;
		$this->projectDirectories = $projectDirectories;
		$this->tracyEnabled = (bool) $logDir;
	}

	public function setEnvironmentFile(string $file): self
	{
		if (is_file($file)) {
			$array = require $file;
			if (!is_array($array)) {
				throw new LogicException(sprintf('Environment file %s must return an array.'));
			}

			$this->environment = array_map('strval', $array);

			if ($this->logDir) {
				$this->logDir->setStaticEnvironments($this->environment);
			}
			
			$this->tempDir->setStaticEnvironments($this->environment);
		}

		return $this;
	}

	public function createConfigurator(): Configurator
	{
		$configurator = new Configurator(
			$this->projectDirectories->getAppDir(),
			$this->projectDirectories->getWwwDir(),
			$this->projectDirectories->getVendorDir()
		);

		$configurator->setDebugMode(
			$this->getDebugMode() ?? $configurator::detectDebugMode()
		);

		if ($this->isTracyEnabled()) {
			$configurator->enableTracy($this->getLogDir());
		}

		$configurator->setTempDirectory($this->getTempDir());

		foreach ($this->configs as $config) {
			$configurator->addConfig($config);
		}

		$env = new EnvironmentResolver($envString = $this->getEnvironment());
		$configurator->addParameters([
			'environment' => [
				'production' => $env->isProduction(),
				'development' => $env->isDevelopment(),
				'value' => $envString,
			],
			'logDir' => $this->logDir ? $this->logDir->resolve() : null,
		]);
		
		Arrays::invoke($this->onCreateConfigurator, $configurator);

		return $configurator;
	}

	/**
	 * @return static
	 */
	public function addConfig(string $filePath, bool $variables = false)
	{
		if ($variables) {
			$filePath = strtr(
				$filePath,
				[
					'%appDir%' => $this->projectDirectories->getAppDir(),
					'%wwwDir%' => $this->projectDirectories->getWwwDir(),
					'%vendorDir%' => $this->projectDirectories->getVendorDir(),
				]
			);
		}

		if (!is_file($filePath)) {
			throw new InvalidArgumentException(sprintf('File %s not exists', $filePath));
		}

		$this->configs[] = $filePath;

		return $this;
	}

	/**
	 * @param bool|string|string[]|null $debugMode
	 * @return static
	 */
	public function setDebugMode($debugMode)
	{
		$this->debugMode = $debugMode;

		return $this;
	}

	public function hasLogDir(): bool
	{
		return (bool) $this->logDir;
	}

	public function getLogDir(): string
	{
		if (!$this->logDir) {
			throw new LogicException('Log directory is not set');
		}

		return $this->logDir->resolve();
	}

	/**
	 * @return static
	 */
	public function disableLogDir()
	{
		$this->logDir = null;

		return $this;
	}

	public function getTempDirSource(): DirectoryResolver
	{
		return $this->tempDir;
	}

	public function getLogDirSource(): ?DirectoryResolver
	{
		return $this->logDir;
	}

	public function getTempDir(): string
	{
		return $this->tempDir->resolve();
	}

	public function getProjectDirectories(): BootstrapDirectories
	{
		return $this->projectDirectories;
	}

	/**
	 * @return bool|string|string[]|null
	 */
	public function getDebugMode()
	{
		if ($this->debugMode === null) {
			$value = (new EnvironmentList(['NETTE_DEBUG_MODE', 'DEBUG_MODE']))
				->setStaticEnvironments($this->environment)
				->resolve();
			
			if ($value !== null) {
				if ($value === '1') {
					$this->debugMode = true;
				} elseif ($value === '0') {
					$this->debugMode = false;
				} else {
					$this->debugMode = $value;
				}
			}
		}

		return $this->debugMode;
	}

	public function disableTracy(): self
	{
		$this->tracyEnabled = false;

		return $this;
	}

	public function isTracyEnabled(): bool
	{
		return $this->tracyEnabled;
	}

	public function getEnvironment(): ?string
	{
		$value = (new EnvironmentList(['NETTE_ENVIRONMENT', 'ENVIRONMENT']))
			->setStaticEnvironments($this->environment)
			->resolve();

		return $value ?? 'dev';
	}

	public function debug(bool $barDump = false): void
	{
		$options = [
			'tempDir' => $this->getTempDir(),
			'logDir' => $this->logDir ? $this->getLogDir() : null,
			'environment' => $this->getEnvironment(),
			'debugMode' => $this->getDebugMode(),
			'tracyEnabled' => $this->isTracyEnabled(),
		];

		if ($barDump) {
			bdump($options);
		} else {
			dump($options);
		}
	}

}
