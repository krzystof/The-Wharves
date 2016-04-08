<?php

namespace WharfTest\Containers;

use Wharf\Project\Config;
use Wharf\Containers\Image;
use Wharf\Containers\WharfContainers;
use Symfony\Component\Console\Output\BufferedOutput;

class WebContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_should_display_that_infos_on_new_containers()
    {
        $output = new BufferedOutput;

        WharfContainers::web()->displayTo($output);

        $infos = $output->fetch();

        $this->assertContains('About: web container (NEW)', $infos);
        $this->assertContains('Config: ERROR', $infos);
        $this->assertContains('This container does not exist.', $infos);
    }

    function setUp()
    {
        $this->webContainer = WharfContainers::web(['image' => 'nginx:1.8.1']);

        $this->output = new BufferedOutput;

        $this->webContainer->displayTo($this->output);

        $this->infos = $this->output->fetch();
    }

    /** @test */
    function it_should_display_that_it_is_an_existing_container()
    {
        $this->assertContains('About: web container (SAVED)', $this->infos);
    }

    /** @test */
    function it_should_display_that_it_has_changed_when_updated()
    {
        $this->webContainer->environmentFrom([]);

        $this->webContainer->displayTo($this->output);

        $this->infos = $this->output->fetch();

        $this->assertContains('About: web container (UPDATED)', $this->infos);
    }

    /** @test */
    function it_should_set_environment_variables()
    {
        $this->webContainer->environmentFrom(['APP_URL' => 'something.dev', 'DIRECTORY' => 'web']);

        $this->assertEquals('something.dev', $this->webContainer->env('APP_URL'));
        $this->assertEquals('web', $this->webContainer->env('DIRECTORY'));
    }

    /** @test */
    function it_should_be_configurable_additional_settings()
    {
        $this->webContainer->environmentFrom(['APP_URL' => 'something.dev']);

        $this->webContainer->configure(['DIRECTORY' => 'web']);

        $this->assertEquals('something.dev', $this->webContainer->env('APP_URL'));
        $this->assertEquals('web', $this->webContainer->env('DIRECTORY'));
    }

   /** @test */
   function it_should_see_that_env_variables_are_set()
   {
       $this->webContainer->environmentFrom(['APP_URL' => 'something.dev']);

       $this->assertTrue($this->webContainer->has('APP_URL'));
       $this->assertFalse($this->webContainer->has('DIRECTORY'));
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
        $this->assertEquals(null, $this->webContainer->env('RANDOM_SHIT'));
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
        $this->assertContains('Version: 1.8.1', $this->infos);
    }

    /** @test */
    function it_should_display_unset_app_url()
    {
        $this->assertContains("APP_URL:\tnot_set", $this->infos);
    }

    /** @test */
    function it_should_display_an_unset_directory_to_serve()
    {
        $this->assertContains("DIRECTORY:\tnot_set", $this->infos);
    }

    /** @test */
    function it_should_display_its_configuration()
    {
        $this->webContainer->configure(['APP_URL' => 'someurl']);
        $this->webContainer->configure(['DIRECTORY' => 'somedir']);

        $this->webContainer->displayTo($this->output);
        $this->infos = $this->output->fetch();

        $this->assertContains('Config: OK', $this->infos);
        $this->assertContains("APP_URL:\tsomeurl", $this->infos);
        // $this->assertContains('DIRECTORY: somedir', $this->infos);
    }

    /** @test */
    function it_should_returns_an_empty_string_for_an_unset_variable()
    {
        $this->assertEquals('not_set', $this->webContainer->env('DIRECTORY'));
    }

    /** @test */
    function it_should_only_set_environment_variables_for_its_settings()
    {
        $config = new Config(['APP_URL' => 'yes', 'DB_USER' => 'no']);

        $this->webContainer->environmentFrom($config);

        $this->assertEquals('yes', $this->webContainer->env('APP_URL'));
        $this->assertEquals(null, $this->webContainer->env('DB_USER'));
    }

    /** @test */
    function it_should_set_an_image()
    {
        $container = WharfContainers::web()->image('nginx');

        $this->assertInstanceOf(Image::class, $container->image());
    }

    /** @test @expectedException Exception */
    function it_should_not_set_an_image_for_a_different_service()
    {
        WharfContainers::web()->image('mysql');
    }
}
