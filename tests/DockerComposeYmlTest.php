<?php

use Wharf\Project\Container;
use Symfony\Component\Yaml\Yaml;
use Wharf\Project\DockerComposeYml;

class DockerComposeYmlTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function it_returns_a_container_by_name()
    {
        $dockerComposeYml = new DockerComposeYml($this->getStubbedFile());

        $container = $dockerComposeYml->container('php');

        $this->assertInstanceOf('Wharf\Project\Container', $container);
        $this->assertEquals('php', $container->name());
    }

    private function getStubbedFile()
    {
        return Yaml::parse(file_get_contents(dirname(__DIR__).'/stubs/docker-compose-v1.yml'));
    }

    /**
     * @test
     * @expectedException Exception
     */
    function it_throws_an_exception_if_the_container_is_invalid()
    {
        $dockerComposeYml = new DockerComposeYml($this->getStubbedFile());

        $dockerComposeYml->container('invalid');
    }

    /** @test */
    function it_returns_a_default_container_if_the_file_is_empty()
    {
        $dockerComposeYml = new DockerComposeYml;

        $this->assertInstanceOf(Container::class, $dockerComposeYml->container('php'));
    }

    // /** @test */
    // function it_can_set_a_different_version_of_php()
    // {
    //     $parsedYmlFile = Yaml::parse('tests/Fixtures/docker-compose-v1.yml');

    //     $dockerComposeYml = new DockerComposeYml($parsedYmlFile);

    //     $dockerComposeYml->setPhp('5.4');

    //     $container = $dockerComposeYml->container('php');

    //     $this->assertEquals('5.4', $container->version());
    // }
}
