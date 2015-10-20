<?php

namespace Modules\Base\Helper;

class ArrayHelper
{
    /**
     * Element
     *
     * Lets you determine whether an array index is set and whether it has a value.
     * If the element is empty it returns FALSE (or whatever you specify as the default value.)
     *
     * @access	public
     * @param	string
     * @param	array
     * @param	mixed
     * @return	mixed	depends on what the array contains
     */
    public function element($item, $array, $default = FALSE)
    {
        if ( ! isset($array[$item]) OR $array[$item] == "")
        {
            return $default;
        }

        return $array[$item];
    }

    /**
     * Random Element - Takes an array as input and returns a random element
     *
     * @access	public
     * @param	array
     * @return	mixed	depends on what the array contains
     */
    public function random_element($array)
    {
        if ( ! is_array($array))
        {
            return $array;
        }

        return $array[array_rand($array)];
    }

    /**
     * Elements
     *
     * Returns only the array items specified.  Will return a default value if
     * it is not set.
     *
     * @access	public
     * @param	array
     * @param	array
     * @param	mixed
     * @return	mixed	depends on what the array contains
     */
    public function elements($items, $array, $default = FALSE)
    {
        $return = array();

        if ( ! is_array($items))
        {
            $items = array($items);
        }

        foreach ($items as $item)
        {
            if (isset($array[$item]))
            {
                $return[$item] = $array[$item];
            }
            else
            {
                $return[$item] = $default;
            }
        }

        return $return;
    }
}