<?php

namespace Modules\Base\Helper;

class StringHelper
{
    /**
     * Trim Slashes
     *
     * Removes any leading/trailing slashes from a string:
     *
     * /this/that/theother/
     *
     * becomes:
     *
     * this/that/theother
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public static function trim_slashes($str)
    {
        return trim($str, '/');
    }

    /**
     * Strip Slashes
     *
     * Removes slashes contained in a string or in an array
     *
     * @access	public
     * @param	mixed	string or array
     * @return	mixed	string or array
     */
    public static function strip_slashes($str)
    {
        if (is_array($str))
        {
            foreach ($str as $key => $val)
            {
                $str[$key] = strip_slashes($val);
            }
        }
        else
        {
            $str = stripslashes($str);
        }

        return $str;
    }

    /**
     * Strip Quotes
     *
     * Removes single and double quotes from a string
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public static function strip_quotes($str)
    {
        return str_replace(array('"', "'"), '', $str);
    }

    /**
     * Quotes to Entities
     *
     * Converts single and double quotes to entities
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public static function quotes_to_entities($str)
    {
        return str_replace(array("\'","\"","'",'"'), array("&#39;","&quot;","&#39;","&quot;"), $str);
    }

    /**
     * Reduce Double Slashes
     *
     * Converts double slashes in a string to a single slash,
     * except those found in http://
     *
     * http://www.some-site.com//index.php
     *
     * becomes:
     *
     * http://www.some-site.com/index.php
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public static function reduce_double_slashes($str)
    {
        return preg_replace("#(^|[^:])//+#", "\\1/", $str);
    }

    /**
     * Reduce Multiples
     *
     * Reduces multiple instances of a particular character.  Example:
     *
     * Fred, Bill,, Joe, Jimmy
     *
     * becomes:
     *
     * Fred, Bill, Joe, Jimmy
     *
     * @access	public
     * @param	string
     * @param	string	the character you wish to reduce
     * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
     * @return	string
     */
    public static function reduce_multiples($str, $character = ',', $trim = FALSE)
    {
        $str = preg_replace('#'.preg_quote($character, '#').'{2,}#', $character, $str);

        if ($trim === TRUE)
        {
            $str = trim($str, $character);
        }

        return $str;
    }

    /**
     * Add's _1 to a string or increment the ending number to allow _2, _3, etc
     *
     * @param   string  $str  required
     * @param   string  $separator  What should the duplicate number be appended with
     * @param   string  $first  Which number should be used for the first dupe increment
     * @return  string
     */
    public static function increment_string($str, $separator = '_', $first = 1)
    {
        preg_match('/(.+)'.$separator.'([0-9]+)$/', $str, $match);

        return isset($match[2]) ? $match[1].$separator.($match[2] + 1) : $str.$separator.$first;
    }

    /**
     * Alternator
     *
     * Allows strings to be alternated.  See docs...
     *
     * @access	public
     * @param	string (as many parameters as needed)
     * @return	string
     */
    public static function alternator()
    {
        static $i;

        if (func_num_args() == 0)
        {
            $i = 0;
            return '';
        }
        $args = func_get_args();
        return $args[($i++ % count($args))];
    }

    /**
     * Repeater function
     *
     * @access	public
     * @param	string
     * @param	integer	number of repeats
     * @return	string
     */
    public static function repeater($data, $num = 1)
    {
        return (($num > 0) ? str_repeat($data, $num) : '');
    }

    /**
     * @param string $pharse
     * @return array
     */
    public function extractKeyWords( $pharse = "" )
    {
        $eraseAccents      = array("!", "?", ".", ",", ":", ";", "'", "\"", "#", "$", "-", "_");
        foreach($eraseAccents as $erase) {
            $pharse = str_replace($erase, " ", $pharse);
        }

        $eraseWords   = array("de", "da", "do", "um", "uma", "uns", "umas", "o", "os", "a", "as", "á", "ás", "à", "às", "ó", "ós", "e", "és");

        $arr = explode(" ", strtolower($pharse));
        if( !is_array($arr) ) {
            $arr = array($arr);
        }

        foreach( $arr as $key => $str ) {
            if( in_array($str, $eraseWords) ) unset($arr[$key]);
            if( trim($str) == "" ) unset($arr[$key]);
        }

        return $arr;
    }
}