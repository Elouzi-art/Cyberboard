<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Reminder;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'     => 'Hagi',
            'email'    => 'hagi@cyberboard.dev',
            'password' => Hash::make('password'),
            'bio'      => 'Étudiant ingénieur IT Security — FST Marrakech',
        ]);

        // Tags
        $tags = [
            ['name' => 'ISC²',       'color' => '#7f77dd'],
            ['name' => 'Cisco',      'color' => '#1d9e75'],
            ['name' => 'PicoCTF',    'color' => '#22c55e'],
            ['name' => 'HTB',        'color' => '#ef9f27'],
            ['name' => 'Laravel',    'color' => '#e24b4a'],
            ['name' => 'Algorithms', 'color' => '#5dcaa5'],
        ];

        $tagIds = [];
        foreach ($tags as $t) {
            $tagIds[$t['name']] = Tag::create(array_merge($t, ['user_id' => $user->id]))->id;
        }

        // Projets
        $projects = [
            [
                'title'       => 'ISC² CC Certification',
                'category'    => 'cert',
                'priority'    => 'critical',
                'status'      => 'active',
                'progress'    => 35,
                'start_date'  => '2026-06-01',
                'end_date'    => '2026-08-15',
                'description' => 'Révision complète du programme ISC² CC + passage de l\'examen.',
                'color'       => '#7f77dd',
                'is_pinned'   => true,
                'tags'        => ['ISC²'],
            ],
            [
                'title'       => 'PicoCTF Summer 2026',
                'category'    => 'ctf',
                'priority'    => 'high',
                'status'      => 'active',
                'progress'    => 20,
                'start_date'  => '2026-06-10',
                'end_date'    => '2026-07-31',
                'description' => 'Résoudre 20+ challenges — focus PWN, Reverse, Stegano.',
                'color'       => '#22c55e',
                'is_pinned'   => true,
                'tags'        => ['PicoCTF'],
            ],
            [
                'title'       => 'Algorithmes en C/C++',
                'category'    => 'code',
                'priority'    => 'high',
                'status'      => 'active',
                'progress'    => 50,
                'start_date'  => '2026-06-15',
                'end_date'    => '2026-08-30',
                'description' => 'Réimplémenter sorts, trees, graphs, DP en C et C++.',
                'color'       => '#ef9f27',
                'is_pinned'   => false,
                'tags'        => ['Algorithms'],
            ],
            [
                'title'       => 'CyberBoard Laravel',
                'category'    => 'web',
                'priority'    => 'critical',
                'status'      => 'active',
                'progress'    => 15,
                'start_date'  => '2026-06-12',
                'end_date'    => '2026-09-01',
                'description' => 'Développer CyberBoard — plateforme de gestion de projets perso.',
                'color'       => '#e24b4a',
                'is_pinned'   => true,
                'tags'        => ['Laravel'],
            ],
            [
                'title'       => 'Hack The Box — Starting Point',
                'category'    => 'ctf',
                'priority'    => 'medium',
                'status'      => 'active',
                'progress'    => 60,
                'start_date'  => '2026-06-01',
                'end_date'    => '2026-07-20',
                'description' => 'Terminer le parcours débutant HTB.',
                'color'       => '#ef9f27',
                'is_pinned'   => false,
                'tags'        => ['HTB'],
            ],
            [
                'title'       => 'Cisco NetAcad CCNA',
                'category'    => 'cert',
                'priority'    => 'high',
                'status'      => 'active',
                'progress'    => 5,
                'start_date'  => '2026-06-20',
                'end_date'    => '2026-09-15',
                'description' => 'Modules réseau avancés — préparation CCNA.',
                'color'       => '#7f77dd',
                'is_pinned'   => false,
                'tags'        => ['Cisco'],
            ],
            [
                'title'       => 'Python pour la cyber',
                'category'    => 'code',
                'priority'    => 'medium',
                'status'      => 'active',
                'progress'    => 10,
                'start_date'  => '2026-07-01',
                'end_date'    => '2026-08-10',
                'description' => 'Scripts réseau, scapy, crypto, automatisation.',
                'color'       => '#5dcaa5',
                'is_pinned'   => false,
                'tags'        => [],
            ],
            [
                'title'       => 'Java OOP + Design Patterns',
                'category'    => 'code',
                'priority'    => 'medium',
                'status'      => 'paused',
                'progress'    => 0,
                'start_date'  => '2026-07-15',
                'end_date'    => '2026-08-31',
                'description' => 'Patterns classiques en Java — Singleton, Factory, Observer.',
                'color'       => '#ef9f27',
                'is_pinned'   => false,
                'tags'        => [],
            ],
        ];

        foreach ($projects as $data) {
            $tagNames = $data['tags'];
            unset($data['tags']);
            $project = $user->projects()->create(array_merge($data, ['user_id' => $user->id]));
            $ids = array_map(fn($n) => $tagIds[$n] ?? null, $tagNames);
            $project->tags()->sync(array_filter($ids));
        }

        // Tâches
        $projectMap = $user->projects()->pluck('id', 'title');

        $tasks = [
            ['title' => 'Finir module réseau Cisco',              'project' => 'Cisco NetAcad CCNA',          'priority' => 'high',     'due_date' => '2026-06-20', 'status' => 'todo'],
            ['title' => 'Résoudre 3 challenges PicoCTF PWN',      'project' => 'PicoCTF Summer 2026',          'priority' => 'critical', 'due_date' => '2026-06-15', 'status' => 'todo'],
            ['title' => 'Implémenter linked list en C',            'project' => 'Algorithmes en C/C++',         'priority' => 'high',     'due_date' => '2026-06-18', 'status' => 'done'],
            ['title' => 'Setup Laravel + Breeze auth',             'project' => 'CyberBoard Laravel',           'priority' => 'critical', 'due_date' => '2026-06-14', 'status' => 'done'],
            ['title' => 'Lire chapitre 1 ISC² CC',                'project' => 'ISC² CC Certification',        'priority' => 'medium',   'due_date' => '2026-06-13', 'status' => 'done'],
            ['title' => 'HTB — Machine Jerry',                     'project' => 'Hack The Box — Starting Point','priority' => 'high',     'due_date' => '2026-06-17', 'status' => 'todo'],
            ['title' => 'Écrire script scapy ping sweep',          'project' => 'Python pour la cyber',         'priority' => 'medium',   'due_date' => '2026-07-05', 'status' => 'todo'],
            ['title' => 'Binary search tree en C++',               'project' => 'Algorithmes en C/C++',         'priority' => 'high',     'due_date' => '2026-06-25', 'status' => 'todo'],
            ['title' => 'Lire chapitre 2 ISC² — Access Control',  'project' => 'ISC² CC Certification',        'priority' => 'high',     'due_date' => '2026-06-22', 'status' => 'todo'],
            ['title' => 'HTB — Machine Blue',                      'project' => 'Hack The Box — Starting Point','priority' => 'medium',   'due_date' => '2026-06-28', 'status' => 'todo'],
        ];

        foreach ($tasks as $t) {
            $projId = $projectMap[$t['project']] ?? null;
            $user->tasks()->create([
                'project_id' => $projId,
                'title'      => $t['title'],
                'priority'   => $t['priority'],
                'due_date'   => $t['due_date'],
                'status'     => $t['status'],
                'completed_at' => $t['status'] === 'done' ? now() : null,
            ]);
        }

        // Rappels
        $user->reminders()->createMany([
            ['title' => 'Passer l\'exam ISC² CC',         'remind_at' => '2026-08-14 09:00:00', 'priority' => 'critical', 'is_read' => false, 'message' => 'Exam demain — réviser les domaines faibles'],
            ['title' => 'Deadline HTB Starting Point',    'remind_at' => '2026-07-19 10:00:00', 'priority' => 'high',     'is_read' => false, 'message' => 'Terminer le parcours avant demain'],
            ['title' => 'Review code Algorithmes C',      'remind_at' => '2026-06-25 14:00:00', 'priority' => 'medium',   'is_read' => false, 'message' => 'Vérifier les implémentations de la semaine'],
            ['title' => 'Commit CyberBoard sur GitHub',   'remind_at' => '2026-06-16 20:00:00', 'priority' => 'medium',   'is_read' => false, 'message' => 'Push les derniers changements'],
        ]);

        $this->command->info('✓ CyberBoard seedé avec succès.');
        $this->command->info('  Compte : hagi@cyberboard.dev / password');
    }
}
