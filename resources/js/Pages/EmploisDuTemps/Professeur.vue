<template>
    <AdminLayout>
        <div class="p-6">
            <div class="mb-8">
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div class="flex items-center gap-3">
                        <img src="/images/logo.png" alt="Logo" class="w-14 h-14" />
                        <div>
                            <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                            <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                            <p>Paix-Joie-Travail</p>
                            <p>Pointe-Noire</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">Année scolaire {{ anneeScolaire }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold text-gray-800">
                    Emploi du temps — {{ professeur.nom }} {{ professeur.prenom }}
                </h1>
                <button @click="imprimer" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 no-print">
                    Imprimer
                </button>
            </div>

            <!-- Onglets : un par grille utilisée par ce professeur -->
            <div v-if="Object.keys(creneaux).length > 1" class="flex gap-2 mb-4 no-print">
                <button v-for="grille in Object.keys(creneaux)"
                        :key="grille"
                        @click="grilleActive = grille"
                        class="px-4 py-2 rounded font-medium text-sm"
                        :class="grilleActive === grille ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    {{ libelleGrille(grille) }}
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-emerald-600 text-white">
                            <th class="border border-gray-300 p-2 w-32">Horaire</th>
                            <th v-for="jour in jours" :key="jour" class="border border-gray-300 p-2 capitalize">
                                {{ jour }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="creneau in creneaux[grilleActive]" :key="creneau.id">
                            <td class="border border-gray-300 p-2 font-medium text-center bg-gray-50">
                                {{ formatHeure(creneau.heure_debut) }} - {{ formatHeure(creneau.heure_fin) }}
                            </td>

                            <td v-if="creneau.type !== 'cours'" :colspan="jours.length" class="border border-gray-300 p-2 text-center bg-gray-200 font-semibold text-gray-600 uppercase text-xs">
                                {{ creneau.type === 'recreation' ? 'Récréation' : 'Pause' }}
                            </td>

                            <template v-else>
                                <td v-for="jour in jours" :key="jour + creneau.id" class="border border-gray-300 p-2 align-top">
                                    <div v-if="getEmplois(jour, creneau.id).length > 0">
                                        <p class="font-semibold text-gray-800">
                                            {{ getEmplois(jour, creneau.id).map(e => e.classe?.nom).join(' + ') }}
                                        </p>
                                        <p class="text-gray-600 text-xs">{{ getEmplois(jour, creneau.id)[0].matiere }}</p>
                                        <span class="text-xs px-1 rounded"
                                              :class="getEmplois(jour, creneau.id)[0].type_seance === 'td' ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-800'">
                                            {{ getEmplois(jour, creneau.id)[0].type_seance === 'td' ? 'TD' : 'Cours' }}
                                        </span>
                                    </div>
                                    <div v-else-if="pasDeCours(jour, creneau)" class="text-gray-300 text-center">—</div>
                                    <div v-else class="text-gray-500 text-center font-semibold">TG</div>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-6 text-right">
                <p class="font-semibold text-gray-800">Le Directeur des Études</p>
            </div>
            <div class="mt-4 text-center avertissement">
                <p class="text-xs italic text-gray-600">
                    Cet emploi du temps ne doit être ni modifié ni échangé sans l'avis du D.E.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    professeur: Object,
    jours: Array,
    creneaux: Object,
    emplois: Array,
    anneeScolaire: String,
});

const grilleActive = ref(Object.keys(props.creneaux)[0]);

function getEmplois(jour, creneauId) {
    return props.emplois.filter(e => e.jour === jour && e.creneau_horaire_id === creneauId);
}

function formatHeure(heure) {
    return heure.slice(0, 5);
}

function imprimer() {
    document.body.classList.add('pdf-print-mode');

    const style = document.createElement('style');
    style.id = 'print-landscape-temp';
    style.textContent = '@page { size: landscape; margin: 0.5cm; }';
    document.head.appendChild(style);

    function nettoyer() {
        document.getElementById('print-landscape-temp')?.remove();
        document.body.classList.remove('pdf-print-mode');
        window.onafterprint = null;
    }

    window.onafterprint = nettoyer;
    window.print();

    // Filet de sécurité : si onafterprint ne se déclenche pas (annulation,
    // navigation Inertia, certains navigateurs), on nettoie quand même après 2s
    setTimeout(nettoyer, 2000);
}

function libelleGrille(grille) {
    if (grille === 'primaire_a') return 'Semaine A';
    if (grille === 'primaire_b') return 'Semaine B';
    return 'Emploi du temps';
}
function pasDeCours(jour, creneau) {
    return jour === 'lundi' && creneau.heure_debut >= '13:45';
}
</script>

<style scoped>
    .pdf-print-mode .no-print {
        display: none !important;
    }

    .pdf-print-mode .p-6 {
        padding: 0.2cm !important;
    }

    .pdf-print-mode .mb-8 {
        margin-bottom: 0.3rem !important;
    }

    .pdf-print-mode .mb-6 {
        margin-bottom: 0.2rem !important;
    }

    .pdf-print-mode .mb-4 {
        margin-bottom: 0.2rem !important;
    }

    .pdf-print-mode img {
        width: 32px !important;
        height: 32px !important;
    }

    .pdf-print-mode .text-sm {
        font-size: 10px !important;
    }

    .pdf-print-mode h1 {
        font-size: 13px !important;
        margin: 0 !important;
    }

    .pdf-print-mode table {
        font-size: 8px !important;
    }

    .pdf-print-mode table th,
    .pdf-print-mode table td {
        padding: 1px 2px !important;
        line-height: 1.1 !important;
    }

    .pdf-print-mode .mt-6 {
        margin-top: 0.2rem !important;
    }

    .pdf-print-mode .avertissement p {
        font-size: 9px !important;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        table {
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
        }
    }
</style>