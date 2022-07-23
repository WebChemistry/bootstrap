<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

class Environment
{

	public function __construct(
		private string $environment,
		private string $wwwDir,
		private string $vendorDir,
		private string $appDir,
		private string $tempDir,
		private ?string $logDir,
	)
	{
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
		return $this->wwwDir;
	}

	public function getVendorDir(): string
	{
		return $this->vendorDir;
	}

	public function getAppDir(): string
	{
		return $this->appDir;
	}

	public function getTempDir(): string
	{
		return $this->tempDir;
	}

	public function getLogDir(): ?string
	{
		return $this->logDir;
	}

}
