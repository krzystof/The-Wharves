<?php

namespace WharfTest;

use Wharf\Containers\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_should_be_constructed_from_a_string()
    {
        $image = Image::make('web', 'nginx');

        $this->assertInstanceOf(Image::class, $image);
    }

    /** @test */
    function it_should_return_its_name()
    {
        $image = Image::make('web', 'nginx');

        $this->assertEquals('nginx', $image->name());
    }

    /** @test */
    function it_should_return_its_tag()
    {
        $image = Image::make('web', 'nginx:1.8.1');

        $this->assertEquals('1.8.1', $image->tag());
    }

    /** @test */
    function it_can_be_converted_to_a_string()
    {
        $image = Image::make('web', 'nginx:1.8.1');

        $this->assertEquals('nginx:1.8.1', $image->__toString());
    }

    /** @test @expectedException Exception */
    function it_should_throw_an_exception_if_the_service_is_invalid()
    {
        Image::make('msdos', 'nginx');
    }

    /** @test */
    function it_should_be_a_custom_image_if_it_is_not_a_wharf_image()
    {
        $image = Image::make('web', 'mysql');

        $this->assertTrue($image->isCustom());
    }

    function test_it_should_set_the_latest_available_version_if_not_provided()
    {
        $image = Image::make('web', 'wharf/nginx');

        $this->assertEquals('1.8.1', $image->tag());
    }

    /** @expectedException Exception*/
    function test_it_should_not_be_created_with_unsupported_version()
    {
        Image::make('web', 'wharf/nginx:0.0.1');
    }

    function test_it_should_create_a_custom_image_if_it_is_not_prefixed_with_wharf()
    {
        $image = Image::make('web', 'nginx:0.0.1');

        $this->assertTrue($image->isCustom());
    }

    function test_it_can_returns_an_empty_image()
    {
        $emptyImage = Image::makeEmpty();

        $this->assertEquals('not_set', $emptyImage->name());
        $this->assertEquals('not_set', $emptyImage->tag());
    }

    function test_it_should_list_available_databases()
    {
        $databases = Image::show('db');

        $this->assertContains('wharf/mysql', $databases);
    }

    /** @test */
    function it_should_change_a_version()
    {
        $image = Image::make('php', 'wharf/php:7.0.5');

        $image = $image->versionTo('5.6');

        $this->assertEquals('wharf/php:5.6', $image->__toString());
    }

    /** @test */
    function it_should_compare_to_the_same_image()
    {
        $image = Image::make('php', 'php:7.0');

        $this->assertFalse($image->notSameAs($image));
    }

    /** @test */
    function it_should_compare_to_the_different_image()
    {
        $imageA = Image::make('php', 'php:7.0');
        $imageB = Image::make('php', 'php:5.6');

        $this->assertTrue($imageA->notSameAs($imageB));
    }
}
