<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

class ProjectDirectories
{

	private string $wwwDir;

	private string $vendorDir;

	private string $appDir;

	private string $tempDir;

	private ?string $logDir;

	public function __construct(string $wwwDir, string $vendorDir, string $appDir, string $tempDir, ?string $logDir)
	{
		$this->wwwDir = $wwwDir;
		$this->vendorDir = $vendorDir;
		$this->appDir = $appDir;
		$this->tempDir = $tempDir;
		$this->logDir = $logDir;
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
