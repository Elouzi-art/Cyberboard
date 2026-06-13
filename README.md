# CyberBoard 🛡️

> Cyber Mission Control — Personal project manager for IT Security students

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat-square&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-9.6-4479A1?style=flat-square&logo=mysql&logoColor=white)

A dark-themed personal project management platform built for IT Security engineering students
juggling certifications, CTF challenges, coding projects, and web development simultaneously.

## ✨ Features

| Feature      | Description                                                               |
| ------------ | ------------------------------------------------------------------------- |
| 🎯 Dashboard | Mission Control — métriques temps réel, projets épinglés, tâches urgentes |
| 📅 Roadmap   | Timeline Gantt dark — barres colorées par catégorie, zoom 30/60/90/120j   |
| 📋 Kanban    | Drag & drop entre colonnes (SortableJS) — Todo / In Progress / Done       |
| 🍅 Pomodoro  | Timer SVG animé + son + auto-switch + streak tracker                      |
| 📊 Stats     | Chart.js — doughnut catégories, bar semaines, calendrier activité         |
| ✅ Tasks     | Filtres overdue / today / week / priorité / projet                        |
| 🔔 Reminders | Rappels avec date/heure, priorité, lien projet                            |
| 📤 Export    | Planning en CSV + PDF (print-ready dark theme)                            |

## 🏗️ Stack

- **Backend** : Laravel 13 · PHP 8.5
- **Frontend** : Blade · TailwindCSS · Chart.js · SortableJS
- **Database** : MySQL 9.6
- **Auth** : Laravel Breeze (multi-users)

## 🚀 Installation

```bash
git clone https://github.com/TON_USERNAME/cyberboard.git
cd cyberboard
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
```

Configurer `.env` :

```env
DB_DATABASE=cyberboard
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## 🔑 Demo account

```
Email    : solid@cyberboard.dev
Password : password
```

## 🗺️ Routes

| URL           | Description        |
| ------------- | ------------------ |
| `/dashboard`  | Mission Control    |
| `/projects`   | CRUD projets       |
| `/kanban`     | Kanban drag & drop |
| `/roadmap`    | Timeline Gantt     |
| `/tasks`      | Toutes les tâches  |
| `/reminders`  | Rappels            |
| `/pomodoro`   | Focus timer        |
| `/stats`      | Statistiques       |
| `/export/csv` | Export CSV         |
| `/export/pdf` | Export PDF         |

## 👤 Author

**Solid** — IT Security Engineering Student ·

- 🏅 ISC² CC Certified
- 🚩 CTF : PicoCTF
- 🔐 Loi 05-20 · Loi 08-09 · DNSSI

## 📄 License

MIT
