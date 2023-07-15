<?php
declare(strict_types=1);

namespace Trejjam\Configuration;

final class SystemDirectory
{
	private string $rootDir;

	public function __construct(
		private readonly string $tempDir,
		private readonly string $appDir,
		private readonly string $wwwDir
	) {
		$this->rootDir = dirname($appDir);
	}

	public function getTempDir() : string
	{
		return $this->tempDir;
	}

	public function getAppDir() : string
	{
		return $this->appDir;
	}

	public function getRootDir() : string
	{
		return $this->rootDir;
	}

	public function getWwwDir() : string
	{
		return $this->wwwDir;
	}
}
