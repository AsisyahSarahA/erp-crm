<?php

declare(strict_types=1);

$loader = require __DIR__ . '/../../../../backend/vendor/autoload.php';

$loader->addPsr4('Astacode\\Kernel\\', __DIR__ . '/../src');
$loader->addPsr4('Astacode\\Kernel\\Tests\\', __DIR__);

return $loader;

