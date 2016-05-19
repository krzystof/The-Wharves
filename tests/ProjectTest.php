<?php

namespace WharfTest;

use Wharf\Project;
use Wharf\Project\EnvFile;
use Wharf\Containers\DbContainer;
use Illuminate\Support\Collection;
use Wharf\Containers\WebContainer;
use Wharf\Containers\WharfContainers;
use WharfTests\Doubles\InMemoryFilesystem;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->filesystem = new InMemoryFilesystem;

        $this->project = new Project($this->filesystem, __DIR__);
    }

    /** @test */
    function it_can_be_constructed_on_the_current_directory()
    {
        if (file_exists('docker-compose.yml')) {
            unlink('docker-compose.yml');
        }

        $project = Project::onDirectory(getcwd());

        $project->save();

        $this->assertFileExists('docker-compose.yml');

        unlink('docker-compose.yml');
    }

    function test_it_does_not_create_a_docker_compose_file_if_the_project_is_not_saved()
    {
        $this->assertFalse($this->filesystem->exists('docker-compose.yml'));
    }

    function test_it_create_a_docker_compose_file_if_the_project_is_saved()
    {
        $this->project->save();

        $this->assertTrue($this->filesystem->exists('docker-compose.yml'));
    }

    /** @test */
    function it_returns_the_env_file()
    {
        $this->filesystem->put('.env', '');

        $this->assertInstanceOf(EnvFile::class, $this->project->envFile());
    }

    /** @test */
    function it_returns_an_empty_env_file_if_not_present()
    {
        $this->assertEmpty($this->project->envFile());
        $this->assertInstanceOf(EnvFile::class, $this->project->envFile());
    }

    function test_it_sees_if_the_env_db_host_is_localhost()
    {
        $this->filesystem->put('.env', 'DB_HOST=localhost');

        $project = new Project($this->filesystem, __DIR__);

        $this->assertTrue($project->dbIsLocalhost());
    }

    /** @test */
    function it_can_set_environment_variable_in_the_env()
    {
        $this->project->setEnvVariable('SOME_NEW_VARIABLE', 'sweet');

        $this->assertEquals('sweet', $this->project->envFile()->get('SOME_NEW_VARIABLE'));
    }

    /** @test */
    function it_persists_changes_in_the_env_file()
    {
        $this->filesystem->put('.env', 'SOME=value');

        $this->project->setEnvVariable('ANOTHER', 'try');

        $this->assertContains('ANOTHER=try', $this->filesystem->get('.env'));
    }

    function test_it_sets_a_db_container()
    {
        $dbContainer = WharfContainers::make('db', ['image' => 'postgres']);

        $dbContainer->configure(['DB_USERNAME' => 'some_jerk']);

        $this->project->save($dbContainer);

        $this->assertInstanceOf(DbContainer::class, $this->project->service('db'));
    }

    /** @test */
    function it_should_detect_the_public_directory()
    {
        $this->filesystem->makeDirectory('public/');

        $this->assertEquals('public', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_web_directory()
    {
        $this->filesystem->put('web/index.php', '');

        $this->assertEquals('web', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_wwww_directory()
    {
        $this->filesystem->put('www/index.php', '');

        $this->assertEquals('www', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_public_directory_in_priority_then_the_web_then_the_www()
    {
        $this->filesystem->put('www/index.php', '');
        $this->assertEquals('www', $this->project->detectDirectoryToServe());
        $this->filesystem->put('web/index.php', '');
        $this->assertEquals('web', $this->project->detectDirectoryToServe());
        $this->filesystem->put('public/index.php', '');
        $this->assertEquals('public', $this->project->detectDirectoryToServe());
    }

    function test_it_should_return_a_new_web_container_if_it_does_not_exist()
    {
        $webContainer = $this->project->service('web');

        $this->assertTrue($webContainer->isNew());
    }

    /** @test @expectedException Exception*/
    function it_should_errors_if_it_saves_an_invalid_container()
    {
        $incompleteContainer = WharfContainers::make('web')->image('nginx');

        $this->project->save($incompleteContainer);
    }

    /** @test */
    function it_should_detect_that_it_is_a_custom_project()
    {
        $this->assertEquals('custom', $this->project->type()->name());
    }

    /** @test */
    function it_should_detect_that_it_is_a_laravel_project()
    {
        $this->filesystem->put('artisan', 'some stuff');

        $this->assertEquals('laravel', $this->project->type()->name());
    }
}
