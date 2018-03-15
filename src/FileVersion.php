<?php
declare(strict_types=1);

namespace Trejjam\Configuration;

final class FileVersion
{
	public const UNSPECIFIED_VERSION = 'UNSPECIFIED_VERSION';

	/**
	 * @var string
	 */
	private $version;
	/**
	 * @var \DateTimeImmutable
	 */
	private $buildTime;

	public function __construct(
		string $version,
		\DateTimeImmutable $buildTime
	) {
		$this->version = $version;
		$this->buildTime = $buildTime;
	}

	public function getVersion() : string
	{
		return $this->version;
	}

	public function getBuildTime() : \DateTimeImmutable
	{
		return $this->buildTime;
	}
}
