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
     * Sayfaları (post_type=page) temsil eden Eloquent model sınıfı.
     * Tanımlı değilse sayfa istekleri de post_model'e yazılır.
     */
    'page_model' => env('CM_PUBLISHER_PAGE_MODEL', null),

    /*
     * Yazı URL'i için base URL (varsayılan: APP_URL).
     */
    'site_url' => env('CM_PUBLISHER_SITE_URL', null),

    /*
     * API prefix. Endpoint'ler: /{prefix}/blog/create vb.
     */
    'prefix' => 'cm',

    // Enable multilingual publishing by mapping content to a model column
    // and listing the locale codes accepted by the destination application.
    'locale_column' => env('CM_PUBLISHER_LOCALE_COLUMN', null),
    'locales' => array_values(array_filter(array_map('trim', explode(',', env('CM_PUBLISHER_LOCALES', ''))))),
    'translation_group_column' => env('CM_PUBLISHER_TRANSLATION_GROUP_COLUMN', null),
];
