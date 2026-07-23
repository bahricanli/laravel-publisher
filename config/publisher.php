<?php

return [
    /*
     * Content Manager'ın gönderdiği token ile eşleşmeli.
     * .env dosyasına ekle: CM_PUBLISHER_TOKEN=xxx
     */
    'token' => env('CM_PUBLISHER_TOKEN', ''),

    /*
     * Blog yazılarını temsil eden Eloquent model sınıfı.
     */
    'post_model' => env('CM_PUBLISHER_MODEL', \App\Models\Post::class),

    /*
     * Yazı URL'i için base URL (varsayılan: APP_URL).
     */
    'site_url' => env('CM_PUBLISHER_SITE_URL', null),

    /*
     * API prefix. Endpoint'ler: /{prefix}/blog/create vb.
     */
    'prefix' => 'cm',
];
