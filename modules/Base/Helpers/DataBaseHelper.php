<?php

/*
 * Essa classe depende da classe \Pixie\Connection
 * Composer: "usmanhalalit/pixie": "~1.0.2"
 * Github: https://github.com/usmanhalalit/pixie
 */

namespace Modules\Base\Helper;

/**
 * Class DataBaseHelper
 * @package Modules\Base\Helper
 */
class DataBaseHelper
{

    /**
     * @param array $config
     * @return \Pixie\QueryBuilder\QueryBuilderHandler
     */
    public function connect( Array $config )
    {
        new \Pixie\Connection('mysql', $config, 'DB');
        return new \DB;
    }

    /**
     * @return \Pixie\QueryBuilder\QueryBuilderHandler
     */
    public function autoConnect()
    {
        return $this->connect(array(
            'driver'    => DB_DRIVER,
            'host'      => DB_HOST,
            'database'  => DB_NAME,
            'username'  => DB_USERNAME,
            'password'  => DB_PASSWORD,
            'charset'   => DB_CHARSET,
            'collation' => DB_COLLATION,
            'prefix'    => DB_PREFIX,
        ));
    }
}