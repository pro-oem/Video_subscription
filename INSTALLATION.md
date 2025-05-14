# Kurulum Talimatları

## Cyberpanel Kurulum Adımları

1. Dosyaları Yükleme
   - FTP veya File Manager üzerinden tüm dosyaları ana dizine yükleyin
   - Dosya izinlerini ayarlayın:
     ```bash
     chmod 755 -R /home/username/public_html
     chmod 777 -R /home/username/public_html/uploads
     chmod 777 -R /home/username/public_html/public/images
     ```

2. Veritabanı Oluşturma
   - Cyberpanel'de MySQL Database kısmından yeni veritabanı oluşturun
   - Veritabanı kullanıcısı oluşturun ve tüm izinleri verin

3. Yapılandırma
   - `config/config.php` dosyasını düzenleyin:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'your_database_name');
     define('DB_USER', 'your_database_user');
     define('DB_PASS', 'your_database_password');
     define('BASE_URL', 'https://your-domain.com');
     ```

4. PHP Gereksinimleri
   - PHP 7.4 veya üzeri
   - Gerekli PHP modülleri:
     - PDO
     - PDO_MYSQL
     - GD
     - mbstring
     - curl

5. Install Script Çalıştırma
   - Tarayıcıdan install/init.php'yi çalıştırın:
     ```
     https://your-domain.com/install/init.php
     ```
   - Kurulum tamamlandıktan sonra güvenlik için install klasörünü silin

6. SSL ve Güvenlik
   - Cyberpanel'den SSL sertifikası kurun
   - .htaccess dosyasında HTTPS yönlendirmesi aktif edilmiştir

7. Varsayılan Giriş Bilgileri
   - Admin: admin@example.com / admin123
   - Demo: demo@example.com / demo123
   - İlk girişte bu şifreleri değiştirmeyi unutmayın!
