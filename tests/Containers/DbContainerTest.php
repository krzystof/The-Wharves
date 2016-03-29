<?php

namespace WharfTest\Containers;

use Wharf\Containers\Container;
use Wharf\Containers\DbContainer;

class DbContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_should_supports_mysql()
    {
        $this->assertTrue(DbContainer::supports('mysql'));
    }

    /** @test */
    function it_should_supports_postgres()
    {
        $this->assertTrue(DbContainer::supports('postgres'));
    }
}
