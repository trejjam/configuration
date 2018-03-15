<?php
declare(strict_types=1);

namespace Trejjam\Configuration;

final class SystemDirectory
{
	/**
	 * @var string
	 */
	private $tempDir;
	/**
	 * @var string
	 */
	private $appDir;
	/**
	 * @var string
	 */
	private $rootDir;
	/**
	 * @var string
	 */
	private $wwwDir;

	public function __construct(
		string $tempDir,
		string $appDir,
		string $wwwDir
	) {
		$this->tempDir = $tempDir;
		$this->appDir = $appDir;
		$this->rootDir = dirname($appDir);
		$this->wwwDir = $wwwDir;
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
