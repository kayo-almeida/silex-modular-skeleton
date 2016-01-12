<?php

function core_load_modules( $modulos, $app )
{
    foreach( $modulos as $moduleName ) {
        core_autoload(BASEPATH . '/modules/' . $moduleName);

        // obtem todas as classes que devem ser instanciadas
        $autoInstanceFiles = core_get_file_by_dir(BASEPATH . '/modules/' . $moduleName . '/Controllers');
        foreach( $autoInstanceFiles as $instanceFiles ) {
            $moduleClassName = '\\Modules\\' . $moduleName . '\\Controller\\' . $instanceFiles;
            if(class_exists($moduleClassName)) new $moduleClassName($app);
        }
    }
}

function core_autoload( $directories )
{
    $dirs = core_get_directories( $directories );
    foreach( $dirs as $dir ) {
        foreach (glob($dir . "/*.php") as $filename) {
            require_once ( $filename );
        }
    }
}

function core_get_file_by_dir( $dir )
{
    $files = array();
    foreach (glob($dir . "/*.php") as $filename) {
        $files[] = str_replace(array($dir . "/", ".php"), array("", ""), $filename);
    }
    return $files;
}

function core_get_directories($root)
{
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
    );

    $paths = array($root);
    foreach ($iter as $path => $dir) {
        if ($dir->isDir()) {
            $paths[] = $path;
        }
    }
    return $paths;
}
