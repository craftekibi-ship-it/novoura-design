# Novoura Design — Canlıya Alma (Hostinger VPS + CyberPanel)

Hedef: `https://design.novouracreative.com` · Stack: Laravel 13 + MySQL + PHP 8.2+

## 1. CyberPanel'de site + veritabanı
1. **Website oluştur**: `design.novouracreative.com` (PHP 8.2 veya üstü seç).
2. **Database oluştur**: ad `novoura_design`, kullanıcı `novoura_design`, güçlü şifre. (.env'e yaz.)
3. **SSL**: CyberPanel → SSL → Issue (Let's Encrypt).

## 2. Kodu sunucuya al
```bash
cd /home/design.novouracreative.com/public_html
# Boşsa içini temizle, sonra:
git clone <repo-url> .        # ya da dosyaları yükle (vendor & node_modules HARİÇ)
composer install --no-dev --optimize-autoloader
```
> Frontend build YOK — Fabric.js + fontlar CDN'den geliyor. `npm` gerekmez.

## 3. Ortam (.env)
```bash
cp .env.production.example .env
nano .env          # DB şifresi + YENİ Anthropic anahtarını gir
php artisan key:generate
```

## 4. Veritabanı + depolama
```bash
php artisan migrate --force
php artisan db:seed --force        # admin kullanıcı + Esto markası/menüsü + 6 marka
php artisan storage:link
```
> İlk seed Esto'yu ve 6 boş markayı kurar. Yeni markaları panelden/onboarding ile eklersin.

## 5. İzinler + cache
```bash
chown -R nobody:nobody storage bootstrap/cache    # CyberPanel kullanıcısına göre
chmod -R 775 storage bootstrap/cache
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 6. Document root
CyberPanel vHost'ta document root **`public/`** olmalı (Laravel public klasörü).

## 7. Giriş + güvenlik
- İlk giriş: `novoura@design.local` / `novoura` → **hemen değiştir** (tinker ile yeni şifre).
- `APP_DEBUG=false` olduğundan emin ol.
- Şifre değiştirme:
```bash
php artisan tinker --execute='App\Models\User::where("email","novoura@design.local")->update(["password"=>bcrypt("YENİ_GÜÇLÜ_ŞİFRE")]);'
```

## Güncelleme (sonraki deploy'lar)
```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Notlar
- Anthropic anahtarı yalnızca sunucu `.env`'inde; tarayıcıya düşmez (tüm AI çağrıları backend proxy).
- Yeni marka onboarding kod gerektirmez: şablonu (`StudioController::brandTemplate`) + JS frame renderer + katalog seed eklenir.
