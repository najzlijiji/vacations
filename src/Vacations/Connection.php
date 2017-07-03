<?php
namespace Vacations;

class Db {

	/**
	 *
	 * @var \mysqli $instance
	 */
    private static $instance = NULL;

    private function __construct() {}

    private function __clone() {}

	/**
	 *
	 * Returns database connector
	 * @return \mysqli 
	 */
    public static function getInstance(): \mysqli {
        if (!isset(self::$instance)) {
            self::$instance = new \mysqli('localhost', 'root', 'redstar', 'vacation');
        }
        return self::$instance;
    }
}
?>