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

	public function __construct(
		protected string $localConfig,
	)
	{
	}

	protected function getAppPath(): string
	{
		if (!isset($this->appPath)) {
			$reflection = new ReflectionClass(static::class);
			$this->appPath = dirname($reflection->getFileName());
		}

		return $this->appPath;
	}

	protected function createLogDirectoryResolver(): DirectoryResolver
	{
		return DirectoryResolver::create($this->getAppPath() . '/../log')
			->addEnvironment('NETTE_LOG_DIR');
	}

	protected function createTempDirectoryResolver(): DirectoryResolver
	{
		return DirectoryResolver::create($this->getAppPath() . '/../tmp')
				->addEnvironment('NETTE_TEMP_DIR');
	}

	protected function createBootstrapDirectories(): BootstrapDirectories
	{
		return BootstrapDirectories::create($this->getAppPath());
	}

	protected function onBootstrapCreated(Bootstrap $bootstrap): void
	{
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

	protected function createConfigurator(): Configurator
	{
		return $this->getBootstrap()->createConfigurator();
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

	public function bootConsole(): Configurator
	{
		$this->getBootstrap()->setDebugMode(true);

		return $this->createConfigurator();
	}

}
