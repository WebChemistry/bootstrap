<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use InvalidArgumentException;

class ProjectDirectories extends BootstrapDirectories
{

	public function __construct(
		string $wwwDir,
		string $vendorDir,
		string $appDir,
		private string $tempDir,
		private ?string $logDir,
	)
	{
		parent::__construct($appDir, $wwwDir, $vendorDir);
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
