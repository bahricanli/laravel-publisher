<?php

namespace BahriCanli\Publisher;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublisherController
{
    public function create(Request $request)
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
            'post_type'     => 'nullable|in:post,page',
        ]);

        $config    = config('bahricanli-publisher');
        $post_type = isset($data['post_type']) ? $data['post_type'] : 'post';
        $model     = $this->resolveModel($post_type, $config);

        $slug  = isset($data['slug']) ? $data['slug'] : Str::slug($data['title']);
        $base  = $slug;
        $count = 1;
        while ($model::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $count++;
        }

        $attributes = [
            'title'              => $data['title'],
            'slug'               => $slug,
            'content'            => $data['content'],
            'excerpt'            => isset($data['excerpt']) ? $data['excerpt'] : null,
            'featured_image_url' => isset($data['featured_image']) ? $data['featured_image'] : null,
            'status'             => isset($data['status']) ? $data['status'] : 'draft',
        ];

        // Sayfalarda kategori/etiket yok; sadece blog yazılarına ekle.
        if ($post_type !== 'page') {
            $attributes['categories'] = isset($data['categories']) ? $data['categories'] : [];
            $attributes['tags']       = isset($data['tags']) ? $data['tags'] : [];
        }

        $post = $model::create($attributes);

        return response()->json([
            'page_id'  => $post->id,
            'page_url' => $this->postUrl($post, $config),
            'status'   => $post->status,
        ], 201);
    }

    public function update(Request $request)
    {
        $config = config('bahricanli-publisher');

        $data = $request->validate([
            'title'         => 'nullable|string|max:500',
            'content'       => 'nullable|string',
            'excerpt'       => 'nullable|string',
            'featured_image'=> 'nullable|url',
            'tags'          => 'nullable|array',
            'post_type'     => 'nullable|in:post,page',
        ]);

        $post_type = isset($data['post_type']) ? $data['post_type'] : 'post';
        $model     = $this->resolveModel($post_type, $config);
        unset($data['post_type']);

        $id   = (int) $request->input('page_id');
        $post = $model::findOrFail($id);

        $filtered = array_filter($data, function ($v) { return $v !== null; });

        // İstek gövdesindeki alan adı ile model kolonu farklı.
        if (array_key_exists('featured_image', $filtered)) {
            $filtered['featured_image_url'] = $filtered['featured_image'];
            unset($filtered['featured_image']);
        }

        $post->update($filtered);

        return response()->json([
            'page_id'  => $post->id,
            'page_url' => $this->postUrl($post, $config),
            'updated'  => true,
        ]);
    }

    public function delete(Request $request)
    {
        $config = config('bahricanli-publisher');

        $post_type = $request->input('post_type', 'post');
        $model     = $this->resolveModel($post_type, $config);

        $id   = (int) $request->input('page_id');
        $post = $model::findOrFail($id);
        $post->delete();

        return response()->json(['deleted' => true, 'page_id' => $id]);
    }

    /**
     * post_type'a göre hedef Eloquent model sınıfını seçer.
     * post_type=page ve config'de page_model tanımlıysa onu, aksi halde post_model'i döndürür.
     */
    private function resolveModel($post_type, $config)
    {
        if ($post_type === 'page' && ! empty($config['page_model'])) {
            return $config['page_model'];
        }

        return $config['post_model'];
    }

    private function postUrl($post, $config)
    {
        $base = rtrim(isset($config['site_url']) ? $config['site_url'] : config('app.url'), '/');
        return $base . '/' . (isset($post->slug) ? $post->slug : $post->id);
    }
}
