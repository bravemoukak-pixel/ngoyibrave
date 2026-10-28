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
                    Emploi du temps — {{ classe.nom }}
                </h1>
                <button @click="imprimer" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 no-print">
                    Imprimer
                </button>
            </div>

            <!-- Onglets Semaine A / Semaine B pour le primaire -->
            <div v-if="estPrimaire" class="flex gap-2 mb-4 no-print">
                <button v-for="grille in Object.keys(creneaux)"
                        :key="grille"
                        @click="grilleActive = grille"
                        class="px-4 py-2 rounded font-medium text-sm"
                        :class="grilleActive === grille ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    {{ grille === 'primaire_a' ? 'Semaine A' : 'Semaine B' }}
                </button>
            </div>
            <p v-if="estPrimaire" class="text-sm font-semibold text-gray-600 mb-2 hidden print:block">
                {{ grilleActive === 'primaire_a' ? 'Semaine A' : 'Semaine B' }}
            </p>

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
                                <td v-for="jour in jours"
                                    :key="jour + creneau.id"
                                    class="border border-gray-300 p-2 align-top cursor-pointer hover:bg-emerald-50"
                                    @click="ouvrirModal(jour, creneau)">
                                    <div v-if="getEmploi(jour, creneau.id)">
                                        <p class="font-semibold text-gray-800">{{ getEmploi(jour, creneau.id).matiere }}</p>
                                        <p class="text-gray-600 text-xs">
                                            {{ getEmploi(jour, creneau.id).professeur?.nom }} {{ getEmploi(jour, creneau.id).professeur?.prenom }}
                                        </p>
                                        <span class="text-xs px-1 rounded"
                                              :class="getEmploi(jour, creneau.id).type_seance === 'td' ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-800'">
                                            {{ getEmploi(jour, creneau.id).type_seance === 'td' ? 'TD' : 'Cours' }}
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
                    Cet emploi du temps ne doit pas être modifié sans l'avis du D.E.
                </p>
            </div>


        </div>



        <!-- Modal de saisie -->
        <div v-if="modalOuvert" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 no-print">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-lg font-bold mb-4 capitalize">{{ jourSelectionne }} — {{ creneauSelectionne ? formatHeure(creneauSelectionne.heure_debut) + ' - ' + formatHeure(creneauSelectionne.heure_fin) : '' }}</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
                        <input v-model="form.matiere" type="text" class="w-full border rounded px-3 py-2" placeholder="Ex: Mathématiques" />
                    </div>

                    <div>
                       <label class="block text-sm font-medium text-gray-700 mb-1">Professeur (optionnel)</label>
                        <select v-model="form.professeur_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Choisir --</option>
                            <option v-for="prof in professeurs" :key="prof.id" :value="prof.id">
                                {{ prof.nom }} {{ prof.prenom }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de séance</label>
                        <select v-model="form.type_seance" class="w-full border rounded px-3 py-2">
                            <option value="cours">Cours</option>
                            <option value="td">Travaux dirigés (TD)</option>
                        </select>
                    </div>

                    <p v-if="form.errors.professeur_id" class="text-red-600 text-sm">{{ form.errors.professeur_id }}</p>
                </div>

                <div class="flex justify-between mt-6">
                    <button v-if="emploiEnCours" @click="supprimer" class="text-red-600 hover:underline text-sm">
                        Supprimer cette case
                    </button>
                    <div class="flex gap-2 ml-auto">
                        <button @click="fermerModal" class="px-4 py-2 rounded border">Annuler</button>
                        <button @click="enregistrer" class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
        classe: Object,
        estPrimaire: Boolean,
        jours: Array,
        creneaux: Object,
        emplois: Array,
        professeurs: Array,
        anneeScolaire: String,
});
const classesSansCoursWeekend = ['6ᵉ 1', '6ᵉ 2', '5ᵉ', '4ᵉ', 'Seconde A', 'Seconde C', 'Première A', 'Première S'];
const classesSamediLimite = ['3ᵉ', 'Terminale A', 'Terminale C', 'Terminale D'];

function pasDeCours(jour, creneau) {
    if (jour === 'lundi' && creneau.heure_debut >= '13:45') return true;
    if (jour === 'samedi') {
        if (classesSansCoursWeekend.includes(props.classe.nom)) return true;
        if (classesSamediLimite.includes(props.classe.nom) && creneau.heure_debut >= '11:45') return true;
    }
    return false;
}

const grilleActive = ref(Object.keys(props.creneaux)[0]);
const modalOuvert = ref(false);
const jourSelectionne = ref(null);
const creneauSelectionne = ref(null);
const emploiEnCours = ref(null);

const form = useForm({
        jour: '',
        creneau_horaire_id: '',
        classe_id: props.classe.id,
        professeur_id: '',
        matiere: '',
        type_seance: 'cours',
});

function getEmploi(jour, creneauId) {
        return props.emplois.find(e => e.jour === jour && e.creneau_horaire_id === creneauId);
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
        setTimeout(nettoyer, 2000);
}

function ouvrirModal(jour, creneau) {
        jourSelectionne.value = jour;
        creneauSelectionne.value = creneau;
        const existant = getEmploi(jour, creneau.id);
        emploiEnCours.value = existant || null;

        form.jour = jour;
        form.creneau_horaire_id = creneau.id;
        form.matiere = existant?.matiere || '';
        form.professeur_id = existant?.professeur_id || '';
        form.type_seance = existant?.type_seance || 'cours';
        form.clearErrors();

        modalOuvert.value = true;
}
function fermerModal() {
        modalOuvert.value = false;  
}

function enregistrer() {
        form.post(route('emplois-du-temps.store'), {
            preserveScroll: true,
            onSuccess: () => fermerModal(),
        });
}

function supprimer() {
        if (!emploiEnCours.value) return;
        router.delete(route('emplois-du-temps.destroy', emploiEnCours.value.id), {
            preserveScroll: true,
            onSuccess: () => fermerModal(),
        });
}

onUnmounted(() => {
        document.getElementById('print-landscape-temp')?.remove();
        document.body.classList.remove('pdf-print-mode');
});
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

    .pdf-print-mode table {
        font-size: 8px !important;
    }

        .pdf-print-mode table th,
        .pdf-print-mode table td {
            padding: 4px 2px !important;
            line-height: 1.4 !important;
        }

    .pdf-print-mode img {
        width: 48px !important;
        height: 48px !important;
    }

    .pdf-print-mode .text-sm {
        font-size: 12px !important;
    }

    .pdf-print-mode h1 {
        font-size: 16px !important;
    }
    .pdf-print-mode .mt-6 {
        margin-top: 0.2rem !important;
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

        .pdf-print-mode .avertissement p {
            font-size: 10px !important;
        }
    }
</style>
