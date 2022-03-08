<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

class Environment
{

	private string $environment;

	private ProjectDirectories $directories;

	public function __construct(string $environment, ProjectDirectories $directories)
	{
		$this->environment = $environment;
		$this->directories = $directories;
	}

	public function getEnvironment(): string
	{
		return $this->environment;
	}

	public function isProduction(): bool
	{
		return str_starts_with($this->environment, 'prod');
	}

	public function isDevelopment(): bool
	{
		return str_starts_with($this->environment, 'prod');
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
