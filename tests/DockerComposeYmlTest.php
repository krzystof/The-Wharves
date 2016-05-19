<?php

namespace WharfTest;

use Wharf\Project\EnvFile;
use Wharf\DockerComposeYml;
use Wharf\Containers\Container;
use Symfony\Component\Yaml\Yaml;
use Wharf\Containers\EmptyContainer;

class DockerComposeYmlTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->dockerComposeYml = new DockerComposeYml($this->getStubbedFile());
    }

    function test_it_returns_a_container_by_name()
    {
        $container = $this->dockerComposeYml->container('php', new EnvFile);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertEquals('php', $container->service());
    }

    private function getStubbedFile()
    {
        return Yaml::parse(file_get_contents(dirname(__DIR__).'/stubs/docker-compose-v1.yml'));
    }

    function test_it_returns_an_empty_container_if_it_does_not_exists()
    {
        $container = $this->dockerComposeYml->container('i_dont_exist');

        $this->assertInstanceOf(EmptyContainer::class, $container);
    }

    function test_it_returns_a_default_container_if_the_file_is_empty()
    {
        $dockerComposeYml = new DockerComposeYml;

        $this->assertInstanceOf(Container::class, $dockerComposeYml->container('php'));
    }

    function test_it_should_remove_a_container()
    {
        $this->dockerComposeYml->removeContainer('db');

        $brandNewContainer = $this->dockerComposeYml->container('db');

        $this->assertTrue($brandNewContainer->isNew());
    }
}
