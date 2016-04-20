<?php

namespace WharfTest;

use Wharf\DockerComposeYml;
use Wharf\Containers\Container;
use Symfony\Component\Yaml\Yaml;
use Wharf\Containers\EmptyContainer;

class DockerComposeYmlTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_returns_a_container_by_name()
    {
        $dockerComposeYml = new DockerComposeYml($this->getStubbedFile());

        $container = $dockerComposeYml->container('php');

        $this->assertInstanceOf(Container::class, $container);
        $this->assertEquals('php', $container->service());
    }

    private function getStubbedFile()
    {
        return Yaml::parse(file_get_contents(dirname(__DIR__).'/stubs/docker-compose-v1.yml'));
    }

    /** @test */
    function it_returns_an_empty_container_if_it_does_not_exists()
    {
        $dockerComposeYml = new DockerComposeYml($this->getStubbedFile());

        $container = $dockerComposeYml->container('invalid');

        $this->assertInstanceOf(EmptyContainer::class, $container);
    }

    /** @test */
    function it_returns_a_default_container_if_the_file_is_empty()
    {
        $dockerComposeYml = new DockerComposeYml;

        $this->assertInstanceOf(Container::class, $dockerComposeYml->container('php'));
    }
}
