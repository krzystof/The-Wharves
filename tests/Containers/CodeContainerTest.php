<?php

namespace WharfTest\Containers;

use Wharf\Containers\Image;
use Wharf\Containers\WharfContainers;

class CodeContainerTest extends \PHPUnit_Framework_TestCase
{
    function test_it_has_a_volumes_setting_by_default()
    {
        $image         = Image::make('code', 'wharf/code');
        $codeContainer = WharfContainers::make('code')->image($image);

        $containerConfig = $codeContainer->toArray();

        $this->assertArrayHasKey('volumes', $containerConfig);
        $this->assertEquals(['.:/code'], $containerConfig['volumes']);
    }
}
