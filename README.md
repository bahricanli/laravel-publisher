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
  <strong>English</strong> · <a href="README.tr.md">Türkçe</a>
</p>

Connects your Laravel application to [content-manager.tr](https://content-manager.tr). It supports creating, updating and deleting posts through a token-based API.

It implements the same API contract as the WordPress plugin [bahricanli-publisher](https://github.com/bahricanli/bahricanli-publisher).

---

## Installation

```bash
composer require bahricanli/laravel-publisher
```

Publish the config file:

```bash
php artisan vendor:publish --tag=bahricanli-publisher
```

---

## Configuration

Add the following to your `.env` file:

```env
CM_PUBLISHER_TOKEN=strong-random-token
CM_PUBLISHER_MODEL=App\Models\Post
CM_PUBLISHER_PAGE_MODEL=App\Models\Page
CM_PUBLISHER_SITE_URL=https://www.example.com
```

`CM_PUBLISHER_TOKEN` must match the **Plugin Token** field in the site settings on content-manager.tr.

`CM_PUBLISHER_PAGE_MODEL` is optional. When set, requests with `post_type=page` are written to this model; otherwise page requests fall back to `CM_PUBLISHER_MODEL`. `categories`/`tags` are never written for pages.

---

## Model Requirements

Your Eloquent model must contain the following fields:

| Field | Type | Description |
|---|---|---|
| `title` | string | Post title |
| `slug` | string | URL slug |
| `content` | text | HTML content |
| `excerpt` | text\|null | Short summary |
| `featured_image_url` | string\|null | Featured image URL |
| `status` | string | `draft` or `published` |
| `categories` | array (JSON cast) | Category list (posts only) |
| `tags` | array (JSON cast) | Tag list (posts only) |

The request body may include an optional `post_type` field (`post` by default, or `page`). `page_model` config is used for `page`. The same `post_type` must be sent with `update`/`delete` requests so that `page_id` is resolved against the correct model.

---

## Endpoints

Every request must send either the `X-Content-Manager-Token` header or the `_cm_token` POST parameter.

| Method | URL | Description |
|---|---|---|
| POST | `/cm/blog/create` | Create a new post |
| POST | `/cm/blog/update` | Update a post (with `page_id`) |
| POST | `/cm/blog/delete` | Delete a post (with `page_id`) |

### Create — Request Body

```json
{
    "title": "Post Title",
    "content": "<p>HTML content...</p>",
    "slug": "post-title",
    "excerpt": "Short summary.",
    "featured_image": "https://example.com/image.jpg",
    "categories": ["Travel", "Europe"],
    "tags": ["estonia", "tallinn"],
    "status": "draft"
}
```

### Create — Response

```json
{
    "page_id": 42,
    "page_url": "https://www.example.com/post-title",
    "status": "draft"
}
```

### Update — Request Body

```json
{
    "page_id": 42,
    "title": "Updated Title",
    "content": "<p>Updated content.</p>"
}
```

### Delete — Request Body

```json
{
    "page_id": 42
}
```

---

## content-manager.tr Settings

When adding a site:
- **Site Type:** Laravel (Custom API)
- **API URL:** `https://www.example.com` (the root URL of your site)
- **Plugin Token:** the `CM_PUBLISHER_TOKEN` value from `.env`

---

## Changing the Prefix

```php
// config/publisher.php
'prefix' => 'api/content',
// Endpoints: /api/content/blog/create, ...
```

---

## License

Licensed under the [GNU General Public License v3.0 or later](LICENSE) (GPL-3.0-or-later).
