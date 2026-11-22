# Changelog

Bu projedeki önemli değişiklikler bu dosyada listelenir.
Biçim [Keep a Changelog](https://keepachangelog.com/tr/1.1.0/) temellidir ve
proje [Semantic Versioning](https://semver.org/lang/tr/) kullanır.

## [Unreleased]

## [1.1.2] - 2026-11-22

### Added

- PHPUnit + Orchestra Testbench test paketi (29 test): middleware token
  doğrulaması, create/update/delete akışları, slug çakışması, `post_type`
  yönlendirmesi, doğrulama hataları ve route prefix'i.
- `phpunit.xml`, `tests/` ve `composer test` script'i.
- CI artık testleri PHP 8.2/8.3/8.4 üzerinde çalıştırıyor.

### Fixed

- `update` endpoint'i `featured_image` alanını `featured_image_url` kolonuna
  yazmıyordu; görsel güncellemesi sessizce kayboluyordu.
- `vendor:publish` config dosyasını `config/publisher.php` olarak yayınlıyordu,
  paket ise `bahricanli-publisher` anahtarını okuyordu. Yayınlanan dosya
  `config/bahricanli-publisher.php` oldu; prefix gibi ayarlar artık etkili.

## [1.1.1] - 2026-11-22

### Changed

- Lisans MIT'ten GPL-3.0-or-later'a taşındı (`composer.json`, `LICENSE`).
- README rozet satırı, sponsor bağlantısı ve lisans bölümü eklendi.
- `README.md` İngilizceye çevrildi; Türkçe sürüm `README.tr.md` olarak ayrıldı.
- GitHub Actions: lint, OpenSSF Scorecard ve CodeQL workflow'ları eklendi.
- Dependabot yapılandırması eklendi.
- `CONTRIBUTING.md` ve `SECURITY.md` eklendi.

## [1.1.0] - 2026-09-09

### Added

- `post_type` (`post` | `page`) desteği; create/update/delete endpoint'leri.
- `page_model` config'i. Tanımlı değilse sayfa istekleri `post_model`'e düşer.
- Sayfa isteklerinde `categories`/`tags` yazılmıyor.

## [1.0.4]

### Added

- Update endpoint'inde `tags` desteği.

## [1.0.3]

### Added

- Laravel 13 desteği.

## [1.0.2]

### Fixed

- Laravel 5.x string route sözdizimi, 6+ array sözdizimi.

## [1.0.1]

### Added

- PHP 5.6+ ve Laravel 5.3+ uyumluluğu.

## [1.0.0]

### Added

- İlk sürüm: content-manager.tr için create / update / delete endpoint'leri.

[Unreleased]: https://github.com/bahricanli/laravel-publisher/compare/v1.1.2...HEAD
[1.1.2]: https://github.com/bahricanli/laravel-publisher/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/bahricanli/laravel-publisher/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/bahricanli/laravel-publisher/compare/v1.0.4...v1.1.0
[1.0.4]: https://github.com/bahricanli/laravel-publisher/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/bahricanli/laravel-publisher/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/bahricanli/laravel-publisher/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/bahricanli/laravel-publisher/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/bahricanli/laravel-publisher/releases/tag/v1.0.0
