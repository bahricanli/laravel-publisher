# Katkı Rehberi

Katkılar memnuniyetle karşılanır. Küçük ve tek konulu pull request'ler tercih
edilir.

## Akış

1. Depoyu forklayın ve `main` dalından yeni bir dal açın.
2. Gerekiyorsa değişikliğinizi test edin; en azından PHP sözdizimini kontrol edin:

   ```bash
   find src routes config -name '*.php' -print0 | xargs -0 -n1 php -l
   composer validate --no-check-publish
   ```

3. Commit mesajlarında [Conventional Commits](https://www.conventionalcommits.org/)
   biçimini kullanın (`feat:`, `fix:`, `chore:` ...).
4. `main` dalına pull request açın. PR'lar squash ile birleştirilir.

## Lisans

Bu projeye gönderilen tüm katkılar
[GNU General Public License v3.0 veya sonrası](LICENSE) (GPL-3.0-or-later)
altında yayınlanır.
