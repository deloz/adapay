<?php

declare(strict_types=1);

use Ergebnis\PhpCsFixer\Config;

require __DIR__ . '/vendor/autoload.php';

$config = Config\Factory::fromRuleSet(new Config\RuleSet\Php81(), [
    'ordered_class_elements' => [
        'sort_algorithm' => 'alpha',
    ],
    'mb_str_functions' => false,
]);

$config->getFinder()->in(__DIR__);
$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');

return $config;
