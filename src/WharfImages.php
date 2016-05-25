<?php

namespace Wharf;

class WharfImages
{
    const NOT_SET = 'not_set';

    public static function all()
    {
        return collect([
            'web' => [
                'wharf/nginx' => ['1.8']
            ],
            'php' => [
                'wharf/php' => ['7.0', '5.6', '5.5']
            ],
            'db' => [
                'wharf/mysql' => ['5.7', '5.6', '5.5'],
                'postgres' => '1.0.0'
            ],
            'code' => ['wharf/code' => 'latest'],
            'not_set' => ['not_set' => self::NOT_SET],
        ]);
    }
}

