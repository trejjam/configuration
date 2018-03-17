<?php
declare(strict_types=1);

namespace Trejjam\Configuration;

use Nextras\Migrations;

final class Migration implements Migrations\IConfiguration
{
	/**
	 * @var string
	 */
	protected $dir;
	/**
	 * @var Migrations\IDriver
	 */
	protected $driver;
	/**
	 * @var bool
	 */
	protected $withDummyData;
	/**
	 * @var array
	 */
	protected $phpParams;
	/**
	 * @var bool
	 */
	protected $withTestData;
	/**
	 * @var bool
	 */
	protected $disablePhpExtension;

	/**
	 * @var Migrations\Entities\Group[]
	 */
	protected $groups;
	/**
	 * @var Migrations\IExtensionHandler[]
	 */
	protected $handlers;
	/**
	 * @var Migrations\IDiffGenerator|NULL
	 */
	protected $structureDiffGenerator;
	/**
	 * @var Migrations\IDiffGenerator|NULL
	 */
	protected $dummyDataDiffGenerator;

	public function __construct(
		string $dir,
		Migrations\IDriver $driver,
		bool $withDummyData = FALSE,
		array $phpParams = [],
		bool $disablePhpExtension = FALSE,
		bool $withTestData = FALSE
	) {
		$this->dir = $dir;
		$this->driver = $driver;
		$this->setWithDummyData($withDummyData);
		$this->phpParams = $phpParams;
		$this->setWithTestData($withTestData);
		$this->disablePhpExtension = $disablePhpExtension;
	}

	public function setWithDummyData(bool $withDummyData)
	{
		$this->withDummyData = $withDummyData;
	}

	public function setWithTestData(bool $withTestData)
	{
		$this->withTestData = $withTestData;
	}

	/**
	 * @return Migrations\Entities\Group[]
	 */
	public function getGroups() : array
	{
		if ($this->groups === NULL) {
			$structures = new Migrations\Entities\Group;
			$structures->enabled = TRUE;
			$structures->name = 'structures';
			$structures->directory = $this->dir . '/structures';
			$structures->dependencies = [];

			$basicData = new Migrations\Entities\Group;
			$basicData->enabled = TRUE;
			$basicData->name = 'basic-data';
			$basicData->directory = $this->dir . '/basic-data';
			$basicData->dependencies = ['structures'];

			$testData = new Migrations\Entities\Group;
			$testData->enabled = $this->withTestData;
			$testData->name = 'test-data';
			$testData->directory = $this->dir . '/test-data';
			$testData->dependencies = ['structures', 'basic-data'];

			$dummyData = new Migrations\Entities\Group;
			$dummyData->enabled = $this->withDummyData;
			$dummyData->name = 'dummy-data';
			$dummyData->directory = $this->dir . '/dummy-data';
			$dummyData->dependencies = ['structures', 'basic-data'];

			$this->groups = [$structures, $basicData, $testData, $dummyData];
		}

		return $this->groups;
	}

	/**
	 * @return Migrations\IExtensionHandler[] (extension => IExtensionHandler)
	 */
	public function getExtensionHandlers() : array
	{
		if ($this->handlers === NULL) {
			$this->handlers = [
				'sql' => new Migrations\Extensions\SqlHandler($this->driver),
				'php' => new Migrations\Extensions\PhpHandler($this->phpParams),
			];

			if ($this->disablePhpExtension) {
				$this->handlers['php'] = new Helper\Migration\DummyHandler;
			}
		}

		return $this->handlers;
	}

	public function setStructureDiffGenerator(Migrations\IDiffGenerator $generator = NULL)
	{
		$this->structureDiffGenerator = $generator;
	}

	public function setDummyDataDiffGenerator(Migrations\IDiffGenerator $generator = NULL)
	{
		$this->dummyDataDiffGenerator = $generator;
	}
}
