<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Doctrine\DBAL\DriverManager;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Doctrine\DBAL\Logging\DebugStack;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);
            $processor = new UidProcessor();
            $logger->pushProcessor($processor);
            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);
            return $logger;
        },
        Connection::class      => function (ContainerInterface $c): Connection {
            $settings = $c->get('settings');

            $connectionParams = [
                'dbname'   => $settings['db']['name'],
                'user'     => $settings['db']['username'],
                'password' => $settings['db']['password'],
                'host'     => $settings['db']['host'],
                'driver'   => 'pdo_mysql',
            ];

            $connection = DriverManager::getConnection($connectionParams);
            $connection->getConfiguration()->setSQLLogger(new DebugStack());
            return $connection;
        }
    ]);
};
