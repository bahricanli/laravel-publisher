# BahriCanli Laravel Publisher

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
CM_PUBLISHER_SITE_URL=https://www.example.com
```

`CM_PUBLISHER_TOKEN` değeri content-manager.tr'deki site ayarlarındaki **Plugin Token** alanıyla eşleşmelidir.

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
| `categories` | array (JSON cast) | Kategori listesi |
| `tags` | array (JSON cast) | Etiket listesi |

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
// config/publisher.php
'prefix' => 'api/content',
// Endpoint'ler: /api/content/blog/create, ...
```
