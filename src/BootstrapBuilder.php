<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

final class BootstrapBuilder
{

	private bool $tracy = true;

	private DirectoryResolver $logDir;

	private BootstrapDirectories $bootstrapDirs;

	/** @var string[] */
	private array $configs = [];

	public function __construct(string $logDir, string $appDir, string $tempDir, string $currentDir)
	{
		$this->logDir = DirectoryResolver::create($logDir)
			->addEnvironment('NETTE_LOG_DIR');
		$this->tempDir = DirectoryResolver::create($tempDir)
			->addEnvironment('NETTE_TEMP_DIR');

		$this->bootstrapDirs = BootstrapDirectories::create($appDir);

		if (is_file($envFile = $currentDir . '/.env.php')) {
			require_once $envFile;
		}
	}

	public function addConfig(string $config): self
	{
		$this->configs[] = $config;

		return $this;
	}

	public function disableTracy(): self
	{
		$this->tracy = false;

		return $this;
	}

	public function build(): Bootstrap
	{
		$bootstrap = new Bootstrap(
			$this->bootstrapDirs,
			$this->tempDir,
			$this->disableTracy ? null : $this->logDir
		);

		foreach ($this->configs as $config) {
			$bootstrap->addConfig($config);
		}

		return $bootstrap;
	}

}
