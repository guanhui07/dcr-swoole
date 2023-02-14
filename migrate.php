<?php
declare(strict_types = 1);

require_once __DIR__.'/vendor/autoload.php';

use DcrSwoole\Di\Container;
use DcrSwoole\Framework\Boostrap;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command;
use Symfony\Component\Console\Application;

/**
 * @see https://www.doctrine-project.org/projects/doctrine-migrations/en/3.5/reference/configuration.html#configuration
 */
$config = [
    'enable' => true,
    'migrations' => [
        'table_storage' => [
            'table_name' => 'migrations',
            'version_column_name' => 'version',
            'version_column_length' => 1024,
            'executed_at_column_name' => 'executed_at',
            'execution_time_column_name' => 'execution_time',
        ],

        'migrations_paths' => [
            'database\migrations' => __DIR__.'/database/migrations',
        ],

        'all_or_nothing' => true,
        'transactional' => true,
        'check_database_platform' => true,
        'organize_migrations' => 'none',
        'connection' => null,
        'em' => null,
    ],
];

!defined('PROJECT_ROOT') && define('PROJECT_ROOT', (__DIR__) . '/');
try {
    $container = Container::instance();
} catch (Exception $e) {
}

// 初始化 注册 config env  db orm  facade门面
/** @var Boostrap $bootstrap */
$bootstrap = $container->make(Boostrap::class);
$bootstrap->run();

$configDb = di()->get(\DcrSwoole\Config\Config::class)->get('db');
$configDb = $configDb['connections']['mysql'];

$dbParams = [
    'driver' => 'pdo_mysql',
    'host' => $configDb['hostname'],
    'dbname' => $configDb['database'],
    'user' => $configDb['username'],
    'password' => $configDb['password'],
];


$connection = DriverManager::getConnection($dbParams);

$configuration = new Configuration($connection);
//foreach ($config['migrations']['migrations_paths'] as $key => $value) {
$configuration->addMigrationsDirectory('database\migrations', __DIR__.'/database/migrations');
//}
$configuration->setAllOrNothing($config['migrations']['all_or_nothing']);
$configuration->setTransactional($config['migrations']['transactional']);
$configuration->setCheckDatabasePlatform($config['migrations']['check_database_platform']);
$configuration->setMigrationOrganization($config['migrations']['organize_migrations']);
$configuration->setConnectionName($config['migrations']['connection']);
$configuration->setEntityManagerName($config['migrations']['em']);

$storageConfiguration = new TableMetadataStorageConfiguration();
$storageConfiguration->setTableName($config['migrations']['table_storage']['table_name']);
$storageConfiguration->setVersionColumnName($config['migrations']['table_storage']['version_column_name']);
$storageConfiguration->setVersionColumnLength($config['migrations']['table_storage']['version_column_length']);
$storageConfiguration->setExecutedAtColumnName($config['migrations']['table_storage']['executed_at_column_name']);
$storageConfiguration->setExecutionTimeColumnName($config['migrations']['table_storage']['execution_time_column_name']);

$configuration->setMetadataStorageConfiguration($storageConfiguration);

$dependencyFactory = DependencyFactory::fromConnection(
    new ExistingConfiguration($configuration),
    new ExistingConnection($connection)
);

$cli = new Application('Doctrine Migrations');
$cli->setCatchExceptions(true);
$cli->addCommands([
    new Command\DumpSchemaCommand($dependencyFactory),
    new Command\ExecuteCommand($dependencyFactory),
    new Command\GenerateCommand($dependencyFactory),
    new Command\LatestCommand($dependencyFactory),
    new Command\ListCommand($dependencyFactory),
    new Command\MigrateCommand($dependencyFactory),
    new Command\RollupCommand($dependencyFactory),
    new Command\StatusCommand($dependencyFactory),
    new Command\SyncMetadataCommand($dependencyFactory),
    new Command\VersionCommand($dependencyFactory),
]);
$cli->run();