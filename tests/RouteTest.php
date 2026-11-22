<?php

namespace BahriCanli\Publisher\Tests;

class RouteTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('bahricanli-publisher.prefix', 'api/content');
    }

    public function test_routes_use_the_configured_prefix()
    {
        $this->postJson('/api/content/blog/create', [])
            ->assertStatus(403);

        $this->postJson('/cm/blog/create', [])->assertNotFound();
    }

    public function test_the_published_config_file_matches_the_package_config_key()
    {
        $published = config_path('bahricanli-publisher.php');

        try {
            $this->artisan('vendor:publish', [
                '--tag'   => 'bahricanli-publisher',
                '--force' => true,
            ])->assertExitCode(0);

            $this->assertFileExists($published);
        } finally {
            if (file_exists($published)) {
                unlink($published);
            }
        }
    }
}
