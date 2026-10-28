<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    professeurs: Array,
    periode: String,
    date: String,
});

const periodeSelectionnee = ref(props.periode);
const dateSelectionnee = ref(props.date);

function actualiser() {
    router.get(route('pointages.recap'), {
        periode: periodeSelectionnee.value,
        date: dateSelectionnee.value,
    }, { preserveState: true });
}

function imprimer() {
    document.body.classList.add('pdf-print-mode');

    function nettoyer() {
        document.body.classList.remove('pdf-print-mode');
        window.onafterprint = null;
    }

    window.onafterprint = nettoyer;
    window.print();
    setTimeout(nettoyer, 2000);
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

const professeursCollege = computed(() => props.professeurs.filter(p => p.niveau === 'Collège'));
const professeursLycee = computed(() => props.professeurs.filter(p => p.niveau === 'Lycée'));

const totalCollege = computed(() => professeursCollege.value.reduce((s, p) => s + p.montant, 0));
const totalLycee = computed(() => professeursLycee.value.reduce((s, p) => s + p.montant, 0));

function anneeScolaire() {
    const maintenant = new Date();
    const mois = maintenant.getMonth() + 1;
    const annee = maintenant.getFullYear();

    if (mois >= 10) return `${annee}-${annee + 1}`;
    if (mois <= 6) return `${annee - 1}-${annee}`;
    return `${annee}-${annee + 1}`;
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Récapitulatif des heures et montants
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6 no-print">
                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                                <select v-model="periodeSelectionnee" @change="actualiser" class="rounded-md border-gray-300">
                                    <option value="jour">Jour</option>
                                    <option value="semaine">Semaine</option>
                                    <option value="mois">Mois</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de référence</label>
                                <input
                                    v-model="dateSelectionnee"
                                    type="date"
                                    @change="actualiser"
                                    class="rounded-md border-gray-300"
                                />
                            </div>
                        </div>

                        <button
                            @click="imprimer"
                            class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer
                        </button>
                    </div>

                  <div class="print-header justify-between">
                        <div class="flex items-center gap-3">
                            <img src="/images/logo.png" alt="Logo" class="w-12 h-12" />
                            <div class="text-sm">
                                <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                                <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                                <p class="text-xs text-gray-600">Paix-Joie-Travail</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Année scolaire {{ anneeScolaire() }}</p>
                    </div>

                  <p class="text-center font-bold uppercase mb-2 text-sm">Collège</p>
                    <table class="w-full border-collapse border border-gray-400 text-sm mb-8">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600">
                                <th class="p-3 border border-gray-400 w-12">N°</th>
                                <th class="p-3 border border-gray-400">Professeur</th>
                                <th class="p-3 border border-gray-400 w-28">Dates travaillées</th>
                                <th class="p-3 border border-gray-400 w-28">Heures de cours effectuées</th>
                                <th class="p-3 border border-gray-400 w-24">Heures de TD</th>
                                <th class="p-3 border border-gray-400 w-24">Total heures</th>
                                <th class="p-3 border border-gray-400 w-28">Taux horaire</th>
                                <th class="p-3 border border-gray-400">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(prof, index) in professeursCollege" :key="prof.id" class="hover:bg-gray-50">
                                <td class="p-3 border border-gray-400 text-center">{{ index + 1 }}</td>
                                <td class="p-3 border border-gray-400 font-medium">{{ prof.nom }} {{ prof.prenom }}</td>
                                <td class="p-3 border border-gray-400 text-sm text-gray-600">{{ prof.dates }}</td>
                                <td class="p-3 border border-gray-400 text-center">{{ prof.heures_cours }} h</td>
                                <td class="p-3 border border-gray-400 text-center">{{ prof.heures_td }} h</td>
                                <td class="p-3 border border-gray-400 text-center font-semibold">{{ prof.heures }} h</td>
                                <td class="p-3 border border-gray-400 text-center">{{ formatMontant(prof.taux) }}</td>
                                <td class="p-3 border border-gray-400 text-right font-semibold">{{ formatMontant(prof.montant) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-50">
                                <td class="p-3 border border-gray-400" colspan="7">Total Collège</td>
                                <td class="p-3 border border-gray-400 text-right text-emerald-700">{{ formatMontant(totalCollege) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                   <div class="print-header justify-between saut-page">
                        <div class="flex items-center gap-3">
                            <img src="/images/logo.png" alt="Logo" class="w-12 h-12" />
                            <div class="text-sm">
                                <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                                <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                                <p class="text-xs text-gray-600">Paix-Joie-Travail</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Année scolaire {{ anneeScolaire() }}</p>
                    </div>

                    <p class="text-center font-bold uppercase mb-2 text-sm">Lycée</p>
                    <table class="w-full border-collapse border border-gray-400 text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600">
                                <th class="p-3 border border-gray-400 w-12">N°</th>
                                <th class="p-3 border border-gray-400">Professeur</th>
                                <th class="p-3 border border-gray-400 w-28">Dates travaillées</th>
                                <th class="p-3 border border-gray-400 w-28">Heures de cours effectuées</th>
                                <th class="p-3 border border-gray-400 w-24">Heures de TD</th>
                                <th class="p-3 border border-gray-400 w-24">Total heures</th>
                                <th class="p-3 border border-gray-400 w-28">Taux horaire</th>
                                <th class="p-3 border border-gray-400">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(prof, index) in professeursLycee" :key="prof.id" class="hover:bg-gray-50">
                                <td class="p-3 border border-gray-400 text-center">{{ index + 1 }}</td>
                                <td class="p-3 border border-gray-400 font-medium">{{ prof.nom }} {{ prof.prenom }}</td>
                                <td class="p-3 border border-gray-400 text-sm text-gray-600">{{ prof.dates }}</td>
                                <td class="p-3 border border-gray-400 text-center">{{ prof.heures_cours }} h</td>
                                <td class="p-3 border border-gray-400 text-center">{{ prof.heures_td }} h</td>
                                <td class="p-3 border border-gray-400 text-center font-semibold">{{ prof.heures }} h</td>
                                <td class="p-3 border border-gray-400 text-center">{{ formatMontant(prof.taux) }}</td>
                                <td class="p-3 border border-gray-400 text-right font-semibold">{{ formatMontant(prof.montant) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-50">
                                <td class="p-3 border border-gray-400" colspan="7">Total Lycée</td>
                                <td class="p-3 border border-gray-400 text-right text-emerald-700">{{ formatMontant(totalLycee) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
<style scoped>
.pdf-print-mode .p-6 {
    padding-top: 0.3cm !important;
    padding-bottom: 0.3cm !important;
    padding-left: 0.15cm !important;
    padding-right: 0.15cm !important;
}

.pdf-print-mode table {
    font-size: 11px;
}

.pdf-print-mode table th,
.pdf-print-mode table td {
    font-size: 11px !important;
    padding: 5px !important;
    line-height: 1.2 !important;
}
@media print {
    .saut-page {
        page-break-before: always;
        break-before: page;
    }
}
</style>
