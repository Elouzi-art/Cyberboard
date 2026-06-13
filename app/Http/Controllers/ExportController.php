<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function pdf()
    {
        $user     = auth()->user();
        $projects = $user->projects()->with(['tasks', 'tags'])->orderBy('end_date')->get();

        $html = view('exports.planning_pdf', compact('projects', 'user'))->render();

        // PDF simple via HTML print (sans dépendance externe)
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('X-Export-Type', 'pdf');
    }

    public function csv()
    {
        $user     = auth()->user();
        $projects = $user->projects()->with('tasks')->get();

        $rows   = [];
        $rows[] = ['Projet', 'Catégorie', 'Priorité', 'Statut', 'Progression', 'Début', 'Fin', 'Tâches total', 'Tâches done'];

        foreach ($projects as $p) {
            $rows[] = [
                $p->title,
                $p->category_label,
                $p->priority_label,
                $p->status,
                $p->progress . '%',
                $p->start_date?->format('d/m/Y') ?? '',
                $p->end_date?->format('d/m/Y') ?? '',
                $p->tasks->count(),
                $p->tasks->where('status', 'done')->count(),
            ];
        }

        $filename = 'cyberboard-planning-' . now()->format('Y-m-d') . '.csv';
        $handle   = fopen('php://temp', 'r+');
        foreach ($rows as $row) fputcsv($handle, $row, ';');
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
