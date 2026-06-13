# CyberBoard — Cyber Mission Control

> Outil de gestion de projets perso pour étudiant ingénieur cyber.
> Laravel 11 · TailwindCSS Dark · MySQL

## Installation rapide

```bash
# Dans le dossier parent
bash setup.sh

# Dans cyberboard/
bash setup_part2.sh

# Créer la DB : cyberboard
# Vérifier .env DB_USERNAME et DB_PASSWORD

php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

## Compte de démo
- Email    : hagi@cyberboard.dev
- Password : password

## Fonctionnalités
- Dashboard mission control
- Projets (CRUD, catégories, priorités, progression, tags)
- Roadmap timeline (Gantt dark)
- Kanban drag & drop (SortableJS)
- Tâches avec filtres (overdue, today, week)
- Rappels avec notifications
- Timer Pomodoro (SVG animé, son, streak)
- Statistiques (Chart.js — doughnut, bar, line)
- Streak tracker (calendrier activité)
- Export CSV du planning
- Export PDF (print)

## Routes principales
| URL          | Description          |
|--------------|----------------------|
| /dashboard   | Mission Control      |
| /projects    | Liste des projets    |
| /kanban      | Kanban drag & drop   |
| /roadmap     | Timeline Gantt       |
| /tasks       | Toutes les tâches    |
| /reminders   | Rappels              |
| /pomodoro    | Timer focus          |
| /stats       | Statistiques         |
| /export/csv  | Export CSV           |
| /export/pdf  | Export PDF (print)   |
