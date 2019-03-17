<?php

namespace LTSC\Helper;


abstract class AbstractFilter
{
    abstract public function filter(AbstractRequest $request): bool;
}