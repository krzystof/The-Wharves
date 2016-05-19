<?php

namespace WharfTest\Containers;

use Wharf\Project\Config;
use Wharf\Project\EnvFile;
use Wharf\Containers\Image;
use Wharf\Containers\WharfContainers;
use Symfony\Component\Console\Output\BufferedOutput;

class WebContainerTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->webContainer = WharfContainers::make('web', ['image' => 'wharf/nginx:1.8.1'], new Envfile);

        $this->output = new BufferedOutput;

        $this->webContainer->displayTo($this->output);

        $this->infos = $this->output->fetch();
    }

    function test_it_should_display_that_it_is_an_existing_container()
    {
        $this->assertRegExp('/About\s+web container \(SAVED\)/', $this->infos);
    }

    /** @test */
    function it_should_display_when_it_is_updated()
    {
        $this->webContainer->environmentFrom([]);

        $this->webContainer->displayTo($this->output);

        $this->infos = $this->output->fetch();

        $this->assertRegExp('/About\s+web container \(UPDATED\)/', $this->infos);
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

    function test_it_should_display_that_the_config_is_not_valid()
    {
        $this->assertRegExp('/Config\s+ERROR/', $this->infos);
    }

    function test_it_should_display_its_image()
    {
        $this->assertRegExp('/Image\s+wharf\/nginx/', $this->infos);
    }

    function test_it_should_display_its_version()
    {
        $this->assertRegExp('/Version\s+1.8.1/', $this->infos);
    }

    function test_it_should_display_unset_app_url()
    {
        $this->assertRegExp('/APP_URL[-\s]+not_set/', $this->infos);
    }

    function test_it_should_display_an_unset_directory_to_serve()
    {
        $this->assertRegExp('/DIRECTORY[-\s]+not_set/', $this->infos);
    }

    function test_it_should_display_its_configuration()
    {
        $this->webContainer->configure(['APP_URL' => 'someurl']);
        $this->webContainer->configure(['DIRECTORY' => 'somedir']);

        $this->webContainer->displayTo($this->output);

        $infos = $this->output->fetch();

        $this->assertRegExp('/Config[-\s]+OK/', $infos);
        $this->assertRegExp('/APP_URL[-\s]+someurl/', $infos);
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
        $container = WharfContainers::make('web')->image('nginx');

        $this->assertInstanceOf(Image::class, $container->image());
    }

    /** @expectedException Exception */
    function test_it_should_not_set_a_wharf_image_for_a_different_service()
    {
        WharfContainers::make('web')->image('wharf/mysql');
    }

    function test_it_should_display_if_it_is_a_custom_container()
    {
        $image = Image::make('web', 'some_funky_name');

        $this->webContainer->image($image);

        $this->webContainer->displayTo($this->output);

        $infos = $this->output->fetch();

        $this->assertContains('some_funky_name', $infos);
        $this->assertRegExp('/Config\s+CUSTOM/', $infos);
    }
}
