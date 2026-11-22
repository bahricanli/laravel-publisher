# Katkı Rehberi

Katkılar memnuniyetle karşılanır. Küçük ve tek konulu pull request'ler tercih
edilir.

## Akış

1. Depoyu forklayın ve `main` dalından yeni bir dal açın.
2. Gerekiyorsa değişikliğinizi test edin:

   ```bash
   composer install
   composer test
   find src routes config tests -name '*.php' -print0 | xargs -0 -n1 php -l
   composer validate --no-check-publish
   ```

   Yeni davranış ekliyorsanız `tests/` altına test ekleyin.

3. Commit mesajlarında [Conventional Commits](https://www.conventionalcommits.org/)
   biçimini kullanın (`feat:`, `fix:`, `chore:` ...).
4. `main` dalına pull request açın. PR'lar squash ile birleştirilir.
5. Sürüm çıkarmak için `git tag -a vX.Y.Z -m "..."` oluşturup `git push origin vX.Y.Z`
   çalıştırın. `Release` workflow'u GitHub Release'i otomatik oluşturur.

## Lisans

Bu projeye gönderilen tüm katkılar
[GNU General Public License v3.0 veya sonrası](LICENSE) (GPL-3.0-or-later)
altında yayınlanır.
