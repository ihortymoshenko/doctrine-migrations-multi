<?php

/*
 * This file is part of the doctrine-migrations-multi package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$autoloadFiles = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
];

$autoloader = false;

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
        $autoloader = true;
    }
}

if (!$autoloader) {
    print 'vendor/autoload.php could not be found. Did you run `php composer.phar install`?' . PHP_EOL;
    exit(1);
}

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\MigrationsVersion;
use Doctrine\DBAL\Migrations\Tools\Console\Command;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;

$eventDispatcher = new EventDispatcher();
$eventDispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $consoleCommandEvent) {
    $input = $consoleCommandEvent->getInput();

    if ($input->hasParameterOption(['--connection'])) {
        $output = $consoleCommandEvent->getOutput();

        if (!$dbConfigurationMulti = $input->getParameterOption(['--db-configuration-multi'])) {
            $output->writeln(
                '<error>--db-configuration-multi option must be used together with --connection option.</error>'
            );
            exit(2);
        }

        if (!file_exists($dbConfigurationMulti)) {
            $output->writeln("<error>$dbConfigurationMulti database configuration file could not be found.</error>");
            exit(3);
        }

        $configurationMulti = require_once $dbConfigurationMulti;

        $connection = $input->getParameterOption(['--connection']);

        if (!isset($configurationMulti[$connection])) {
            $output->writeln("<error>$connection connection could not be found.</error>");
            exit(4);
        }

        $consoleCommandEvent->getCommand()->getHelperSet()->set(
            new ConnectionHelper(DriverManager::getConnection($configurationMulti[$connection])),
            'connection'
        );
    }
});

$helperSet = new HelperSet();

if (class_exists(QuestionHelper::class)) {
    $helperSet->set(new QuestionHelper(), 'question');
} else {
    $helperSet->set(new DialogHelper(), 'dialog');
}

$application = new Application('Doctrine Migrations', MigrationsVersion::VERSION());
$application->setDispatcher($eventDispatcher);
$application->setHelperSet($helperSet);
$application->setCatchExceptions(true);
$application->getDefinition()->addOptions(
    [
        new InputOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection name'),
        new InputOption(
            'db-configuration-multi',
            null,
            InputOption::VALUE_OPTIONAL,
            'The path to a database connection configuration file with more than one connection'
        ),
    ]
);
$application->addCommands(array(
    new Command\ExecuteCommand(),
    new Command\GenerateCommand(),
    new Command\LatestCommand(),
    new Command\MigrateCommand(),
    new Command\StatusCommand(),
    new Command\VersionCommand(),
));
$application->run();
