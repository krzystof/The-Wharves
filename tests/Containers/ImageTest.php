<?php

namespace WharfTest;

use Wharf\Containers\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_should_be_constructed_from_a_string()
    {
        $image = Image::make('web:nginx');

        $this->assertInstanceOf(Image::class, $image);
    }

    /** @test */
    function it_should_return_its_name()
    {
        $image = Image::make('web:nginx');

        $this->assertEquals('nginx', $image->name());
    }

    /** @test */
    function it_should_return_its_tag()
    {
        $image = Image::make('web:nginx:1.8.1');

        $this->assertEquals('1.8.1', $image->tag());
    }

    /** @test */
    function it_can_be_converted_to_a_string()
    {
        $image = Image::make('web:nginx:1.8.1');

        $this->assertEquals('nginx:1.8.1', $image->__toString());
    }

    /** @test @expectedException Exception */
    function it_should_throw_an_exception_if_the_service_is_invalid()
    {
        Image::make('msdos:nginx');
    }

    /** @test @expectedException Exception */
    function it_should_throw_an_exception_if_the_image_is_not_compatible_with_the_service()
    {
        Image::make('web:mysql');
    }

    /** @test */
    function it_should_set_the_latest_available_version_if_not_provided()
    {
        $image = Image::make('web:nginx');

        $this->assertEquals('1.8.1', $image->tag());
    }

    /** @test @expectedException Exception*/
    function it_should_not_be_created_with_unsupported_version()
    {
        Image::make('web:nginx:0.0.1');
    }

    /** @test */
    function it_should_returns_a_null_image()
    {
        $emptyImage = Image::makeEmpty();

        $this->assertEquals('not_set', $emptyImage->name());
        $this->assertEquals('not_set', $emptyImage->tag());
    }

    /** @test */
    function it_should_list_available_databases()
    {
        $databases = Image::show('db');

        $this->assertContains('mysql', $databases);
        $this->assertContains('postgres', $databases);
    }

    /** @test */
    function it_should_change_a_version()
    {
        $image = Image::make('php:php:7.0');

        $image = $image->versionTo('5.6');

        $this->assertEquals('php:5.6', $image->__toString());
    }

    /** @test */
    function it_should_compare_to_the_same_image()
    {
        $image = Image::make('php:php:7.0');

        $this->assertFalse($image->notSameAs($image));
    }

    /** @test */
    function it_should_compare_to_the_different_image()
    {
        $imageA = Image::make('php:php:7.0');
        $imageB = Image::make('php:php:5.6');

        $this->assertTrue($imageA->notSameAs($imageB));
    }
}
