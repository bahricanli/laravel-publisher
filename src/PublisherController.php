<?php

namespace BahriCanli\Publisher;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PublisherController
{
    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'         => 'required|string|max:500',
            'content'       => 'required|string',
            'slug'          => 'nullable|string|max:500',
            'excerpt'       => 'nullable|string',
            'featured_image'=> 'nullable|url',
            'categories'    => 'nullable|array',
            'tags'          => 'nullable|array',
            'status'        => 'nullable|in:draft,published',
        ]);

        $config = config('bahricanli-publisher');
        $model  = $config['post_model'];

        $slug = $data['slug'] ?? Str::slug($data['title']);
        $base  = $slug;
        $count = 1;
        while ($model::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $count++;
        }

        $post = $model::create([
            'title'              => $data['title'],
            'slug'               => $slug,
            'content'            => $data['content'],
            'excerpt'            => $data['excerpt'] ?? null,
            'featured_image_url' => $data['featured_image'] ?? null,
            'status'             => $data['status'] ?? 'draft',
            'categories'         => $data['categories'] ?? [],
            'tags'               => $data['tags'] ?? [],
        ]);

        return response()->json([
            'page_id'  => $post->id,
            'page_url' => $this->postUrl($post, $config),
            'status'   => $post->status,
        ], 201);
    }

    public function update(Request $request): JsonResponse
    {
        $config = config('bahricanli-publisher');
        $model  = $config['post_model'];

        $id   = (int) $request->input('page_id');
        $post = $model::findOrFail($id);

        $data = $request->validate([
            'title'         => 'nullable|string|max:500',
            'content'       => 'nullable|string',
            'excerpt'       => 'nullable|string',
            'featured_image'=> 'nullable|url',
        ]);

        $post->update(array_filter($data, fn($v) => $v !== null));

        return response()->json([
            'page_id'  => $post->id,
            'page_url' => $this->postUrl($post, $config),
            'updated'  => true,
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        $config = config('bahricanli-publisher');
        $model  = $config['post_model'];

        $id   = (int) $request->input('page_id');
        $post = $model::findOrFail($id);
        $post->delete();

        return response()->json(['deleted' => true, 'page_id' => $id]);
    }

    private function postUrl(object $post, array $config): ?string
    {
        $base = rtrim($config['site_url'] ?? config('app.url'), '/');
        return $base . '/' . ($post->slug ?? $post->id);
    }
}
