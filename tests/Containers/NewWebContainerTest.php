<?php

namespace WharfTest\Containers;

use Wharf\Containers\WharfContainers;
use Symfony\Component\Console\Output\BufferedOutput;

class NewWebContainerTest extends \PHPUnit_Framework_TestCase
{
    function test_it_should_display_infos_on_new_containers()
    {
        $output = new BufferedOutput;

        WharfContainers::make('web')->displayTo($output);

        $infos = $output->fetch();

        $this->assertRegExp('/About\s+web container \(NEW\)/', $infos);
        $this->assertRegExp('/Config\s+ERROR/', $infos);
    }
}
