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

	private BootstrapVariable $logDir;

	private BootstrapVariable $tmpDir;

	private BootstrapVariable $environment;

	private BootstrapVariable $debugMode;

	private BootstrapVariable $wwwDir;

	private BootstrapVariable $vendorDir;

	private BootstrapVariable $validateContainer;

	protected Env $env;

	/** @var callable[] */
	public array $onBootstrapCreated = [];

	public function __construct(
		protected ?string $localConfig,
		?Env $env = null,
	)
	{
		$this->env = $env ?? Env::fromNative();

		$this->debugMode = BootstrapVariable::create('debug mode', $this->env)
			->append(BootstrapValue::fromEnv('NETTE_DEBUG_MODE'));

		$this->environment = BootstrapVariable::create('environment', $this->env)
			->append(BootstrapValue::fromEnv('NETTE_ENVIRONMENT'))
			->append(BootstrapValue::fromString('production'));

		$this->validateContainer = BootstrapVariable::create('validate container', $this->env)
			->append(BootstrapValue::fromEnv('NETTE_VALIDATE_CONTAINER'))
			->append(BootstrapValue::fromString('1'));

		$this->logDir = BootstrapVariable::create('log directory', $this->env)
			->append(BootstrapValue::fromEnv('NETTE_LOG_DIR'))
			->append(BootstrapValue::fromString($this->getAppPath() . '/../log'));

		$this->tmpDir = BootstrapVariable::create('tmp directory', $this->env)
			->append(BootstrapValue::fromEnv('NETTE_TMP_DIR'))
			->append(BootstrapValue::fromEnv('NETTE_TEMP_DIR'))
			->append(BootstrapValue::fromString($this->getAppPath() . '/../tmp'));

		$this->wwwDir = BootstrapVariable::create('www directory', $this->env)
			->append(BootstrapValue::fromString($this->getAppPath() . '/../www'));

		$this->vendorDir = BootstrapVariable::create('vendor directory', $this->env)
			->append(BootstrapValue::fromString($this->getAppPath() . '/../vendor'));
	}

	public function getEnvironment(): BootstrapVariable
	{
		return $this->environment;
	}

	public function getLogDir(): BootstrapVariable
	{
		return $this->logDir;
	}

	public function getTmpDir(): BootstrapVariable
	{
		return $this->tmpDir;
	}

	public function getDebugMode(): BootstrapVariable
	{
		return $this->debugMode;
	}

	public function getValidateContainer(): BootstrapVariable
	{
		return $this->validateContainer;
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
				$this->validateContainer->getValue(),
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
