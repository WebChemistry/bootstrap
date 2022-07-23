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

	private BootstrapValue $logDir;

	private BootstrapValue $tmpDir;

	private BootstrapValue $environment;

	private BootstrapValue $debugMode;

	private BootstrapValue $wwwDir;

	private BootstrapValue $vendorDir;

	protected EnvironmentVariables $env;

	/** @var callable[] */
	public array $onBootstrapCreated = [];

	public function __construct(
		protected ?string $localConfig,
		?EnvironmentVariables $env = null,
	)
	{
		$this->env = $env ?? new EnvironmentVariables();

		$this->debugMode = BootstrapValue::create('debug mode', $this->env)
			->addEnvironment('NETTE_DEBUG_MODE');

		$this->environment = BootstrapValue::create('environment', $this->env)
			->setDefault('production')
			->addEnvironment('NETTE_ENVIRONMENT');

		$this->logDir = BootstrapValue::create('log directory', $this->env)
			->setDefault($this->getAppPath() . '/../log')
			->addEnvironment('NETTE_LOG_DIR');

		$this->tmpDir = BootstrapValue::create('tmp directory', $this->env)
			->setDefault($this->getAppPath() . '/../tmp')
			->addEnvironment('NETTE_TEMP_DIR')
			->addEnvironment('NETTE_TMP_DIR');

		$this->wwwDir = BootstrapValue::create('www directory', $this->env)
			->setDefault($this->getAppPath() . '/../www');

		$this->vendorDir = BootstrapValue::create('vendor directory', $this->env)
			->setDefault($this->getAppPath() . '/../vendor');
	}

	public function getEnvironment(): BootstrapValue
	{
		return $this->environment;
	}

	public function getLogDir(): BootstrapValue
	{
		return $this->logDir;
	}

	public function getTmpDir(): BootstrapValue
	{
		return $this->tmpDir;
	}

	public function getDebugMode(): BootstrapValue
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
				$this->getAppPath(),
				$this->wwwDir->getValue(),
				$this->vendorDir->getValue(),
				$this->tmpDir->getValue(),
				$this->environment->getValue(),
				$this->logDir->getValueNullable(),
				$this->debugMode->getValueNullable(),
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

	protected function onBootstrapCreated(Bootstrap $bootstrap): void
	{
	}

	protected function createConfigurator(): Configurator
	{
		return $this->getBootstrap()->createConfigurator();
	}

}
