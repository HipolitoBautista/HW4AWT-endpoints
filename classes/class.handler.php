<?php

require_once 'class.applicants.php';
require_once 'class.request.php';
require_once 'inc/composer/vendor/autoload.php';

use Monolog\Level;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Handler {
    public $db;
    public $log;

    function __construct() {
        $logFilename = date('Y-m-d') . "_activity.log";

        $this->log = new Logger('AWT');
        $handler = new StreamHandler("log/$logFilename");
        $handler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n"));
        $this->log->pushHandler($handler);

        $this->db = new mysqli(DBHOST, DBUSER, DBPWD, DBNAME);
        if (mysqli_connect_error()) {
            $this->log->error("Error connecting to MySQL Error: [" . mysqli_connect_error() . "]");
            $this->db = null;
        }
        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}

?>
