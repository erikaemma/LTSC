<?php

if(!function_exists('fullPath')) {
    function fullPath(string $path) {
        $last = substr($path, strlen($path) - 1);
        if($last != DIRECTORY_SEPARATOR)
            return $path . DIRECTORY_SEPARATOR;
    }
}