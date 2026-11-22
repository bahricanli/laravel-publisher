<?php

namespace BahriCanli\Publisher\Tests;

use BahriCanli\Publisher\PublisherServiceProvider;
use BahriCanli\Publisher\Tests\Fixtures\Page;
use BahriCanli\Publisher\Tests\Fixtures\Post;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [PublisherServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('bahricanli-publisher.token', 'test-token');
        $app['config']->set('bahricanli-publisher.post_model', Post::class);
        $app['config']->set('bahricanli-publisher.page_model', null);
        $app['config']->set('bahricanli-publisher.site_url', 'https://example.com');
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('featured_image_url')->nullable();
            $table->string('status')->default('draft');
            $table->text('categories')->nullable();
            $table->text('tags')->nullable();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('featured_image_url')->nullable();
            $table->string('status')->default('draft');
        });
    }
}
