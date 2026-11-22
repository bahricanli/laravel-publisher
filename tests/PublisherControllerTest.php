<?php

namespace BahriCanli\Publisher\Tests;

use BahriCanli\Publisher\Tests\Fixtures\Page;
use BahriCanli\Publisher\Tests\Fixtures\Post;

class PublisherControllerTest extends TestCase
{
    private function payload(array $overrides = [])
    {
        return array_merge([
            'title'   => 'Merhaba Dünya',
            'content' => '<p>İçerik</p>',
        ], $overrides);
    }

    private function create(array $overrides = [])
    {
        return $this->postJson('/cm/blog/create', $this->payload($overrides), [
            'X-Content-Manager-Token' => 'test-token',
        ]);
    }

    private function update(array $overrides = [])
    {
        return $this->postJson('/cm/blog/update', $overrides, [
            'X-Content-Manager-Token' => 'test-token',
        ]);
    }

    private function destroy(array $overrides = [])
    {
        return $this->postJson('/cm/blog/delete', $overrides, [
            'X-Content-Manager-Token' => 'test-token',
        ]);
    }

    // ---------------------------------------------------------------- create

    public function test_create_stores_a_draft_post_and_returns_its_url()
    {
        $response = $this->create();

        $response->assertStatus(201)
            ->assertJson([
                'page_url' => 'https://example.com/merhaba-dunya',
                'status'   => 'draft',
            ]);

        $post = Post::firstOrFail();

        $this->assertSame('Merhaba Dünya', $post->title);
        $this->assertSame('merhaba-dunya', $post->slug);
        $this->assertSame('<p>İçerik</p>', $post->content);
        $this->assertSame('draft', $post->status);
        $this->assertSame($post->id, $response->json('page_id'));
    }

    public function test_create_persists_every_optional_field()
    {
        $this->create([
            'slug'           => 'ozel-slug',
            'excerpt'        => 'Kısa özet',
            'featured_image' => 'https://example.com/image.jpg',
            'categories'     => ['Seyahat', 'Avrupa'],
            'tags'           => ['estonya', 'tallinn'],
            'status'         => 'published',
        ])->assertStatus(201)->assertJson([
            'page_url' => 'https://example.com/ozel-slug',
            'status'   => 'published',
        ]);

        $post = Post::firstOrFail();

        $this->assertSame('ozel-slug', $post->slug);
        $this->assertSame('Kısa özet', $post->excerpt);
        $this->assertSame('https://example.com/image.jpg', $post->featured_image_url);
        $this->assertSame(['Seyahat', 'Avrupa'], $post->categories);
        $this->assertSame(['estonya', 'tallinn'], $post->tags);
        $this->assertSame('published', $post->status);
    }

    public function test_create_appends_a_suffix_when_the_slug_already_exists()
    {
        Post::create([
            'title'   => 'Merhaba Dünya',
            'slug'    => 'merhaba-dunya',
            'content' => 'x',
        ]);

        $this->create()->assertStatus(201);

        $this->assertSame('merhaba-dunya-1', Post::latest('id')->first()->slug);
    }

    public function test_create_increments_the_suffix_until_the_slug_is_free()
    {
        foreach (['merhaba-dunya', 'merhaba-dunya-1', 'merhaba-dunya-2'] as $slug) {
            Post::create(['title' => 'x', 'slug' => $slug, 'content' => 'x']);
        }

        $this->create()->assertStatus(201);

        $this->assertSame('merhaba-dunya-3', Post::latest('id')->first()->slug);
    }

    public function test_create_falls_back_to_the_app_url_when_site_url_is_not_configured()
    {
        config(['bahricanli-publisher.site_url' => null, 'app.url' => 'https://app.test']);

        $this->create()->assertJsonPath('page_url', 'https://app.test/merhaba-dunya');
    }

