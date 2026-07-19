#!/bin/bash
set -e

cd /var/www/html

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║        Asistio – Setup Container         ║"
echo "╚══════════════════════════════════════════╝"

# 1. Salin .env kalau belum ada
if [ ! -f ".env" ]; then
    echo "[1/7] .env tidak ada → menyalin dari .env.example"
    cp .env.example .env
else
    echo "[1/7] .env sudah ada ✓"
fi

# 2. Install vendor jika belum ada
if [ ! -f "vendor/autoload.php" ]; then
    echo "[2/7] vendor/ belum ada → composer install..."
    composer install --no-interaction --optimize-autoloader
else
    echo "[2/7] vendor/ sudah ada ✓"
fi

# 3. Generate APP_KEY jika kosong
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "[3/7] APP_KEY kosong → generate..."
    php artisan key:generate --force
else
    echo "[3/7] APP_KEY sudah ada ✓"
fi

# 4. Permission storage & bootstrap/cache
echo "[4/7] Set permission storage & cache..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 5. Buat folder fonts untuk dompdf (dipakai PengawasController rekapPdf)
mkdir -p storage/fonts
chmod -R 775 storage/fonts

# 6. Tunggu MySQL siap, lalu migrate + seed
echo "[5/7] Menunggu MySQL (DB_HOST=$DB_HOST)..."
TRIES=0
until php artisan migrate --force --seed 2>&1; do
    TRIES=$((TRIES + 1))
    if [ "$TRIES" -ge 30 ]; then
        echo "✗ MySQL tidak merespon setelah 30x percobaan. Cek container db."
        exit 1
    fi
    echo "  ... DB belum siap, percobaan ke-$TRIES/30 (tunggu 3 detik)"
    sleep 3
done
echo "[6/7] Migrate + seed selesai ✓"

# 7. Buat symlink public/storage → storage/app/public
#    (dipakai Storage::disk('public') di AsistenController untuk foto bukti presensi)
php artisan storage:link 2>/dev/null || true
echo "[7/7] storage:link ✓"

# Bersihkan cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "✅ Asistio siap! Akses: http://localhost:8000"
echo "   Login awal: laboran / (lihat PASSWORD_LABORAN di .env)"
echo ""

exec php-fpm
