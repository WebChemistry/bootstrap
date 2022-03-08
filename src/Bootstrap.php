<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use InvalidArgumentException;
use Nette\Utils\Arrays;

final class Bootstrap
{

	private bool $tracyEnabled;

	/** @var bool|string|string[]|null */
	private bool|string|array|null $debugModeResolved = null;

	/** @var string[] */
	private array $configs = [];

	/** @var callable[] */
	public array $onCreateConfigurator = [];

	public function __construct(
		private BootstrapDirectories $projectDirectories,
		private EnvironmentValue $tempDir,
		private EnvironmentValue $logDir,
		private EnvironmentValue $environment,
		private EnvironmentValue $debugMode,
	)
	{
		$this->tracyEnabled = (bool) $this->logDir->getValueNullable();
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

		if ($this->tracyEnabled) {
			$configurator->enableTracy($this->logDir->getValue());
		}

		$configurator->setTempDirectory($this->tempDir->getValue());

		foreach ($this->configs as $config) {
			$configurator->addConfig($config);
		}

		$environment = $this->environment->getValue();
		$configurator->addParameters([
			'environment' => [
				'production' => str_starts_with($environment, 'prod'),
				'development' => str_starts_with($environment, 'dev'),
				'value' => $environment,
			],
			'logDir' => $this->logDir->getValueNullable(),
		]);

		Arrays::invoke($this->onCreateConfigurator, $configurator);

		return $configurator;
	}

	public function getLogDir(): EnvironmentValue
	{
		return $this->logDir;
	}

	public function getTempDir(): EnvironmentValue
	{
		return $this->tempDir;
	}

	public function getEnvironment(): EnvironmentValue
	{
		return $this->environment;
	}

	public function addConfig(string $filePath, bool $variables = false): self
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
	 */
	public function setDebugMode($debugMode): self
	{
		$this->debugModeResolved = $debugMode;

		return $this;
	}

	public function getProjectDirectories(): BootstrapDirectories
	{
		return $this->projectDirectories;
	}

	/**
	 * @return bool|string|string[]|null
	 */
	public function getDebugMode(): bool|string|array|null
	{
		if ($this->debugModeResolved === null) {
			$value = $this->debugMode->getValueNullable();

			if ($value === '1') {
				$this->debugModeResolved = true;
			} elseif ($value === '0') {
				$this->debugModeResolved = false;
			} else {
				$this->debugModeResolved = $value;
			}
		}

		return $this->debugModeResolved;
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

}
