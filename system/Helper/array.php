<?php

if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed   $target
     * @param  string|array  $key
     * @param  mixed   $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $segment) {
            if (is_array($target)) {
                if (! array_key_exists($segment, $target)) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif ($target instanceof ArrayAccess) {
                if (! isset($target[$segment])) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (! isset($target->{$segment})) {
                    return value($default);
                }

                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (! function_exists('str_replace_array')) {
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string  $search
     * @param  array   $replace
     * @param  string  $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = preg_replace('/'.$search.'/', $value, $subject, 1);
        }

        return $subject;
    }
}


/**
 * @param $array
 * @param array $keys
 * @return array
 */
function array_remove_keys($array, $keys = array()) {
    if(empty($array) || (! is_array($array))) {
        return $array;
    }

    if(is_string($keys) || is_numeric($keys)) {
        $keys = explode(',', str_replace(' ','',$keys));
    }

    if(! is_array($keys)) {
        return $array;
    }

    $assocKeys = array_fill_keys($keys,true);
    return array_diff_key($array, $assocKeys);
}

/**
 * @param $array
 * @param array $keys
 * @return array
 */
function array_filter_keys($array, $keys = array()) {
    if(empty($array) || (! is_array($array))) {
        return $array;
    }

    if(is_string($keys) || is_numeric($keys) ) {
        $keys = explode(',', str_replace(' ','',$keys));
    }

    if(! is_array($keys)) {
        return $array;
    }

    $assocKeys = array_fill_keys($keys,true);
    return array_intersect_key($array, $assocKeys);
}


if (!function_exists('array_column')) {
    function array_column($input, $column_key, $index_key = null)
    {
        if ($index_key !== null) {
            $keys = array();
            $i = 0; // Counter for numerical keys when key does not exist

            foreach ($input as $row) {
                if (array_key_exists($index_key, $row)) {
                    // Update counter for numerical keys
                    if (is_numeric($row[$index_key]) || is_bool($row[$index_key])) {
                        $i = max($i, (int) $row[$index_key] + 1);
                    }

                    // Get the key from a single column of the array
                    $keys[] = $row[$index_key];
                } else {
                    // The key does not exist, use numerical indexing
                    $keys[] = $i++;
                }
            }
        }

        if ($column_key !== null) {
            // Collect the values
            $values = array();
            $i = 0; // Counter for removing keys

            foreach ($input as $row) {
                if (array_key_exists($column_key, $row)) {
                    // Get the values from a single column of the input array
                    $values[] = $row[$column_key];
                    $i++;
                } elseif (isset($keys)) {
                    // Values does not exist, also drop the key for it
                    array_splice($keys, $i, 1);
                }
            }
        } else {
            // Get the full arrays
            $values = array_values($input);
        }

        if ($index_key !== null) {
            return array_combine($keys, $values);
        }

        return $values;
    }
}


/**
 * @package     BugFree
 * @version     $Id: FunctionsMain.inc.php,v 1.32 2005/09/24 11:38:37 wwccss Exp $
 *
 *
 * Sort an two-dimension array by some level two items use array_multisort() function.
 *
 * sysSortArray($Array,"Key1","SORT_ASC","SORT_RETULAR","Key2"……)
 * @author                      Chunsheng Wang <wwccss@263.net>
 * @param  array   $array_data   the array to sort.
 * @param  string  $key_name    the first item to sort by.
 * @param  string  $sort_order  the order to sort by("SORT_ASC"|"SORT_DESC")
 * @param  string  $sort_type   the sort type("SORT_REGULAR"|"SORT_NUMERIC"|"SORT_STRING")
 * @return array                sorted array.
 */
function sort_array($arr, $keyname1, $sort_order1="SORT_ASC", $sort_type1="SORT_REGULAR")
{
    if(!is_array($arr) || empty($arr))
    {
        return $arr;
    }

    // Get args number.
    $arg_count = func_num_args();
    $Key_name_list = [];
    for($i = 1;$i < $arg_count;$i ++)
    {
        $arg = func_get_arg($i);
        if(strpos($arg,"SORT") === false)
        {
            $Key_name_list[] = $arg;
            $sort_rule[]    = '$'.$arg;
        }
        else
        {
            $sort_rule[]    = $arg;
        }
    }

    // Get the values according to the keys and put them to array.
    foreach($arr as $key => $Info)
    {
        foreach($Key_name_list as $key_name)
        {
            ${$key_name}[$key] = $Info[$key_name];
        }
    }

    // Create the eval string and eval it.
    $eval_string = 'array_multisort('.join(",",$sort_rule).',$arr);';
    eval ($eval_string);
    return $arr;
}