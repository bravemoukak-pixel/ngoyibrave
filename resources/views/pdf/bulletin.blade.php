<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .entete {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .entete-gauche {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .entete-droite {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: center;
        }

        .entete-gauche img {
            width: 45px;
            height: 45px;
            float: left;
            margin-right: 8px;
        }

        .entete-texte p {
            margin: 0;
            line-height: 1.3;
        }

        .titre-bulletin {
            text-align: center;
            border: 2px solid #333;
            border-radius: 4px;
            padding: 6px;
            margin: 10px 0;
        }

        .titre-bulletin p {
            margin: 0;
        }

        .titre-bulletin .principal {
            font-weight: bold;
            text-transform: uppercase;
        }

        .infos-eleve {
            width: 100%;
            margin-bottom: 10px;
        }

        .infos-eleve td {
            padding: 2px 4px;
            font-size: 11px;
        }

        table.notes {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 10px;
        }

        table.notes th,
        table.notes td {
            border: 1px solid #999;
            padding: 3px;
        }

        table.notes th {
            background: #eee;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .bloc-resultat {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .bloc-col {
            display: table-cell;
            width: 33%;
            vertical-align: top;
            padding: 4px;
        }

        .bloc-col.deux-colonnes {
            width: 50%;
        }

        .bloc-titre {
            font-weight: bold;
            margin-bottom: 4px;
        }

        table.detail {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }

        table.detail td {
            border: 1px solid #999;
            padding: 3px;
        }

        .mention {
            text-align: center;
            font-weight: bold;
            color: #047857;
            border-top: 1px solid #999;
            padding-top: 4px;
            margin-top: 4px;
        }

        .visa {
            display: table;
            width: 100%;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #999;
        }

        .visa-col {
            display: table-cell;
            width: 50%;
        }

        .visa-col.droite {
            text-align: right;
        }

        .decision {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            color: #047857;
            border: 1px solid #999;
            border-radius: 4px;
            padding: 6px;
            margin-top: 6px;
        }

        .date-signature {
            text-align: right;
            font-size: 9.5px;
            margin-top: 10px;
        }

        .bg-emerald {
            background: #ecfdf5;
        }

        .bg-gray {
            background: #f9fafb;
        }
    </style>
</head>

<body>

    <div class="entete">
        <div class="entete-gauche">
            <img src="{{ public_path('images/logo.png') }}">
            <div class="entete-texte">
                <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                <p class="font-bold" style="color:#047857;">LA DIVINE PROVIDENCE</p>
                <p>Paix-Joie-Travail</p>
                <p>Pointe-Noire</p>
            </div>
        </div>
        <div class="entete-droite">
            <p class="font-bold">République du Congo</p>
            <p>Ministère de l'Enseignement, Préscolaire, Primaire,</p>
            <p>Secondaire et de l'Alphabétisation</p>
        </div>
    </div>

    <div class="titre-bulletin">
        <p class="principal">Bulletin de notes du {{ $trimestreEnLettres }} trimestre</p>
        <p>Année scolaire {{ $anneeScolaire }}</p>
    </div>

    <table class="infos-eleve">
        <tr>
            <td><strong>Matricule :</strong> {{ $eleve->matricule ?? '—' }}</td>
            <td><strong>Classe :</strong> {{ $classe->nom }}</td>
        </tr>
        <tr>
            <td><strong>Nom :</strong> {{ $eleve->nom }}</td>
            <td><strong>Sexe :</strong> {{ $eleve->sexe }}</td>
        </tr>
        <tr>
            <td><strong>Prénom :</strong> {{ $eleve->prenom }}</td>
            <td></td>
        </tr>
    </table>

    <table class="notes">
        <thead>
            <tr>
                <th>Matière</th>
                <th class="text-center">{{ $estPrimaire ? 'Comp. 1' : 'D1' }}</th>
                <th class="text-center">{{ $estPrimaire ? 'Comp. 2' : 'D2' }}</th>
                @if(!$estPrimaire)<th class="text-center">MC</th>@endif
                <th class="text-center">{{ $estPrimaire ? 'Comp.T' : 'Compo' }}</th>
                <th class="text-center">Coef</th>
                <th class="text-center">Total</th>
                <th class="text-center">{{ $estPrimaire ? 'Moy.' : 'Moy /20' }}</th>
                <th class="text-center">Rang</th>
                <th>Appréciation</th>
                <th>{{ $estPrimaire ? 'Enseignant' : 'Professeur' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lignes as $index => $ligne)
            <tr>
                <td class="font-bold">{{ $ligne['matiere'] }}</td>
                <td class="text-center">{{ $ligne['d1'] ?? '—' }}</td>
                <td class="text-center">{{ $ligne['d2'] ?? '—' }}</td>
                @if(!$estPrimaire)<td class="text-center">{{ $ligne['mc'] ?? '—' }}</td>@endif
                <td class="text-center">{{ $ligne['composition'] ?? '—' }}</td>
                <td class="text-center">{{ $ligne['coefficient'] }}</td>
                <td class="text-center">{{ $ligne['totalBrut'] ?? '—' }}</td>
                <td class="text-center font-bold">{{ $ligne['moyenne'] ?? '—' }}</td>
                <td class="text-center">{{ $ligne['rang'] }}</td>
                <td>{{ $ligne['appreciation'] }}</td>
                @if($estPrimaire)
                @if($index === 0)
                <td class="text-center" rowspan="{{ count($lignes) }}">{{ $lignes[0]['professeur'] ?? '' }}</td>
                @endif
                @else
                <td>{{ $ligne['professeur'] }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($estPrimaire)
            <tr class="font-bold bg-gray">
                <td colspan="4">Total</td>
                <td class="text-center">{{ $totalCoef }}</td>
                <td class="text-center">{{ number_format($totalTotal, 2) }}</td>
                <td colspan="4"></td>
            </tr>
            @else
            <tr class="font-bold bg-gray">
                <td colspan="3">Total</td>
                <td class="text-center">{{ number_format($totalMC, 2) }}</td>
                <td colspan="1"></td>
                <td class="text-center">{{ $totalCoef }}</td>
                <td class="text-center">{{ number_format($totalTotal, 2) }}</td>
                <td colspan="3"></td>
            </tr>
            @endif

            <tr class="font-bold bg-gray">
                <td colspan="{{ $estPrimaire ? 6 : 7 }}">Moyenne trimestrielle</td>
                <td class="text-center">{{ $moyenneGenerale ?? '—' }}</td>
                <td colspan="3"></td>
            </tr>

            @if($trimestre === 3)
            <tr class="font-bold bg-emerald">
                <td colspan="{{ $estPrimaire ? 6 : 7 }}">Moyenne annuelle</td>
                <td class="text-center">{{ $moyenneAnnuelle ?? '—' }}</td>
                <td colspan="3"></td>
            </tr>
            @endif
        </tfoot>
    </table>

    <div class="bloc-resultat">
        <div class="bloc-col {{ $trimestre === 3 ? '' : 'deux-colonnes' }}">
            <p class="bloc-titre">Résultat trimestriel</p>
            <table class="detail">
                @if(!$estPrimaire)
                <tr>
                    <td>Moyenne de classe</td>
                    <td class="text-right font-bold">{{ $moyenneControleEleve ?? '—' }}</td>
                </tr>
                @endif
                <tr>
                    <td>Moyenne la plus haute</td>
                    <td class="text-right">{{ $moyennePlusHaute ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Moyenne la plus basse</td>
                    <td class="text-right">{{ $moyennePlusBasse ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Effectif total de la classe</td>
                    <td class="text-right">{{ $effectifClasse }}</td>
                </tr>
                @if($trimestre !== 3)
                <tr>
                    <td class="font-bold">Appréciation du travail</td>
                    <td class="text-right font-bold">{{ $appreciationTravail }}</td>
                </tr>
                @endif
            </table>
            @if(!$estPrimaire)<p class="mention">Mention : {{ $mention }}</p>@endif
        </div>

        <div class="bloc-col {{ $trimestre === 3 ? '' : 'deux-colonnes' }}">
            <p class="bloc-titre">Discipline</p>
            <table class="detail">
                <tr>
                    <td>Absences justifiées</td>
                    <td class="text-right">{{ $assiduite['absences_justifiees'] }}</td>
                </tr>
                <tr>
                    <td>Absences injustifiées</td>
                    <td class="text-right">{{ $assiduite['absences_injustifiees'] }}</td>
                </tr>
                <tr>
                    <td>Retards justifiés</td>
                    <td class="text-right">{{ $assiduite['retards_justifies'] }}</td>
                </tr>
                <tr>
                    <td>Retards injustifiés</td>
                    <td class="text-right">{{ $assiduite['retards_injustifies'] }}</td>
                </tr>
            </table>
            <p style="font-size:9.5px; margin-top:4px;"><strong>Avertissements :</strong> {{ $assiduite['avertissements'] }}</p>
            <p style="font-size:9.5px;"><strong>Blâme :</strong> {{ $assiduite['blame'] }}</p>
        </div>

        @if($trimestre === 3)
        <div class="bloc-col bg-emerald">
            <p class="bloc-titre">Résultat annuel</p>
            <table class="detail">
                <tr>
                    <td>Rang</td>
                    <td class="text-right font-bold">{{ $rangAnnuel }}</td>
                </tr>
                <tr>
                    <td>Moyenne annuelle la plus haute</td>
                    <td class="text-right">{{ $moyennePlusHauteAnnuelle ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Moyenne annuelle la plus basse</td>
                    <td class="text-right">{{ $moyennePlusBasseAnnuelle ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Effectif total de la classe</td>
                    <td class="text-right">{{ $effectifClasse }}</td>
                </tr>
            </table>
            @if(!$estPrimaire)<p class="mention">Mention annuelle : {{ $mentionAnnuelle }}</p>@endif
        </div>
        @endif
    </div>

    <div class="visa">
        <div class="visa-col">
            <p class="font-bold">Visa des parents ou tuteurs</p>
        </div>
        <div class="visa-col droite">
            <p class="font-bold">Visa de la Directrice</p>
            @if($decisionAnnuelle)
            <p class="decision">{{ $decisionAnnuelle }}</p>
            @endif
        </div>
    </div>

    <p class="date-signature">Pointe-Noire le {{ $dateSignature }}</p>

</body>

</html>