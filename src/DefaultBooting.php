<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use Nette\Configurator;
use ReflectionClass;

abstract class DefaultBooting
{

	/** @var string[] */
	protected array $configs = [];

	private Bootstrap $bootstrap;

	private string $appPath;

	protected ?string $defaultLogDirectory = null;

	protected ?string $defaultTempDirectory = null;

	public function __construct(
		protected string $localConfig,
	)
	{
	}

	public function setLocalConfig(string $localConfig): static
	{
		$this->localConfig = $localConfig;

		return $this;
	}

	public function boot(): Configurator
	{
		return $this->createConfigurator();
	}

	public function bootCron(): Configurator
	{
		$this->getBootstrap()->disableTracy();

		return $this->createConfigurator();
	}

	public function bootConsole(bool $tracy = true): Configurator
	{
		$this->getBootstrap()->setDebugMode(true);

		if (!$tracy) {
			$this->getBootstrap()->disableTracy();
		}

		return $this->createConfigurator();
	}

	public function getBootstrap(): Bootstrap
	{
		if (!isset($this->bootstrap)) {
			$this->bootstrap = new Bootstrap(
				$this->createBootstrapDirectories(),
				$this->createTempDirectoryResolver(),
				$this->createLogDirectoryResolver(),
			);

			foreach ($this->configs as $config) {
				$this->bootstrap->addConfig($config);
			}

			$this->bootstrap->addConfig($this->localConfig, true);

			$this->onBootstrapCreated($this->bootstrap);
		}

		return $this->bootstrap;
	}

	public function setDefaultLogDirectory(?string $defaultLogDirectory): static
	{
		$this->defaultLogDirectory = $defaultLogDirectory;

		return $this;
	}

	public function setDefaultTempDirectory(?string $defaultTempDirectory): static
	{
		$this->defaultTempDirectory = $defaultTempDirectory;

		return $this;
	}

	protected function getAppPath(): string
	{
		if (!isset($this->appPath)) {
			$reflection = new ReflectionClass(static::class);
			$this->appPath = dirname($reflection->getFileName());
		}

		return $this->appPath;
	}

	protected function getDefaultLogDirectory(): string
	{
		return $this->defaultLogDirectory ?: $this->getAppPath() . '/../log';
	}

	protected function getDefaultTempDirectory(): string
	{
		return $this->defaultTempDirectory ?: $this->getAppPath() . '/../tmp';
	}

	protected function createLogDirectoryResolver(): DirectoryResolver
	{
		return DirectoryResolver::create($this->getDefaultLogDirectory())
			->addEnvironment('NETTE_LOG_DIR');
	}

	protected function createTempDirectoryResolver(): DirectoryResolver
	{
		return DirectoryResolver::create($this->getDefaultTempDirectory())
				->addEnvironment('NETTE_TEMP_DIR');
	}

	protected function createBootstrapDirectories(): BootstrapDirectories
	{
		return BootstrapDirectories::create($this->getAppPath());
	}

	protected function onBootstrapCreated(Bootstrap $bootstrap): void
	{
	}

	protected function createConfigurator(): Configurator
	{
		return $this->getBootstrap()->createConfigurator();
	}

}
