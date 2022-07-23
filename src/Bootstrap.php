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
		private string $appDir,
		private string $wwwDir,
		private string $vendorDir,
		private string $tempDir,
		private string $environment,
		private ?string $logDir,
		private ?string $debugMode,
	)
	{
		$this->tracyEnabled = (bool) $this->logDir;
	}

	public function createConfigurator(): Configurator
	{
		$configurator = new Configurator(
			$this->appDir,
			$this->wwwDir,
			$this->vendorDir,
		);

		$configurator->setDebugMode(
			$this->getDebugMode() ?? $configurator::detectDebugMode()
		);

		if ($this->tracyEnabled) {
			$configurator->enableTracy($this->logDir);
		}

		$configurator->setTempDirectory($this->tempDir);

		foreach ($this->configs as $config) {
			$configurator->addConfig($config);
		}

		$configurator->addParameters([
			'environment' => [
				'production' => str_starts_with($this->environment, 'prod'),
				'development' => str_starts_with($this->environment, 'dev'),
				'value' => $this->environment,
			],
			'logDir' => $this->logDir,
		]);

		Arrays::invoke($this->onCreateConfigurator, $configurator);

		return $configurator;
	}

	public function addConfig(string $filePath, bool $variables = false): self
	{
		if ($variables) {
			$filePath = strtr(
				$filePath,
				[
					'%appDir%' => $this->appDir,
					'%wwwDir%' => $this->wwwDir,
					'%vendorDir%' => $this->vendorDir,
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
	public function setDebugMode(bool|string|array|null $debugMode): self
	{
		$this->debugModeResolved = $debugMode;

		return $this;
	}

	/**
	 * @return bool|string|string[]|null
	 */
	public function getDebugMode(): bool|string|array|null
	{
		if ($this->debugModeResolved === null) {
			$value = $this->debugMode;

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
