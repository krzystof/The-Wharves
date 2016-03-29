<?php

namespace WharfTest\Containers;

use Wharf\Containers\Container;
use Wharf\Containers\DbContainer;
use Wharf\Containers\PhpContainer;
use Wharf\Containers\WebContainer;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_checks_if_a_container_is_supported()
    {
        $this->assertTrue(Container::supports('php'));
        $this->assertFalse(Container::supports('ruby'));
    }

    /** @test */
    function it_supports_the_following_software()
    {
        $this->assertTrue(Container::supports('php'));
        $this->assertTrue(Container::supports('db'));
        $this->assertTrue(Container::supports('web'));

        $this->assertTrue(DbContainer::supports('mysql'));
    }

    /** @test */
    function it_creates_php_container()
    {
        $container = Container::php();

        $this->assertInstanceOf(PhpContainer::class, $container);
    }

    /** @test */
    function it_creates_db_container()
    {
        $container = Container::db();

        $this->assertInstanceOf(DbContainer::class, $container);
    }

    /** @test */
    function it_should_create_web_containers_with_a_valid_service()
    {
        $container = Container::web();

        $this->assertInstanceOf(WebContainer::class, $container);
        $this->assertEquals('web', $container->service());
    }

    /** @test */
    function it_should_create_empty_containers()
    {
        $container = Container::web();

        $this->assertTrue($container->isEmpty());
    }

    /** @test */
    function it_should_see_if_a_container_is_not_empty()
    {
        $container = Container::web(['image' => 'nginx']);

        $this->assertFalse($container->isEmpty());
    }

    /** @test */
    function it_should_set_the_tag_latest_by_default()
    {
        $container = Container::web(['image' => 'nginx']);

        $this->assertEquals('nginx', $container->image());
        $this->assertEquals('latest', $container->tag());
    }

    /** @test */
    function it_should_set_the_tag_if_provided()
    {
        $container = Container::web(['image' => 'nginx:2.0']);

        $this->assertEquals('nginx', $container->image());
        $this->assertEquals('2.0', $container->tag());
    }

    /**
     * @test
     * @expectedException Exception
     */
    function it_errors_if_the_image_is_invalid()
    {
        Container::db(['image' => 'excel']);
    }

    /** @test */
    function it_creates_new_containers_with_unset_image()
    {
        $container = Container::db();

        $this->assertEquals('not set', $container->image());
    }
}
