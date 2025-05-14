# CyberPanel Kurulum Kılavuzu

## Gereksinimler
- PHP 8.0 veya üzeri
- MySQL 5.7 veya üzeri
- mod_rewrite modülü aktif

## Kurulum Adımları

1. CyberPanel üzerinde yeni bir website oluşturun:
   - Domain adınızı ekleyin
   - PHP sürümünü 8.0 veya üzeri seçin
   - SSL sertifikası oluşturun

2. Dosya Yükleme:
   - FTP veya File Manager üzerinden tüm proje dosyalarını yükleyin
   - Dosyaları public_html dizinine kopyalayın
   - Dosya izinlerini ayarlayın:
     ```
     chmod 755 -R /home/username/public_html
     chmod 644 -R /home/username/public_html/*.php
     chmod 755 -R /home/username/public_html/uploads
     ```

3. Veritabanı Kurulumu:
   - CyberPanel'den yeni bir MySQL veritabanı oluşturun
   - config/config.php dosyasını düzenleyin:
     - DB_HOST
     - DB_NAME
     - DB_USER
     - DB_PASS

4. .htaccess Ayarları:
   - mod_rewrite modülünün aktif olduğundan emin olun
   - .htaccess dosyasının doğru çalıştığını kontrol edin

5. SSL ve HTTPS Ayarları:
   - Let's Encrypt SSL sertifikası kurun
   - HTTPS yönlendirmesini kontrol edin

## Sorun Giderme
- Hata günlükleri için error.log dosyasını kontrol edin
- PHP sürümünü phpinfo() ile kontrol edin
- Dosya izinlerini kontrol edin
- mod_rewrite modülünün aktif olduğunu doğrulayın
