<?php

namespace Wharf\Containers;

use Illuminate\Support\Collection;

class CodeContainer extends Container
{
    public function service()
    {
        return 'code';
    }

    protected function defaultSettings()
    {
        return new Collection([
            'volumes' => ['.:/code'],
        ]);
    }

    protected function configurables()
    {
        return new Collection;
    }

    protected function requiredSettings()
    {
        return new Collection;
    }
}
