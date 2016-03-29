<?php

use Wharf\Project;
use Wharf\Project\EnvFile;
use Wharf\Containers\Container;
use Wharf\Project\AppDirectory;
use League\Flysystem\Filesystem;
use Wharf\Containers\DbContainer;
use Wharf\Containers\WebContainer;
use Wharf\Project\DockerComposeYml;
use League\Flysystem\Memory\MemoryAdapter;

class ProjectTest extends PHPUnit_Framework_TestCase
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

        $project = Project::onCurrentDirectory();

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


    private function dummyProject()
    {
        $this->fileSystem = new Filesystem(new MemoryAdapter);

        return Project::onFilesystem($this->fileSystem);
    }

    /** @test */
    function it_returns_the_env_file()
    {
        $project = $this->dummyProject();

        $this->fileSystem->write('.env', '');

        $this->assertInstanceOf(EnvFile::class, $project->envFile());
    }

    /** @test */
    function it_returns_an_empty_env_file_if_not_present()
    {
        $project = $this->dummyProject();

        $this->assertInstanceOf('Wharf\Project\EnvFile', $project->envFile());
        $this->assertEmpty($project->envFile());
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

        $this->fileSystem->put('.env', '');

        $project->setEnvVariable('ANOTHER', 'try');

        $this->assertContains('ANOTHER=try', (string) $this->fileSystem->read('.env'));
    }

    /** @test */
    function it_sets_a_db_container()
    {
        $dbContainer = Container::db(['image' => 'postgres']);

        $this->project->addContainer($dbContainer);

        $this->assertInstanceOf(DbContainer::class, $this->project->db());
    }

    /** @test */
    function it_should_detect_the_public_directory()
    {
        $this->fileSystem->write('public/index.php', '');

        $this->assertEquals('public', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_web_directory()
    {
        $this->fileSystem->write('web/index.php', '');

        $this->assertEquals('web', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_wwww_directory()
    {
        $this->fileSystem->write('www/index.php', '');

        $this->assertEquals('www', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_public_directory_in_priority_then_the_web_then_the_www()
    {
        $this->fileSystem->write('www/index.php', '');
        $this->assertEquals('www', $this->project->detectDirectoryToServe());
        $this->fileSystem->write('web/index.php', '');
        $this->assertEquals('web', $this->project->detectDirectoryToServe());
        $this->fileSystem->write('public/index.php', '');
        $this->assertEquals('public', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_return_the_web_container()
    {
        $this->assertInstanceOf(WebContainer::class, $this->project->web());
    }

    /** @test */
    function it_should_return_a_new_web_container_if_it_does_not_exist()
    {
        $webContainer = $this->project->web();

        $this->assertTrue($webContainer->isNew());
    }
}
