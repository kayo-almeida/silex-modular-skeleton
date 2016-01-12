<?php

namespace Modules\Base\Helper;

/**
 * Class SessionHelper
 * @package Modules\Base\Helper
 */
class SessionHelper {

    /**
     * @var string
     */
    public $prefix;

    /**
     *
     */
    public function __construct()
    {
        session_start();
        $this->prefix = sha1($_SERVER['HTTP_HOST']);

        if($this->get_session('flash')) {
            foreach($_SESSION[$this->prefix]['flash'] as $name=>$vals) {
                ++$_SESSION[$this->prefix]['flash'][$name]['counter'];
                if($_SESSION[$this->prefix]['flash'][$name]['counter']>1) {
                    unset($_SESSION[$this->prefix]['flash'][$name]);
                }
            }
        }
    }

    /**
     * @param $session_var
     * @return bool
     */
    function get_session($session_var) {
        return ((isset($_SESSION[$this->prefix][$session_var])) ? $_SESSION[$this->prefix][$session_var] : false);
    }

    /**
     * @param $session_var
     * @param $value
     */
    function set_session($session_var,$value) {
        $_SESSION[$this->prefix][$session_var] = $value;
    }

    /**
     * @param $cookie_name
     * @return bool
     */
    function get_cookie($cookie_name) {
        return ((isset($_COOKIE[$cookie_name])) ? $_COOKIE[$cookie_name] : false);
    }

    /**
     * @param $cookie_name
     * @param $value
     * @param int $time
     */
    function set_cookie($cookie_name,$value, $time = 86400) {
        setcookie($cookie_name,$value,time()+$time,'/');
    }

    /**
     *
     */
    function delete_session() {
        $session_vars = func_get_args();
        foreach($session_vars as $session_var) {
            if($this->get($session_var)||is_array($this->get($session_var))) {
                unset($_SESSION[$this->prefix][$session_var]);
            }
        }
    }

    /**
     *
     */
    function delete_cookie() {
        $cookies = func_get_args();
        foreach($cookies as $cookie) {
            if($this->get_cookie($cookie)) {
                setcookie($cookie,'del',time()-3600,'/');
            }
        }
    }

    /**
     * @param $flash_var
     * @param $value
     */
    function set_flash($flash_var,$value) {
        $_SESSION[$this->prefix]['flash'][$flash_var] = array('val'=>$value,'counter'=>0);
    }

    /**
     * @param $flash_var
     * @return bool
     */
    function get_flash($flash_var) {
        return ((isset($_SESSION[$this->prefix]['flash'][$flash_var]['val'])) ? $_SESSION[$this->prefix]['flash'][$flash_var]['val'] : false);
    }

}