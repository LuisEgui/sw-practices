<?php

# Database parameters
define('BD_HOST', 'localhost');
define('BD_NAME', 'testing');
define('BD_USER', 'userTesting');
define('BD_PASS', '1234');
define('BD_PORT', '3305');

require_once __DIR__.'/DatabaseConnection.php';

class MysqlConnector implements DatabaseConnection {
    
    private static $instance;
    private $mysqli;

    private function __construct() {
        $this->mysqli = null;
    }

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new self;
        return self::$instance;
    }

    public function getConnection() {
        if ($this->mysqli == null) {
            $mysqli = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME, BD_PORT);

            if ($mysqli->connect_errno)
                error_log("Error when connecting to the database: ({$mysqli->errno}) {$mysqli->error}");
            
            if (!$mysqli->set_charset("utf8mb4"))
                error_log("Error when connecting to the database: ({$mysqli->errno}) {$mysqli->error}");
            
            $this->mysqli = $mysqli;

            // It'll call closeConnection() before finishing the script execution:
            register_shutdown_function(Closure::fromCallable([$this, 'closeConnection']));
        }

        return $this->mysqli;
    }

    public function closeConnection() {
        if ($this->mysqli != null && !$this->mysqli->connect_errno)
            $this->mysqli->close();
    }

    public function query($sql) {
        return $this->getConnection()->query($sql);
    }

    public function prepare($sql) {
        return self::getInstance()->getConnection()->prepare($sql);
    }

    public function beginTransaction() {
        return $this->getConnection()->begin_transaction();
    }

    public function commit() {
        return $this->getConnection()->commit();
    }

    public function rollback() {
        return $this->getConnection()->rollback();
    }
}