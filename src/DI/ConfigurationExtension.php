<?php
declare(strict_types=1);

namespace Trejjam\Configuration\DI;

use Nette\Utils\Validators;
use Trejjam;
use Nextras\Migrations;

final class ConfigurationExtension extends Trejjam\BaseExtension\DI\BaseExtension
{
	protected $default = [
		'environment'  => Trejjam\Configuration\Environment::SITE_MODE_PUBLIC,
		'fileVersion'  => [
			'version'   => Trejjam\Configuration\FileVersion::UNSPECIFIED_VERSION,
			'buildTime' => NULL,
		],
		'useMigration' => FALSE,
		'migration'    => [
			'dir'           => NULL,
			'withDummyData' => FALSE,
			'phpParams'     => [],
		],
	];

	protected $classesDefinition = [
		'environment'     => Trejjam\Configuration\Environment::class,
		'fileVersion'     => Trejjam\Configuration\FileVersion::class,
		'systemDirectory' => Trejjam\Configuration\SystemDirectory::class,
	];

	public function loadConfiguration(bool $validateConfig = TRUE) : void
	{
		$this->default['fileVersion']['buildTime'] = new \DateTimeImmutable;
		$this->default['useMigration'] = interface_exists(Migrations\IConfiguration::class);

		parent::loadConfiguration($validateConfig);

		Validators::assertField($this->config, 'useMigration', 'bool');
		if ($this->config['useMigration']) {
			Validators::assertField($this->config['migration'], 'dir', 'string|Nette\PhpGenerator\PhpLiteral');
			Validators::assertField($this->config['migration'], 'phpParams', 'array');
		}

		$containerBuilder = $this->getContainerBuilder();
		$applicationParameters = $containerBuilder->parameters;

		$types = $this->getTypes();

		$types['environment']->setArguments(
			[
				'siteMode' => $this->config['environment'],
			]
		);
		$types['fileVersion']->setArguments(
			[
				'version'   => $this->config['fileVersion']['version'],
				'buildTime' => $this->config['fileVersion']['buildTime'],
			]
		);
		$types['systemDirectory']->setArguments(
			[
				'tempDir' => $applicationParameters['tempDir'],
				'appDir'  => $applicationParameters['appDir'],
				'wwwDir'  => $applicationParameters['wwwDir'],
			]
		);
	}

	public function beforeCompile()
	{
		$containerBuilder = $this->getContainerBuilder();

		if ($this->config['useMigration']) {
			$migration = $containerBuilder->getDefinitionByType(Migrations\IConfiguration::class);

			$migration->setType(Trejjam\Configuration\Migration::class);
			$migration->setFactory(
				Trejjam\Configuration\Migration::class,
				[
					'dir'           => $this->config['migration']['dir'],
					'withDummyData' => $this->config['migration']['withDummyData'],
					'phpParams'     => $this->config['migration']['phpParams'],
				]
			);
		}
	}
}
