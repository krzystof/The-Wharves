<?php

namespace Wharf\Containers;

class EmptyContainer extends Container
{
    public function __construct()
    {
        $this->config = collect([]);
    }

    public static function service()
    {
        return '';
    }

    protected function configurables()
    {
        return collect([]);
    }

    protected function requiredSettings()
    {
        return collect([]);
    }

    public function isValid()
    {
        return true;
    }
}
