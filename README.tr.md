# BahriCanli Laravel Publisher

<p align="center">
  <a href="https://github.com/bahricanli/laravel-publisher/actions/workflows/tests.yml"><img src="https://github.com/bahricanli/laravel-publisher/actions/workflows/tests.yml/badge.svg" alt="tests"></a>
  <a href="https://scorecard.dev/viewer/?uri=github.com/bahricanli/laravel-publisher"><img src="https://api.scorecard.dev/projects/github.com/bahricanli/laravel-publisher/badge" alt="OpenSSF Scorecard"></a>
  <a href="https://github.com/bahricanli/laravel-publisher/releases/latest"><img src="https://img.shields.io/github/v/release/bahricanli/laravel-publisher" alt="Latest release"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-5.6%2B-777BB4?logo=php&logoColor=white" alt="PHP 5.6+"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-5.3%2B-FF2D20?logo=laravel&logoColor=white" alt="Laravel 5.3+"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-GPL--3.0-blue" alt="GPL-3.0"></a>
  <a href="https://github.com/sponsors/bmericc"><img src="https://img.shields.io/badge/sponsor-%E2%9D%A4-ea4aaa?logo=githubsponsors" alt="Sponsor"></a>
</p>

<p align="center">
  <a href="README.md">English</a> · <strong>Türkçe</strong>
</p>

Laravel uygulamanızı [content-manager.tr](https://content-manager.tr) ile bağlayan pakettir. Token tabanlı API üzerinden yazı oluşturma, güncelleme ve silme işlemlerini destekler.

WordPress eklentisi [bahricanli-publisher](https://github.com/bahricanli/bahricanli-publisher) ile aynı API sözleşmesini uygular.

---

## Kurulum

```bash
composer require bahricanli/laravel-publisher
```

Config dosyasını yayınla:

```bash
php artisan vendor:publish --tag=bahricanli-publisher
```

---

## Yapılandırma

`.env` dosyasına ekle:

```env
CM_PUBLISHER_TOKEN=guclu-rastgele-token
CM_PUBLISHER_MODEL=App\Models\Post
CM_PUBLISHER_PAGE_MODEL=App\Models\Page
CM_PUBLISHER_SITE_URL=https://www.example.com
```

`CM_PUBLISHER_TOKEN` değeri content-manager.tr'deki site ayarlarındaki **Plugin Token** alanıyla eşleşmelidir.

`CM_PUBLISHER_PAGE_MODEL` opsiyoneldir. Tanımlıysa `post_type=page` gönderilen istekler bu modele yazılır; tanımlı değilse sayfa istekleri de `CM_PUBLISHER_MODEL`'e düşer. Sayfalarda `categories`/`tags` yazılmaz.

---

## Model Gereksinimleri

Eloquent modelinizin şu alanları içermesi gerekir:

| Alan | Tür | Açıklama |
|---|---|---|
| `title` | string | Yazı başlığı |
| `slug` | string | URL slug'ı |
| `content` | text | HTML içerik |
| `excerpt` | text\|null | Kısa özet |
| `featured_image_url` | string\|null | Öne çıkan görsel URL |
| `status` | string | `draft` veya `published` |
| `categories` | array (JSON cast) | Kategori listesi (yalnızca yazı) |
| `tags` | array (JSON cast) | Etiket listesi (yalnızca yazı) |

İstek gövdesinde opsiyonel `post_type` alanı gönderilebilir (`post` varsayılan, veya `page`). `page` için `page_model` config'i kullanılır. `update`/`delete` isteklerinde de aynı `post_type` gönderilmelidir ki doğru model üzerinde `page_id` çözülsün.

---

## Endpoint'ler

Tüm isteklerde `X-Content-Manager-Token` header'ı veya `_cm_token` POST parametresi gönderilmelidir.

| Method | URL | Açıklama |
|---|---|---|
| POST | `/cm/blog/create` | Yeni yazı oluştur |
| POST | `/cm/blog/update` | Yazıyı güncelle (`page_id` parametresi ile) |
| POST | `/cm/blog/delete` | Yazıyı sil (`page_id` parametresi ile) |

### Create — İstek Gövdesi

```json
{
    "title": "Yazı Başlığı",
    "content": "<p>HTML içerik...</p>",
    "slug": "yazi-basligi",
    "excerpt": "Kısa özet.",
    "featured_image": "https://example.com/image.jpg",
    "categories": ["Seyahat", "Avrupa"],
    "tags": ["estonya", "tallinn"],
    "status": "draft"
}
```

### Create — Yanıt

```json
{
    "page_id": 42,
    "page_url": "https://www.example.com/yazi-basligi",
    "status": "draft"
}
```

### Update — İstek Gövdesi

```json
{
    "page_id": 42,
    "title": "Güncellenmiş Başlık",
    "content": "<p>Güncellenmiş içerik.</p>"
}
```

### Delete — İstek Gövdesi

```json
{
    "page_id": 42
}
```

---

## content-manager.tr Ayarları

Site eklerken:
- **Site Türü:** Laravel (Custom API)
- **API URL:** `https://www.example.com` (sitenizin kök URL'i)
- **Plugin Token:** `.env`'deki `CM_PUBLISHER_TOKEN` değeri

---

## Prefix Değiştirme

```php
// config/bahricanli-publisher.php
'prefix' => 'api/content',
// Endpoint'ler: /api/content/blog/create, ...
```

---

## Testler

```bash
composer install
composer test
```

Test paketi [Orchestra Testbench](https://packages.tools/testbench) kullanır ve PHP 8.2+ üzerinde çalışır. Veritabanı gerektirmez; SQLite in-memory kullanılır.

---

## Lisans

[GNU General Public License v3.0 veya sonrası](LICENSE) (GPL-3.0-or-later) ile lisanslanmıştır.
