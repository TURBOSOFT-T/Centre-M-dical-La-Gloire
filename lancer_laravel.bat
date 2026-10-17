@echo off
title Lancement Serveur Laravel Local
cd /d "C:\CMLG\Centre-M-dical-La-Gloire"
php artisan serve --host=192.168.1.2 --port=8000
pause