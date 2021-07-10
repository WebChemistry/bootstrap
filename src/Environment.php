<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

class Environment
{

	private EnvironmentResolver $environment;

	private ProjectDirectories $directories;

	public function __construct(string $environment, ProjectDirectories $directories)
	{
		$environment = trim(strtolower($environment));
		if (!$environment) {
			$environment = 'dev';
		}

		$this->environment = new EnvironmentResolver($environment);
		$this->directories = $directories;
	}

	public function getEnvironment(): string
	{
		return $this->environment;
	}

	public function isProduction(): bool
	{
		return $this->environment->isProduction();
	}

	public function isDevelopment(): bool
	{
		return $this->environment->isDevelopment();
	}

	public function getWwwDir(): string
	{
		return $this->directories->getWwwDir();
	}

	public function getVendorDir(): string
	{
		return $this->directories->getVendorDir();
	}

	public function getAppDir(): string
	{
		return $this->directories->getAppDir();
	}

	public function getTempDir(): string
	{
		return $this->directories->getTempDir();
	}

	public function getLogDir(): ?string
	{
		return $this->directories->getLogDir();
	}

}
