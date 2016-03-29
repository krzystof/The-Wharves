<?php

namespace WharfTest\Containers;

use Wharf\Project\Config;
use Wharf\Containers\Container;
use Wharf\Containers\WebContainer;
use Symfony\Component\Console\Output\BufferedOutput;

class WebContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_should_display_that_it_is_new()
    {
        $output = new BufferedOutput;
        Container::web()->displayTo($output);
        $infos = $output->fetch();

        $this->assertContains('About: web container (NEW)', $infos);
        $this->assertContains('Config: ERROR', $infos);
        $this->assertContains('This container does not exist.', $infos);
    }

    function setUp()
    {
        $this->webContainer = Container::web(['image' => 'nginx:5.1']);

        $this->output = new BufferedOutput;

        $this->webContainer->displayTo($this->output);

        $this->infos = $this->output->fetch();
    }

    /** @test */
    function it_should_display_that_it_is_saved()
    {
        $this->assertContains('About: web container (SAVED)', $this->infos);
    }

    /** @test */
    function it_should_display_that_it_has_changed()
    {
        $this->webContainer->environmentFrom([]);

        $this->webContainer->displayTo($this->output);

        $this->infos = $this->output->fetch();

        $this->assertContains('About: web container (CHANGED)', $this->infos);
    }

    /** @test */
    function it_should_set_environment_variables()
    {
        $this->webContainer->environmentFrom(['APP_URL' => 'something.dev', 'DIRECTORY' => 'web']);

        $this->assertEquals('something.dev', $this->webContainer->env('APP_URL'));
        $this->assertEquals('web', $this->webContainer->env('DIRECTORY'));
    }

    /** @test */
    function it_should_be_configurable()
    {
        $this->webContainer->environmentFrom(['APP_URL' => 'something.dev']);

        $this->webContainer->configure(['DIRECTORY' => 'web']);

        $this->assertEquals('something.dev', $this->webContainer->env('APP_URL'));
        $this->assertEquals('web', $this->webContainer->env('DIRECTORY'));
    }

    /** @test */
    function it_should_overwrite_setting_but_keep_others()
    {
        $this->webContainer->environmentFrom(['APP_URL' => 'something.dev', 'DIRECTORY' => 'web']);

        $this->webContainer->configure(['DIRECTORY' => 'www']);

        $this->assertEquals('something.dev', $this->webContainer->env('APP_URL'));
        $this->assertEquals('www', $this->webContainer->env('DIRECTORY'));
    }

    /** @test */
    function it_should_not_configure_invalid_settings()
    {
        $this->webContainer->configure(['RANDOM_SHIT' => 'eat_that']);

        $this->assertFalse($this->webContainer->has('RANDOM_SHIT'));
        $this->assertEquals('', $this->webContainer->env('RANDOM_SHIT'));
    }

   /** @test */
   function it_should_see_that_env_variables_are_set()
   {
       $this->webContainer->environmentFrom(['APP_URL' => 'something.dev']);

       $this->assertTrue($this->webContainer->has('APP_URL'));
       $this->assertFalse($this->webContainer->has('DIRECTORY'));
   }

    /** @test */
    function it_should_replace_env_variable_and_keep_existing_one()
    {
        $this->webContainer->environmentFrom(['APP_URL' => 'something.dev']);
        $this->webContainer->environmentFrom(['DIRECTORY' => 'web']);

        $this->assertEquals('something.dev', $this->webContainer->env('APP_URL'));
        $this->assertEquals('web', $this->webContainer->env('DIRECTORY'));
    }

    /** @test */
    function it_should_display_that_the_config_is_not_valid()
    {
        $this->assertContains('Config: ERROR', $this->infos);
    }

    /** @test */
    function it_should_display_its_image()
    {
        $this->assertContains('Image: nginx', $this->infos);
    }

    /** @test */
    function it_should_display_its_version()
    {
        $this->assertContains('Version: 5.1', $this->infos);
    }

    /** @test */
    function it_should_display_unset_app_url()
    {
        $this->markTestIncomplete();

        $this->assertContains($this->infos, 'App url: not set');
    }

    /** @test */
    function it_should_display_an_unset_directory_to_serve()
    {
        $this->markTestIncomplete();

        $this->assertContains($this->infos, 'Directory: not set');
    }

    /** @test */
    function it_should_returns_an_empty_string_for_an_unset_variable()
    {
        $this->assertEquals('', $this->webContainer->env('db_user'));
    }

    /** @test */
    function it_should_only_set_environment_variables_for_its_settings()
    {
        $config = new Config(['APP_URL' => 'yes', 'DB_USER' => 'no']);

        $this->webContainer->environmentFrom($config);

        $this->assertEquals('yes', $this->webContainer->env('APP_URL'));
        $this->assertEquals('', $this->webContainer->env('DB_USER'));
    }
}
