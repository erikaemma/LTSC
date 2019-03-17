<?php

namespace LTSC\Handler\Router;


class Route
{
    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }
}