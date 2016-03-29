<?php

namespace WharfTest\Containers;

use Wharf\Containers\Container;
use Symfony\Component\Console\Output\BufferedOutput;

class NewWebContainerTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $output = new BufferedOutput;

        $this->container = Container::web();

        $this->container->displayTo($output);

        $this->infos = $output->fetch();
    }

    /** @test */
    function it_should_display_that_it_does_not_exist()
    {
        $this->assertContains('About: web container (NEW)', $this->infos);
        $this->assertContains('Config: ERROR', $this->infos);
        $this->assertContains('This container does not exist.', $this->infos);
    }

    /** @test */
    function it_should_be_new()
    {
        $this->assertTrue($this->container->isNew());
    }
}
