<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use Nette\Configurator;
use Nette\Utils\Arrays;
use ReflectionClass;

abstract class DefaultBooting
{

	/** @var string[] */
	protected array $configs = [];

	private Bootstrap $bootstrap;

	private string $appPath;

	private EnvironmentValue $logDir;

	private EnvironmentValue $tmpDir;

	private EnvironmentValue $environment;

	protected ?string $defaultLogDirectory = null;

	protected ?string $defaultTempDirectory = null;

	protected EnvironmentVariables $env;

	protected EnvironmentResolver $environmentResolver;

	/** @var callable[] */
	public array $onBootstrapCreated = [];

	public function __construct(
		protected ?string $localConfig,
		?EnvironmentVariables $env = null,
	)
	{
		$this->env = $env ?? new EnvironmentVariables();
		$this->debugMode = EnvironmentValue::create('debug mode', $this->env)
			->addEnvironment('NETTE_DEBUG_MODE');

		$this->environment = EnvironmentValue::create('environment', $this->env)
			->setDefault('production')
			->addEnvironment('NETTE_ENVIRONMENT');

		$this->logDir = EnvironmentValue::create('log directory', $this->env)
			->setDefault($this->getAppPath() . '/../log')
			->addEnvironment('NETTE_LOG_DIR');

		$this->tmpDir = EnvironmentValue::create('tmp directory', $this->env)
			->setDefault($this->getAppPath() . '/../tmp')
			->addEnvironment('NETTE_TEMP_DIR')
			->addEnvironment('NETTE_TMP_DIR');
	}

	public function getEnvironment(): EnvironmentValue
	{
		return $this->environment;
	}

	public function getLogDir(): EnvironmentValue
	{
		return $this->logDir;
	}

	public function getTmpDir(): EnvironmentValue
	{
		return $this->tmpDir;
	}

	public function getDebugMode(): EnvironmentValue
	{
		return $this->debugMode;
	}

	public function setLocalConfig(?string $localConfig): static
	{
		$this->localConfig = $localConfig;

		return $this;
	}

	public function boot(): Configurator
	{
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
				$this->tmpDir,
				$this->logDir,
				$this->environment,
				$this->debugMode,
			);

			foreach ($this->configs as $config) {
				$this->bootstrap->addConfig($config);
			}

			if ($this->localConfig) {
				$this->bootstrap->addConfig($this->localConfig, true);
			}

			$this->onBootstrapCreated($this->bootstrap);

			Arrays::invoke($this->onBootstrapCreated, $this->bootstrap);
		}

		return $this->bootstrap;
	}

	protected function getAppPath(): string
	{
		return $this->appPath ??= dirname((new ReflectionClass(static::class))->getFileName());
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
