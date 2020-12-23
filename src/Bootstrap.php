<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use InvalidArgumentException;
use LogicException;
use Nette\SmartObject;

final class Bootstrap
{

	use SmartObject;

	private BootstrapDirectories $projectDirectories;

	private DirectoryResolver $tempDir;

	private ?DirectoryResolver $logDir;

	private bool $tracyEnabled;

	/** @var bool|string|string[]|null */
	private $debugMode;

	/** @var string[] */
	private array $configs = [];

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

		$configurator->addParameters([
			'env' => $this->getEnvironment(),
			'logDir' => $this->logDir ? $this->logDir->resolve() : null,
		]);

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
			$value = (new EnvironmentList(['NETTE_DEBUG_MODE', 'DEBUG_MODE']))->resolve();
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
		$value = (new EnvironmentList(['NETTE_ENVIRONMENT', 'ENVIRONMENT']))->resolve();

		return $value ?? 'dev';
	}

	/**
	 * @return static
	 */
	public function kubernetesEnvironment()
	{
		if (isset($_SERVER['HTTP_X_REAL_IP'])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
		}
		if (isset($_SERVER['KUBERNETES_SERVICE_PORT'])) {
			$_SERVER['SERVER_PORT'] = $_SERVER['KUBERNETES_SERVICE_PORT'];
		}

		return $this;
	}

}
