<?php

namespace LTSC\Helper;


interface InterfaceContainer
{
    public function set($id, $injection, int $argNums = 0, bool $cover = true, string $getInstance = 'getInstance'): bool;
    public function get($id, ...$args);
    public function has($id);
}