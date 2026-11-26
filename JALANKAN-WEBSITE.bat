@echo off
title PORTAL RESMI BWS KALIMANTAN I - KEMENTERIAN PEKERJAAN UMUM
color 1F

echo ==============================================================================
echo       PORTAL SURAT UNDANGAN ^& SUKET LUPA ABSEN
echo       BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK
echo       KEMENTERIAN PEKERJAAN UMUM RI
echo ==============================================================================
echo.
echo Sedang menyiapkan server dan database lokal...

cd /d "%~dp0"

where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\xampp\php\php.exe" (
        set "PATH=C:\xampp\php;%PATH%"
    ) else (
        echo [ERROR] PHP tidak ditemukan di sistem atau XAMPP.
        pause
        exit /b 1
    )
)

echo [OK] PHP terdeteksi.
echo Membuka Portal di Browser...
start "" "http://127.0.0.1:8000"

echo.
echo ==============================================================================
echo SERVER AKTIF PADA: http://127.0.0.1:8000
echo JANGAN TUTUP JENDELA INI SELAMA ANDA MENGGUNAKAN WEBSITE!
echo ==============================================================================
echo.

php artisan serve --port=8000
pause
