@echo off
title Agro Monitor - Dev Launcher

echo ================================
echo   Запуск Agro Monitor (DEV)
echo ================================

REM Переходим в папку проекта
cd /d C:\Users\C1l3NCE\Downloads\Projects\agro-monitor

REM Запуск Laravel backend
start "Laravel Server" cmd /k "php artisan serve"

REM Небольшая пауза
timeout /t 2 >nul

REM Запуск Vite / npm
start "Vite Dev Server" cmd /k "npm run dev"

echo ================================
echo   Проект запущен
echo ================================
