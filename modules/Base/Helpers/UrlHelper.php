<?php

namespace Modules\Base\Helper;

class UrlHelper
{
    /**
     * @var \Silex\Application
     */
    private static $app = FALSE;

    /**
     * @return string
     */
    public static function currentURL()
    {
        return sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            $_SERVER['REQUEST_URI']
        );
    }

    /**
     * @param bool|false $uri
     * @return mixed|string
     */
    public static function baseURL( $uri = false )
    {
        $u = sprintf(
            "%s://%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME']
        );
        if( !empty( $uri ) )
            return str_replace(array("//", ":/"), array("/", "://"), $u . "/" . $uri);
        return $u;
    }

    /**
     * @param string $uri
     * @return mixed
     */
    public static function publicURL( $uri = "" )
    {
        return str_replace(array("//", ":/"), array("/", "://"), substr( APPURL, -1 ) == "/" ? APPURL : APPURL . "/" . $uri);
    }

    /**
     * @param string $uri
     * @return mixed
     */
    public static function assetsURL( $uri = "" )
    {
        return str_replace(array("//", ":/"), array("/", "://"), substr( APPURL, -1 ) == "/" ? APPURL : APPURL . "/assets/" . $uri);
    }
}