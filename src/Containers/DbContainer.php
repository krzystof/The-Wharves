<?php

namespace Wharf\Containers;

class DbContainer extends Container
{
    const SERVICE = 'db';

    public static function supports($software)
    {
        return collect(['mysql', 'postgres'])->contains($software);
    }

    public static function software($software)
    {
        if (! static::supports($software)) {
            throw new \Exception(sprintf('%s is not supported.', $software));
        }

        return new static;
    }

   protected function configurables()
   {
       return collect(['DB_HOST', 'DB_USERNAME']);
   }
}
