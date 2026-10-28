<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Répartition des élèves par âge et genre
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex justify-end mb-6 no-print">
                        <button
                            @click="imprimer"
                            class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer
                        </button>
                    </div>

                    <div class="print-header justify-between flex">
                        <div class="flex items-center gap-3">
                            <img src="/images/logo.png" alt="Logo" class="w-12 h-12" />
                            <div class="text-sm">
                                <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                                <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Année scolaire {{ anneeScolaire }}</p>
                    </div>

                    <p class="text-center font-bold uppercase mb-2 text-sm">Répartition des élèves par niveau, âge et genre — Collège</p>
                    <table class="w-full border-collapse border border-gray-400 text-xs text-center mb-8">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-400 p-1" rowspan="2">N°</th>
                                <th class="border border-gray-400 p-1" rowspan="2">Âge</th>
                                <template v-for="classe in college.classes" :key="classe">
                                    <th class="border border-gray-400 p-1" colspan="2">{{ classe }}</th>
                                </template>
                                <th class="border border-gray-400 p-1" colspan="2">TOTAL</th>
                            </tr>
                            <tr class="bg-gray-100">
                                <template v-for="classe in college.classes" :key="'sub-' + classe">
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">F</th>
                                </template>
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">F</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(ligne, index) in college.lignes" :key="ligne.age">
                                <td class="border border-gray-400 p-1">{{ index + 1 }}</td>
                                <td class="border border-gray-400 p-1 font-semibold">{{ ligne.age }} ans</td>
                                <template v-for="classe in college.classes" :key="ligne.age + '-' + classe">
                                    <td class="border border-gray-400 p-1">{{ ligne.classes[classe].t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.classes[classe].f }}</td>
                                </template>
                                <td class="border border-gray-400 p-1 font-semibold">{{ ligne.totalT }}</td>
                                <td class="border border-gray-400 p-1 font-semibold">{{ ligne.totalF }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="print-header justify-between flex saut-page">
                        <div class="flex items-center gap-3">
                            <img src="/images/logo.png" alt="Logo" class="w-12 h-12" />
                            <div class="text-sm">
                                <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                                <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Année scolaire {{ anneeScolaire }}</p>
                    </div>

                    <p class="text-center font-bold uppercase mb-2 text-sm">Répartition des élèves par niveau, âge et genre — Lycée</p>
                    <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-400 p-1" rowspan="2">N°</th>
                                <th class="border border-gray-400 p-1" rowspan="2">Âge</th>
                                <template v-for="classe in lycee.classes" :key="classe">
                                    <th class="border border-gray-400 p-1" colspan="2">{{ classe }}</th>
                                </template>
                                <th class="border border-gray-400 p-1" colspan="2">TOTAL</th>
                            </tr>
                            <tr class="bg-gray-100">
                                <template v-for="classe in lycee.classes" :key="'sub-' + classe">
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">F</th>
                                </template>
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">F</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(ligne, index) in lycee.lignes" :key="ligne.age">
                                <td class="border border-gray-400 p-1">{{ index + 1 }}</td>
                                <td class="border border-gray-400 p-1 font-semibold">{{ ligne.age }} ans</td>
                                <template v-for="classe in lycee.classes" :key="ligne.age + '-' + classe">
                                    <td class="border border-gray-400 p-1">{{ ligne.classes[classe].t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.classes[classe].f }}</td>
                                </template>
                                <td class="border border-gray-400 p-1 font-semibold">{{ ligne.totalT }}</td>
                                <td class="border border-gray-400 p-1 font-semibold">{{ ligne.totalF }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    anneeScolaire: String,
    college: Object,
    lycee: Object,
});

function imprimer() {
    document.body.classList.add('pdf-print-mode');

    const style = document.createElement('style');
    style.id = 'print-landscape-temp';
    style.textContent = '@page { size: A4 landscape; margin: 0.6cm; }';
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
</script>

<style scoped>
.pdf-print-mode table {
    font-size: 9px !important;
}

.pdf-print-mode table th,
.pdf-print-mode table td {
    padding: 2px !important;
    line-height: 1.1 !important;
}

@media print {
    table {
        page-break-inside: avoid;
    }
    tr {
        page-break-inside: avoid;
    }
    .saut-page {
        page-break-before: always;
        break-before: page;
    }
}
</style>