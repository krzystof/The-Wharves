<?php

namespace Wharf;

class ClosureFactory
{
    public function makeWritableByAll()
    {
        return function ($path) {
            return chmod($path, 0777);
        };
    }

    public function isWritableByAll()
    {
        return function ($path) {
            return file_exists($path) && substr(sprintf('%o', fileperms($path)), -1) === '7';
        };
    }
}
