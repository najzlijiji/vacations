<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Models\\User' => $baseDir . '/models/User.php',
    'Models\\Vacation' => $baseDir . '/models/Vacation.php',
    'Vacations\\Db' => $baseDir . '/src/Vacations/Connection.php',
    'Vacations\\Router' => $baseDir . '/src/Vacations/Router.php',
    'Vacations\\VacationStatus' => $baseDir . '/src/Vacations/VacationStatus.php',
);
