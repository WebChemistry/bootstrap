<?php declare(strict_types = 1);

namespace WebChemistry\Bootstrap;

use InvalidArgumentException;

class BootstrapDirectories
{

	public function __construct(
		private string $appDir,
		private string $wwwDir,
		private string $vendorDir,
	)
	{
	}

	public function getAppDir(): string
	{
		return $this->appDir;
	}

	public function getWwwDir(): string
	{
		return $this->wwwDir;
	}

	public function getVendorDir(): string
	{
		return $this->vendorDir;
	}

	public static function create(string $appDir, ?string $wwwDir = null, ?string $vendorDir = null): self
	{
		$appDir = realpath($appDir);
		if (!$appDir) {
			throw new InvalidArgumentException(sprintf('app path (%s) not exists or permission denied', $appDir));
		}

		$wwwDir = realpath($wwwDir ?? $appDir . '/../www');
		if (!$wwwDir) {
			throw new InvalidArgumentException(sprintf('www path (%s) not exists or permission denied', $appDir));
		}

		$vendorDir = realpath($vendorDir ?? $appDir . '/../vendor');
		if (!$vendorDir) {
			throw new InvalidArgumentException(sprintf('vendor path (%s) not exists or permission denied', $vendorDir));
		}

		return new self($appDir, $wwwDir, $vendorDir);
	}

}
