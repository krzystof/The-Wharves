<?php

namespace Wharf\Containers;

use Illuminate\Support\Collection;

class EmptyContainer extends Container
{
    public function __construct($name)
    {
        $this->service = $name;
        $this->config = collect([]);
    }

    public function service()
    {
        return $this->name;
    }

    protected function configurables()
    {
        return collect([]);
    }

    protected function defaultSettings()
    {
        return new Collection;
    }

    protected function requiredSettings()
    {
        return collect([]);
    }

    public function isValid()
    {
        return false;
    }
}
