<?php

use Wharf\Wharf;
use Wharf\Project\Container;
use Wharf\Project\AppDirectory;
use League\Flysystem\Filesystem;
use Wharf\Project\DockerComposeYml;
use League\Flysystem\Memory\MemoryAdapter;

class WharfTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->project = $this->dummyProject();
    }

    /** @test */
    function it_can_be_constructed_on_the_current_directory()
    {
        if (file_exists('docker-compose.yml')) {
            unlink('docker-compose.yml');
        }

        $project = Wharf::onCurrentDirectory();

        $project->save();

        $this->assertFileExists('docker-compose.yml');

        unlink('docker-compose.yml');
    }

    /** @test */
    function it_does_not_create_a_docker_compose_file_if_the_project_is_not_saved()
    {
        $this->dummyProject();

        $this->assertFalse($this->fileSystem->has('docker-compose.yml'));
    }

    /** @test */
    function it_create_a_docker_compose_file_if_the_project_is_saved()
    {
        $project = $this->dummyProject();

        $project->save();

        $this->assertTrue($this->fileSystem->has('docker-compose.yml'));
    }


    private function dummyProject($envFile = null)
    {
        $this->fileSystem = new Filesystem(new MemoryAdapter);

        return Wharf::onFilesystem($this->fileSystem, $envFile);
    }

    /** @test */
    function it_returns_the_php_version()
    {
        $project = $this->dummyProject();

        $this->assertEquals('7.0', $project->phpVersion());
    }

    /** @test */
    function it_returns_the_supported_php_versions()
    {
        $this->assertEquals(['5.4', '5.5', '5.6', '7.0'], Wharf::supportedPhpVersions());
    }

    /** @test */
    function it_returns_the_supported_db_systems()
    {
        $this->assertEquals(['mysql', 'postgres', 'sqlite', 'sql server'], Wharf::supportedDbSystems());
    }

    /** @test */
    function it_is_created_with_php_7_if_no_docker_compose_file_was_found()
    {
        $project = $this->dummyProject();

        $this->assertEquals('7.0', $project->phpVersion());
    }

    /** @test */
    function it_sets_the_php_version()
    {
        $project = $this->dummyProject();

        $project->setPhpVersion('5.4');

        $this->assertEquals('5.4', $project->phpVersion());
    }

    /**
     * @test
     * @expectedException Exception
     */
    function it_accept_only_valid_php_versions()
    {
        $project = $this->dummyProject();

        $project->setPhpVersion('woooohoooo!');
    }

    /** @test */
    function it_gets_the_current_database_system()
    {
        $project = $this->dummyProject();

        $this->assertEquals('mysql', $project->dbSystem());
    }

    /** @test */
    function it_gets_the_current_database_system_version()
    {
        $project = $this->dummyProject();

        $this->assertEquals('5.7', $project->dbSystemVersion());
    }

    /** @test */
    function it_creates_an_env_file_if_no_env_file_is_present()
    {
        $project = $this->dummyProject();

        $this->assertTrue($project->detectEnvFile());
    }

    /** @test */
    function it_returns_the_env_file()
    {
        $project = $this->dummyProject();

        $this->fileSystem->write('.env', '');

        $this->assertInstanceOf('Wharf\Project\EnvFile', $project->envFile());
    }

    /** @test */
    function it_creates_an_env_file_if_it_does_not_exists()
    {
        $project = $this->dummyProject();

        $project->envFile();

        $this->assertTrue($this->fileSystem->has('.env'));
    }

    /** @test */
    function it_create_a_wharf_section_on_the_env_file()
    {
        $project = $this->dummyProject();

        $project->envFile();

        $this->assertContains('# WHARF ENV', $this->fileSystem->read('.env'));
    }

    /** @test */
    function it_sees_if_the_env_db_host_is_localhost()
    {
        $project = $this->dummyProject();

        $this->fileSystem->put('.env', 'DB_HOST=localhost');

        $this->assertTrue($project->dbIsLocalhost());
    }

    /** @test */
    function it_can_set_environment_variable_in_the_env()
    {
        $project = $this->dummyProject();

        $project->setEnvVariable('SOME_NEW_VARIABLE', 'sweet');

        $this->assertEquals('sweet', $project->envFile()->get('SOME_NEW_VARIABLE'));
    }

    /** @test */
    function it_persists_changes_in_the_env_file()
    {
        $project = $this->dummyProject();

        $project->setEnvVariable('ANOTHER', 'try');

        $this->assertContains('ANOTHER=try', (string) $this->fileSystem->read('.env'));
    }

    /** @test */
    function it_sets_a_db_container()
    {
        $this->project->setDb(Container::database('postgres'));

        $this->assertEquals('postgres', $this->project->dbSystem());
    }
}
