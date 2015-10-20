<?php

function core_autoload( $directories ) {
    $dirs = core_get_directories( $directories );
    foreach( $dirs as $dir ) {
        foreach (glob($dir . "/*.php") as $filename) {
            require_once ( $filename );
        }
    }
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
