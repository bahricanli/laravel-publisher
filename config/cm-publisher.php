<?php

return [
    /*
     * Content Manager'ın gönderdiği token ile eşleşmeli.
     * .env dosyasına ekle: CM_PUBLISHER_TOKEN=xxx
     */
    'token' => env('CM_PUBLISHER_TOKEN', ''),

    /*
     * Blog yazılarını temsil eden Eloquent model sınıfı.
     * Model; title, slug, content, excerpt, featured_image_url,
     * status, categories (JSON/array cast), tags (JSON/array cast) alanlarını içermeli.
     */
    'post_model' => env('CM_PUBLISHER_MODEL', \App\Models\Post::class),

    /*
     * Yazı URL'i için base URL (varsayılan: APP_URL).
     * Örnek: https://www.example.com
     */
    'site_url' => env('CM_PUBLISHER_SITE_URL', null),

    /*
     * API prefix. Değiştirmek istemiyorsan boş bırak.
     */
    'prefix' => 'cm',
];
