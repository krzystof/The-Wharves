<?php

namespace WharfTest;

use Wharf\Project;
use Wharf\Project\EnvFile;
use League\Flysystem\Filesystem;
use Wharf\Containers\DbContainer;
use Illuminate\Support\Collection;
use Wharf\Containers\WebContainer;
use Wharf\Containers\WharfContainers;
use League\Flysystem\Memory\MemoryAdapter;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter);

        $this->project = Project::onFilesystem($this->filesystem);
    }

    private function dummyProject()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter);

        return Project::onFilesystem($this->filesystem);
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

        $this->assertFalse($this->filesystem->has('docker-compose.yml'));
    }

    /** @test */
    function it_create_a_docker_compose_file_if_the_project_is_saved()
    {
        $project = $this->dummyProject();

        $project->save();

        $this->assertTrue($this->filesystem->has('docker-compose.yml'));
    }

    /** @test */
    function it_returns_the_env_file()
    {
        $project = $this->dummyProject();

        $this->filesystem->write('.env', '');

        $this->assertInstanceOf(EnvFile::class, $project->envFile());
    }

    /** @test */
    function it_returns_an_empty_env_file_if_not_present()
    {
        $project = $this->dummyProject();

        $this->assertEmpty($project->envFile());
        $this->assertInstanceOf(EnvFile::class, $project->envFile());
    }

    /** @test */
    function it_sees_if_the_env_db_host_is_localhost()
    {
        $project = $this->dummyProject();

        $this->filesystem->put('.env', 'DB_HOST=localhost');

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

        $this->filesystem->put('.env', '');

        $project->setEnvVariable('ANOTHER', 'try');

        $this->assertContains('ANOTHER=try', (string) $this->filesystem->read('.env'));
    }

    /** @test */
    function it_sets_a_db_container()
    {
        $dbContainer = WharfContainers::db(['image' => 'postgres']);

        $dbContainer->configure(['DB_USERNAME' => 'some_jerk']);

        $this->project->save($dbContainer);

        $this->assertInstanceOf(DbContainer::class, $this->project->service('db'));
    }

    /** @test */
    function it_should_detect_the_public_directory()
    {
        $this->filesystem->write('public/index.php', '');

        $this->assertEquals('public', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_web_directory()
    {
        $this->filesystem->write('web/index.php', '');

        $this->assertEquals('web', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_wwww_directory()
    {
        $this->filesystem->write('www/index.php', '');

        $this->assertEquals('www', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_detect_the_public_directory_in_priority_then_the_web_then_the_www()
    {
        $this->filesystem->write('www/index.php', '');
        $this->assertEquals('www', $this->project->detectDirectoryToServe());
        $this->filesystem->write('web/index.php', '');
        $this->assertEquals('web', $this->project->detectDirectoryToServe());
        $this->filesystem->write('public/index.php', '');
        $this->assertEquals('public', $this->project->detectDirectoryToServe());
    }

    /** @test */
    function it_should_return_the_web_container()
    {
        $this->assertInstanceOf(WebContainer::class, $this->project->service('web'));
    }

    /** @test */
    function it_should_return_a_new_web_container_if_it_does_not_exist()
    {
        $webContainer = $this->project->service('web');

        $this->assertTrue($webContainer->isNew());
    }

    /** @test @expectedException Exception*/
    function it_should_errors_if_it_saves_an_invalid_container()
    {
        $incompleteContainer = WharfContainers::web()->image('nginx');

        $this->project->save($incompleteContainer);
    }

    /** @test */
    function it_should_detect_that_it_is_a_custom_project()
    {
        $this->assertEquals('custom', $this->project->type());
    }

    /** @test */
    function it_should_detect_that_it_is_a_laravel_project()
    {
        $this->filesystem->write('artisan', 'some stuff');

        $this->assertEquals('laravel', $this->project->type());
    }
}
