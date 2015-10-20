<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASEPATH', dirname(__DIR__));
chdir(BASEPATH);

require_once( "vendor/autoload.php" );
require_once( "src/bootstrap.php" );