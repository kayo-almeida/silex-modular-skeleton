<?php

// Carregar configurações
if( file_exists( "config/config.dev.php" ) ) {
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);
    define( "ENVIRONMENT", "dev" );
    define( "DEBUG", TRUE );
    require_once( "config/config.dev.php" );
}elseif( file_exists( "config/config.hom.php" ) ) {
    error_reporting(E_ERROR | E_WARNING);
    ini_set('error_reporting', E_ALL);
    define( "ENVIRONMENT", "hom" );
    define( "DEBUG", TRUE );
    require_once( "config/config.hom.php" );
}elseif( file_exists( "config/config.prod.php" ) ) {
    error_reporting(0);
    define( "ENVIRONMENT", "prod" );
    define( "DEBUG", FALSE );
    require_once( "config/config.prod.php" );
}else {
    die( "Nenhum arquivo de configuração encontrado" );
}

// Carrega outras configurações / funções
require_once( "autoload.php" ); // obrigatório
if( file_exists("mimes.php") ) require_once( "mimes.php" );
if( file_exists("doctypes.php") ) require_once( "doctypes.php" );


// Define os módulos utilizados
$modulos = array(
    'Base', // Esse módulo contém helpers e uma interface. É legal utiliza-la
    'Exemplo'
);

// Inicia aplicação
$app = new Silex\Application();
$app['debug'] = DEBUG;

// Inicia os controllers
foreach( $modulos as $modulo ) {
    core_autoload(BASEPATH . '/modules/' . $modulo);
    $className = '\\Modules\\' . $modulo . '\\Controller\\' . $modulo;
    if(class_exists($className)) new $className($app);
}

// Roda a aplicação
$app->run();