<?php

namespace App\Http\Controllers;

use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Support\Facades\Response;

class SauvegardeController extends Controller
{
    public function exporter()
    {
        $nomFichier = 'sauvegarde_ngoyibrave_' . now()->format('Y-m-d_His') . '.sql';
        $cheminTemporaire = storage_path('app/' . $nomFichier);

        $dump = new Mysqldump(
            'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );

        $dump->start($cheminTemporaire);

        return response()->download($cheminTemporaire, $nomFichier)->deleteFileAfterSend(true);
    }
}
