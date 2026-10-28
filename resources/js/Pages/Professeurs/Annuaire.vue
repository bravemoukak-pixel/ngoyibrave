<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, nextTick } from 'vue';

defineProps({
  professeursCollege: Array,
  professeursLycee: Array,
});

const sectionsAMasquer = ref([]);
const sectionImprimee = ref(null);
const toutesSections = ['college', 'lycee'];

async function imprimerSection(section) {
    sectionsAMasquer.value = toutesSections.filter(s => s !== section);
    sectionImprimee.value = section;
    document.body.classList.add('pdf-print-mode');
    await nextTick();
    window.print();
    sectionsAMasquer.value = [];
    sectionImprimee.value = null;
    document.body.classList.remove('pdf-print-mode');
}

async function imprimerTout() {
    sectionsAMasquer.value = [];
    sectionImprimee.value = 'tout';
    document.body.classList.add('pdf-print-mode');
    await nextTick();
    window.print();
    sectionsAMasquer.value = [];
    sectionImprimee.value = null;
    document.body.classList.remove('pdf-print-mode');
}

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
                Annuaire téléphonique des professeurs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="print-header justify-between" :class="{ 'cache-si-section-specifique': sectionImprimee && sectionImprimee !== 'tout' && sectionImprimee !== 'college' }">
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

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-700" :class="{ 'cache-si-section-specifique': sectionImprimee && sectionImprimee !== 'tout' && sectionImprimee !== 'college' }">
                            Annuaire téléphonique
                        </h3>

                        <div class="flex items-center gap-2 no-print">
                            <button @click="imprimerSection('college')" class="bg-blue-700 hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded-md">
                                🖶 Imprimer Collège
                            </button>
                            <button @click="imprimerSection('lycee')" class="bg-green-700 hover:bg-green-800 text-white font-semibold px-4 py-2 rounded-md">
                                🖶 Imprimer Lycée
                            </button>
                            <button @click="imprimerTout" class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md">
                                🖶 Imprimer tout
                            </button>
                        </div>
                    </div>

                    <!-- Collège -->
                    <div :class="{ 'no-print': sectionsAMasquer.includes('college') }">
                        <h4 class="text-md font-bold text-blue-700 mb-3">Collège</h4>
                        <div v-if="professeursCollege.length === 0" class="text-gray-500 mb-8">
                            Aucun professeur enregistré pour le collège.
                        </div>
                        <table v-else class="w-full border-collapse border border-gray-400 mb-8">
                            <thead>
                                <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                    <th class="p-3 border border-gray-400 w-10">N°</th>
                                    <th class="p-3 border border-gray-400">Nom</th>
                                    <th class="p-3 border border-gray-400">Prénom</th>
                                    <th class="p-3 border border-gray-400">Matière</th>
                                    <th class="p-3 border border-gray-400">Téléphone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prof, index) in professeursCollege" :key="prof.id" class="hover:bg-gray-50">
                                    <td class="p-3 border border-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.nom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.prenom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.matiere }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.telephone }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-4 text-right text-sm font-semibold text-gray-700">
                              La Direction des Études
                        </div>
                    </div>

                    <!-- Lycée -->
                    <div :class="{ 'no-print': sectionsAMasquer.includes('lycee') }">
                        <div class="print-header justify-between" :class="{ 'saut-page': sectionImprimee === 'tout' }">
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
                        <h3 class="text-lg font-semibold text-gray-700">Annuaire téléphonique</h3>
                        <h4 class="text-md font-bold text-green-700 mb-3">Lycée</h4>
                        <div v-if="professeursLycee.length === 0" class="text-gray-500">
                            Aucun professeur enregistré pour le lycée.
                        </div>
                        <table v-else class="w-full border-collapse border border-gray-400">
                            <thead>
                                <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                    <th class="p-3 border border-gray-400 w-10">N°</th>
                                    <th class="p-3 border border-gray-400">Nom</th>
                                    <th class="p-3 border border-gray-400">Prénom</th>
                                    <th class="p-3 border border-gray-400">Matière</th>
                                    <th class="p-3 border border-gray-400">Téléphone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prof, index) in professeursLycee" :key="prof.id" class="hover:bg-gray-50">
                                    <td class="p-3 border border-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.nom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.prenom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.matiere }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.telephone }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-4 text-right text-sm font-semibold text-gray-700">
                            La Direction des Études
                        </div>
                    </div>

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
    font-size: 14px;
}

.pdf-print-mode table th,
.pdf-print-mode table td {
    font-size: 14px !important;
    padding: 4px !important;
    line-height: 1.2 !important;
}

@media print {
    .cache-si-section-specifique {
        display: none !important;
    }
    .saut-page {
        page-break-before: always;
        break-before: page;
    }
}
</style>