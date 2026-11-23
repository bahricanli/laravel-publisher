<?php

namespace BahriCanli\Publisher;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublisherController
{
    public function capabilities()
    {
        $config = config('bahricanli-publisher');

        return response()->json(['locales' => $this->locales($config)]);
    }

    public function create(Request $request)
    {
        $config = config('bahricanli-publisher');
        $data = $request->validate([
            'locale'        => 'nullable|string|max:16',
            'translation_group' => 'nullable|string|max:64|required_with:locale',
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

        $localization = $this->localizationAttributes($data, $config, new $model);
        $post = $model::create(array_merge($attributes, $localization));

        return response()->json([
            'page_id'  => $post->id,
            'page_url' => $this->postUrl($post, $config),
            'status'   => $post->status,
            'locale'   => $this->postLocale($post, $config),
        ], 201);
    }

    public function update(Request $request)
    {
        $config = config('bahricanli-publisher');

        $data = $request->validate([
            'locale'        => 'nullable|string|max:16',
            'translation_group' => 'nullable|string|max:64|required_with:locale',
            'categories'    => 'nullable|array',
            'title'         => 'nullable|string|max:500',
            'content'       => 'nullable|string',
            'excerpt'       => 'nullable|string',
            'featured_image'=> 'nullable|url',
            'tags'          => 'nullable|array',
            'post_type'     => 'nullable|in:post,page',
        ]);

        $post_type = isset($data['post_type']) ? $data['post_type'] : 'post';
        $model     = $this->resolveModel($post_type, $config);
        $localization = $this->localizationAttributes($data, $config, new $model);
        unset($data['post_type'], $data['locale'], $data['translation_group']);
        if ($post_type === 'page') {
            unset($data['categories']);
        }

        $id   = (int) $request->input('page_id');
        $post = $model::findOrFail($id);

        $filtered = array_filter($data, function ($v) { return $v !== null; });

        // İstek gövdesindeki alan adı ile model kolonu farklı.
        if (array_key_exists('featured_image', $filtered)) {
            $filtered['featured_image_url'] = $filtered['featured_image'];
            unset($filtered['featured_image']);
        }

        $post->update(array_merge($filtered, $localization));

        return response()->json([
            'page_id'  => $post->id,
            'page_url' => $this->postUrl($post, $config),
            'updated'  => true,
            'locale'   => $this->postLocale($post, $config),
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

    private function locales($config)
    {
        return empty($config['locale_column']) ? [] : array_values((array) $config['locales']);
    }

    private function localizationAttributes($data, $config, $model)
    {
        if (empty($data['locale'])) {
            return [];
        }

        if (! in_array($data['locale'], $this->locales($config), true)) {
            abort(422, 'Unsupported content locale.');
        }

        $attributes = [$config['locale_column'] => $data['locale']];
        if (! empty($config['translation_group_column']) && isset($data['translation_group'])) {
            $attributes[$config['translation_group_column']] = $data['translation_group'];
        }

        foreach (array_keys($attributes) as $column) {
            if (! $model->isFillable($column)) {
                abort(422, 'The model does not accept the configured localization field.');
            }
        }

        return $attributes;
    }

    private function postLocale($post, $config)
    {
        return empty($config['locale_column']) ? null : $post->getAttribute($config['locale_column']);
    }

    private function postUrl($post, $config)
    {
        $base = rtrim(isset($config['site_url']) ? $config['site_url'] : config('app.url'), '/');
        $locale = $this->postLocale($post, $config) ?: config('app.locale');
        $base = str_replace('{locale}', rawurlencode($locale), $base);

        return $base . '/' . (isset($post->slug) ? $post->slug : $post->id);
    }
}
