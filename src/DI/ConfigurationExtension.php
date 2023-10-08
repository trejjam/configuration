<?php
declare(strict_types=1);

namespace Trejjam\Configuration\DI;

use DateTimeImmutable;
use Nette\DI;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\Literal;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Trejjam;
use Nextras\Migrations;
use Trejjam\Configuration\SiteMode;

final class ConfigurationExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        $siteModes = array_column(SiteMode::cases(), 'name');
        $knownSiteModes = join(', ', $siteModes);

        return Expect::structure([
            'environment' => Expect::string()
                                   ->assert(fn (string $x) => in_array($x, $siteModes, true), "Expect one of the following values: $knownSiteModes")
                                   ->transform(fn ($x) => SiteMode::from($x))
                                   ->default(SiteMode::Public->name),
            'isCli' => Expect::bool()->default(PHP_SAPI === 'cli'),
            'fileVersion' => Expect::structure([
                'version' => Expect::string()->dynamic()->default(Trejjam\Configuration\FileVersion::UNSPECIFIED_VERSION),
                'buildTime' => Expect::anyOf(
                    Expect::type(DateTimeImmutable::class),
                    Expect::type(DI\Definitions\Statement::class)
                )->dynamic()->default(new DateTimeImmutable()),
            ]),
            'useMigration' => Expect::bool()->default(interface_exists(Migrations\IConfiguration::class)),
            'migration' => Expect::structure([
                'dir' => Expect::anyOf(
                    Expect::string()->nullable()->default(null),
                    Expect::type(Literal::class)->nullable()->default(null)
                ),
                'withDummyData' => Expect::bool()->default(false),
                'phpParams' => Expect::array(),
                'disablePhpExtension' => Expect::bool()->default(false),
                'withTestData' => Expect::bool()->default(false),
            ]),
        ]);
    }

    public function beforeCompile()
    {
        $containerBuilder = $this->getContainerBuilder();

        if ($this->config->useMigration) {
            $migration = $containerBuilder->getDefinitionByType(Migrations\IConfiguration::class);

            $migrationConfiguration = $this->config->migration;

            $migration->setType(Trejjam\Configuration\Migration::class);
            $migration->setFactory(
                Trejjam\Configuration\Migration::class,
                [
                    'dir' => $migrationConfiguration->dir,
                    'withDummyData' => $migrationConfiguration->withDummyData,
                    'phpParams' => $migrationConfiguration->phpParams,
                    'disablePhpExtension' => $migrationConfiguration->disablePhpExtension,
                    'withTestData' => $migrationConfiguration->withTestData,
                ]
            );
        }

        $containerBuilder->addDefinition($this->prefix('environment'))
            ->setType(Trejjam\Configuration\Environment::class)
            ->setArguments(
                [
                    'siteMode' => $this->config->environment,
                    'isCli' => $this->config->isCli,
                ]
            );

        $fileVersion = $this->config->fileVersion;
        $containerBuilder->addDefinition($this->prefix('fileVersion'))
            ->setType(Trejjam\Configuration\FileVersion::class)
            ->setArguments(
                [
                    'version' => $fileVersion->version,
                    'buildTime' => $fileVersion->buildTime,
                ]
            );

        $applicationParameters = $containerBuilder->parameters;
        $containerBuilder->addDefinition($this->prefix('systemDirectory'))
            ->setType(Trejjam\Configuration\SystemDirectory::class)
            ->setArguments(
                [
                    'tempDir' => $applicationParameters['tempDir'],
                    'appDir' => $applicationParameters['appDir'],
                    'wwwDir' => $applicationParameters['wwwDir'],
                ]
            );
    }
}
