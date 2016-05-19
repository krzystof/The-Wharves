<?php

namespace WharfTest\Containers;

use Wharf\Containers\Image;
use Wharf\Containers\DbContainer;
use Wharf\Containers\PhpContainer;
use Wharf\Containers\WebContainer;
use Wharf\Containers\WharfContainers;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_creates_php_containers()
    {
        $container = WharfContainers::make('php');

        $this->assertInstanceOf(PhpContainer::class, $container);
    }

    /** @test */
    function it_creates_db_containers()
    {
        $container = WharfContainers::make('db');

        $this->assertInstanceOf(DbContainer::class, $container);
    }

    /** @test */
    function it_should_create_web_containers_with_a_valid_service()
    {
        $container = WharfContainers::make('web');

        $this->assertInstanceOf(WebContainer::class, $container);
        $this->assertEquals('web', $container->service());
    }

    function test_it_should_create_new_containers()
    {
        $container = WharfContainers::make('web', null, null);

        $this->assertTrue($container->isNew());
    }

    function test_it_should_set_the_image_appropriatly()
    {
        $container = WharfContainers::make('web', ['image' => 'nginx:1.8.1']);

        $this->assertInstanceOf(Image::class, $container->image());
        $this->assertEquals('nginx:1.8.1', $container->image()->__toString());
    }

    function test_it_should_always_have_an_instance_of_an_image()
    {
        $container = WharfContainers::make('web');

        $this->assertInstanceOf(Image::class, $container->image());
    }

    function test_it_creates_new_containers_with_unset_image()
    {
        $container = WharfContainers::make('db');

        $this->assertEquals('not_set', $container->image()->name());
    }
}