    public function test_create_requires_a_title_and_content()
    {
        $this->postJson('/cm/blog/create', [], [
            'X-Content-Manager-Token' => 'test-token',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);

        $this->assertSame(0, Post::count());
    }

    public function test_create_rejects_an_invalid_status()
    {
        $this->create(['status' => 'archived'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_create_rejects_an_invalid_featured_image_url()
    {
        $this->create(['featured_image' => 'not-a-url'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['featured_image']);
    }

    public function test_create_rejects_an_unknown_post_type()
    {
        $this->create(['post_type' => 'product'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['post_type']);
    }

    // ------------------------------------------------------------------ page

    public function test_create_writes_pages_to_the_page_model_and_skips_taxonomies()
    {
        config(['bahricanli-publisher.page_model' => Page::class]);

        $this->create([
            'post_type'  => 'page',
            'slug'       => 'hakkimizda',
            'categories' => ['Yok sayılmalı'],
            'tags'       => ['Yok sayılmalı'],
        ])->assertStatus(201)
            ->assertJsonPath('page_url', 'https://example.com/hakkimizda');

        $this->assertSame(0, Post::count());
        $this->assertSame(1, Page::count());

        $page = Page::firstOrFail();
        $this->assertSame('hakkimizda', $page->slug);
        $this->assertArrayNotHasKey('categories', $page->getAttributes());
        $this->assertArrayNotHasKey('tags', $page->getAttributes());
    }

    public function test_create_falls_back_to_the_post_model_when_no_page_model_is_configured()
    {
        $this->create(['post_type' => 'page'])->assertStatus(201);

        $this->assertSame(1, Post::count());
        $this->assertSame(0, Page::count());
    }

    // ---------------------------------------------------------------- update

    public function test_update_changes_the_post_fields()
    {
        $post = Post::create([
            'title'   => 'Eski',
            'slug'    => 'eski',
            'content' => 'eski içerik',
        ]);

        $this->update([
            'page_id' => $post->id,
            'title'   => 'Yeni Başlık',
            'content' => 'yeni içerik',
        ])->assertOk()->assertJson([
            'page_id' => $post->id,
            'updated' => true,
        ]);

        $post->refresh();
        $this->assertSame('Yeni Başlık', $post->title);
        $this->assertSame('yeni içerik', $post->content);
    }

    public function test_update_maps_featured_image_to_the_model_column()
    {
        $post = Post::create([
            'title'   => 'Eski',
            'slug'    => 'eski',
            'content' => 'içerik',
        ]);

        $this->update([
            'page_id'        => $post->id,
            'featured_image' => 'https://example.com/new.jpg',
        ])->assertOk();

        $this->assertSame('https://example.com/new.jpg', $post->refresh()->featured_image_url);
    }

    public function test_update_does_not_overwrite_fields_that_are_not_sent()
    {
        $post = Post::create([
            'title'   => 'Eski',
            'slug'    => 'eski',
            'content' => 'eski içerik',
        ]);

        $this->update(['page_id' => $post->id, 'title' => 'Yeni'])->assertOk();

        $post->refresh();
        $this->assertSame('Yeni', $post->title);
        $this->assertSame('eski içerik', $post->content);
        $this->assertSame('eski', $post->slug);
    }

    public function test_update_updates_tags()
    {
        $post = Post::create([
            'title'   => 'Eski',
            'slug'    => 'eski',
            'content' => 'içerik',
            'tags'    => ['eski'],
        ]);

        $this->update([
            'page_id' => $post->id,
            'tags'    => ['yeni', 'etiket'],
        ])->assertOk();

        $this->assertSame(['yeni', 'etiket'], $post->refresh()->tags);
    }

    public function test_update_targets_the_page_model_for_pages()
    {
        config(['bahricanli-publisher.page_model' => Page::class]);

        $page = Page::create([
            'title'   => 'Sayfa',
            'slug'    => 'sayfa',
            'content' => 'içerik',
        ]);
        $post = Post::create([
            'title'   => 'Yazı',
            'slug'    => 'yazi',
            'content' => 'içerik',
        ]);

        // Aynı id'ye sahip yazıya dokunulmamalı.
        $page->forceFill(['id' => $post->id])->save();

        $this->update([
            'page_id'   => $post->id,
            'post_type' => 'page',
            'title'     => 'Güncel Sayfa',
        ])->assertOk();

        $this->assertSame('Güncel Sayfa', $page->refresh()->title);
        $this->assertSame('Yazı', $post->refresh()->title);
    }

    public function test_update_returns_404_for_an_unknown_id()
    {
        $this->update(['page_id' => 999, 'title' => 'X'])->assertNotFound();
    }

    // ---------------------------------------------------------------- delete

    public function test_delete_removes_the_post()
    {
        $post = Post::create([
            'title'   => 'Sil',
            'slug'    => 'sil',
            'content' => 'içerik',
        ]);

        $this->destroy(['page_id' => $post->id])
            ->assertOk()
            ->assertJson(['deleted' => true, 'page_id' => $post->id]);

        $this->assertSame(0, Post::count());
    }

    public function test_delete_removes_the_page_when_post_type_is_page()
    {
        config(['bahricanli-publisher.page_model' => Page::class]);

        $page = Page::create([
            'title'   => 'Sayfa',
            'slug'    => 'sayfa',
            'content' => 'içerik',
        ]);

        $this->destroy(['page_id' => $page->id, 'post_type' => 'page'])->assertOk();

        $this->assertSame(0, Page::count());
    }

    public function test_delete_returns_404_for_an_unknown_id()
    {
        $this->destroy(['page_id' => 999])->assertNotFound();
    }
}
