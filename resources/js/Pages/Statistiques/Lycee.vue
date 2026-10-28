<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Résultats globaux — Lycée
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6 no-print">
                        <div class="flex gap-2">
                            <button
                                v-for="t in [1, 2, 3]"
                                :key="t"
                                @click="changerTrimestre(t)"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-semibold',
                                    trimestre === t ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                ]"
                            >
                                Trimestre {{ t }}
                            </button>
                        </div>
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

                    <p class="text-center font-bold uppercase mb-4">Résultats globaux</p>

                    <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-400 p-1" rowspan="3">Classes</th>
                                <th class="border border-gray-400 p-1" colspan="2">Inscrits</th>
                                <th class="border border-gray-400 p-1" colspan="4">Présents</th>
                                <th class="border border-gray-400 p-1" colspan="4">Admis</th>
                                <th class="border border-gray-400 p-1" colspan="4">Échoués</th>
                            </tr>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-400 p-1" rowspan="2">T</th>
                                <th class="border border-gray-400 p-1" rowspan="2">DF</th>
                                <th class="border border-gray-400 p-1" colspan="2">Nombre</th>
                                <th class="border border-gray-400 p-1" colspan="2">%</th>
                                <th class="border border-gray-400 p-1" colspan="2">Nombre</th>
                                <th class="border border-gray-400 p-1" colspan="2">%</th>
                                <th class="border border-gray-400 p-1" colspan="2">Nombre</th>
                                <th class="border border-gray-400 p-1" colspan="2">%</th>
                            </tr>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">DF</th>
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">DF</th>
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">DF</th>
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">DF</th>
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">DF</th>
                                <th class="border border-gray-400 p-1">T</th>
                                <th class="border border-gray-400 p-1">DF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ligne in lignes" :key="ligne.libelle">
                                <td class="border border-gray-400 p-1 font-semibold">{{ ligne.libelle }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.inscrits.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.inscrits.df }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.presents.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.presents.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne.presents.t, ligne.inscrits.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne.presents.df, ligne.inscrits.df) }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.admis.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.admis.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne.admis.t, ligne.presents.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne.admis.df, ligne.presents.df) }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.echoues.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne.echoues.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne.echoues.t, ligne.presents.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne.echoues.df, ligne.presents.df) }}</td>
                            </tr>
                            <tr class="font-bold bg-gray-50">
                                <td class="border border-gray-400 p-1">TOTAL</td>
                                <td class="border border-gray-400 p-1">{{ totaux.inscrits.t }}</td>
                                <td class="border border-gray-400 p-1">{{ totaux.inscrits.df }}</td>
                                <td class="border border-gray-400 p-1">{{ totaux.presents.t }}</td>
                                <td class="border border-gray-400 p-1">{{ totaux.presents.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(totaux.presents.t, totaux.inscrits.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(totaux.presents.df, totaux.inscrits.df) }}</td>
                                <td class="border border-gray-400 p-1">{{ totaux.admis.t }}</td>
                                <td class="border border-gray-400 p-1">{{ totaux.admis.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(totaux.admis.t, totaux.presents.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(totaux.admis.df, totaux.presents.df) }}</td>
                                <td class="border border-gray-400 p-1">{{ totaux.echoues.t }}</td>
                                <td class="border border-gray-400 p-1">{{ totaux.echoues.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(totaux.echoues.t, totaux.presents.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(totaux.echoues.df, totaux.presents.df) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-8">
                        <p class="text-center font-bold uppercase mb-2 text-sm">
                            Résultats disciplinaires — {{ tableauMatieres.matieres.join(' / ') }}
                        </p>
                        <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-400 p-1" rowspan="3">Classes</th>
                                    <template v-for="matiere in tableauMatieres.matieres" :key="matiere">
                                        <th class="border border-gray-400 p-1" colspan="4">{{ matiere.toUpperCase() }}</th>
                                    </template>
                                </tr>
                                <tr class="bg-gray-100">
                                    <template v-for="matiere in tableauMatieres.matieres" :key="'sub-' + matiere">
                                        <th class="border border-gray-400 p-1" colspan="2">Admis</th>
                                        <th class="border border-gray-400 p-1" colspan="2">Échoués</th>
                                    </template>
                                </tr>
                                <tr class="bg-gray-100">
                                    <template v-for="matiere in tableauMatieres.matieres" :key="'ft-' + matiere">
                                        <th class="border border-gray-400 p-1">T</th>
                                        <th class="border border-gray-400 p-1">F</th>
                                        <th class="border border-gray-400 p-1">T</th>
                                        <th class="border border-gray-400 p-1">F</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ligne in tableauMatieres.lignes" :key="ligne.libelle">
                                    <td class="border border-gray-400 p-1 font-semibold">{{ ligne.libelle }}</td>
                                    <template v-for="matiere in tableauMatieres.matieres" :key="ligne.libelle + '-' + matiere">
                                        <template v-if="ligne.matieres[matiere] === null">
                                            <td class="border border-gray-400 p-1" colspan="4">—</td>
                                        </template>
                                        <template v-else>
                                            <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].admis.t }}</td>
                                            <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].admis.df }}</td>
                                            <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].echoues.t }}</td>
                                            <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].echoues.df }}</td>
                                        </template>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        <p class="text-center font-bold uppercase mb-2 text-sm">
                            Résultats scolaires par niveau et par genre (Admis-Redoublants-Exclus)
                        </p>
                        <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-400 p-1" rowspan="2">Niveaux</th>
                                    <th class="border border-gray-400 p-1" colspan="2">Inscrits</th>
                                    <th class="border border-gray-400 p-1" colspan="2">Présents</th>
                                    <th class="border border-gray-400 p-1" colspan="2">Admis M≥10</th>
                                    <th class="border border-gray-400 p-1" colspan="2">Redoublants M&lt;10</th>
                                    <th class="border border-gray-400 p-1" colspan="2">Exclus M&lt;5</th>
                                    <th class="border border-gray-400 p-1" colspan="2">Abandons</th>
                                </tr>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">DF</th>
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">DF</th>
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">DF</th>
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">DF</th>
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">DF</th>
                                    <th class="border border-gray-400 p-1">T</th>
                                    <th class="border border-gray-400 p-1">DF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ligne in tableauAdmisRedoublants.lignes" :key="ligne.libelle">
                                    <td class="border border-gray-400 p-1 font-semibold">{{ ligne.libelle }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.inscrits.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.inscrits.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.presents.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.presents.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.admis.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.admis.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.redoublants.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.redoublants.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.exclus.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.exclus.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.abandons.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ ligne.abandons.df }}</td>
                                </tr>
                                <tr class="font-bold bg-gray-50">
                                    <td class="border border-gray-400 p-1">TOTAL</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.inscrits.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.inscrits.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.presents.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.presents.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.admis.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.admis.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.redoublants.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.redoublants.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.exclus.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.exclus.df }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.abandons.t }}</td>
                                    <td class="border border-gray-400 p-1">{{ tableauAdmisRedoublants.totaux.abandons.df }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    trimestre: Number,
    anneeScolaire: String,
    lignes: Array,
    totaux: Object,
    tableauMatieres: Object,
    tableauAdmisRedoublants: Object,
});

function changerTrimestre(t) {
    router.get(route('statistiques.lycee'), { trimestre: t });
}

function pourcentage(valeur, total) {
    if (!total) return '0%';
    return Math.round((valeur / total) * 100) + '%';
}

function imprimer() {
    window.print();
}
</script>