<?php

namespace BahriCanli\Publisher\Tests;

class PublisherMiddlewareTest extends TestCase
{
    public function test_it_rejects_request_when_token_is_not_configured()
    {
        config(['bahricanli-publisher.token' => '']);

        $this->postJson('/cm/blog/create', ['title' => 'A', 'content' => 'B'])
            ->assertStatus(500)
            ->assertJson(['error' => 'CM_PUBLISHER_TOKEN tanımlı değil.']);
    }

    public function test_it_rejects_request_without_token()
    {
        $this->postJson('/cm/blog/create', ['title' => 'A', 'content' => 'B'])
            ->assertStatus(403)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_it_rejects_request_with_wrong_token()
    {
        $this->postJson('/cm/blog/create', ['title' => 'A', 'content' => 'B'], [
            'X-Content-Manager-Token' => 'wrong-token',
        ])->assertStatus(403);
    }

    public function test_it_accepts_the_token_header()
    {
        $this->postJson('/cm/blog/create', ['title' => 'A', 'content' => 'B'], [
            'X-Content-Manager-Token' => 'test-token',
        ])->assertStatus(201);
    }

    public function test_it_accepts_the_token_from_the_request_body()
    {
        $this->postJson('/cm/blog/create', [
            'title'     => 'A',
            'content'   => 'B',
            '_cm_token' => 'test-token',
        ])->assertStatus(201);
    }

    public function test_it_prefers_the_header_over_the_request_body()
    {
        $this->postJson('/cm/blog/create', [
            'title'     => 'A',
            'content'   => 'B',
            '_cm_token' => 'test-token',
        ], [
            'X-Content-Manager-Token' => 'wrong-token',
        ])->assertStatus(403);
    }

    public function test_it_protects_every_endpoint()
    {
        foreach (['create', 'update', 'delete'] as $action) {
            $this->postJson('/cm/blog/' . $action, [])->assertStatus(403);
        }
    }
}
