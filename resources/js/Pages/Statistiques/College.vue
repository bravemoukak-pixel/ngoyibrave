<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Résultats globaux — Collège
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

                    <div v-for="(tableau, i) in tableauxMatieres" :key="i" class="mt-8">
                        <p class="text-center font-bold uppercase mb-2 text-sm">
                            Résultats disciplinaires — {{ tableau.matieres.join(' / ') }}
                        </p>
                        <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-400 p-1" rowspan="3">Classes</th>
                                    <template v-for="matiere in tableau.matieres" :key="matiere">
                                        <th class="border border-gray-400 p-1" colspan="4">{{ matiere.toUpperCase() }}</th>
                                    </template>
                                </tr>
                                <tr class="bg-gray-100">
                                    <template v-for="matiere in tableau.matieres" :key="'sub-' + matiere">
                                        <th class="border border-gray-400 p-1" colspan="2">Admis</th>
                                        <th class="border border-gray-400 p-1" colspan="2">Échoués</th>
                                    </template>
                                </tr>
                                <tr class="bg-gray-100">
                                    <template v-for="matiere in tableau.matieres" :key="'ft-' + matiere">
                                        <th class="border border-gray-400 p-1">T</th>
                                        <th class="border border-gray-400 p-1">F</th>
                                        <th class="border border-gray-400 p-1">T</th>
                                        <th class="border border-gray-400 p-1">F</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ligne in tableau.lignes" :key="ligne.libelle">
                                    <td class="border border-gray-400 p-1 font-semibold">{{ ligne.libelle }}</td>
                                    <template v-for="matiere in tableau.matieres" :key="ligne.libelle + '-' + matiere">
                                        <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].admis.t }}</td>
                                        <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].admis.df }}</td>
                                        <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].echoues.t }}</td>
                                        <td class="border border-gray-400 p-1">{{ ligne.matieres[matiere].echoues.df }}</td>
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
        <div class="mt-8">
                <p class="text-center font-bold uppercase mb-2 text-sm">
                    Résultats comparés des compositions trimestrielles
                </p>
                <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-400 p-1" rowspan="3">Niveau</th>
                            <th class="border border-gray-400 p-1" colspan="10">1ᵉʳ Trimestre</th>
                            <th class="border border-gray-400 p-1" colspan="10">2ᵉ Trimestre</th>
                            <th class="border border-gray-400 p-1" colspan="10">3ᵉ Trimestre</th>
                        </tr>
                        <tr class="bg-gray-100">
                            <template v-for="t in [1, 2, 3]" :key="'grp-' + t">
                                <th class="border border-gray-400 p-1" colspan="2">Inscrits</th>
                                <th class="border border-gray-400 p-1" colspan="2">Présents</th>
                                <th class="border border-gray-400 p-1" colspan="2">Admis</th>
                                <th class="border border-gray-400 p-1" colspan="2">%</th>
                                <th class="border border-gray-400 p-1" colspan="2">Échoués</th>
                            </template>
                        </tr>
                        <tr class="bg-gray-100">
                            <template v-for="t in [1, 2, 3]" :key="'sub-' + t">
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
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ligne in tableauCompare.lignes" :key="ligne.libelle">
                            <td class="border border-gray-400 p-1 font-semibold">{{ ligne.libelle }}</td>
                            <template v-for="t in [1, 2, 3]" :key="ligne.libelle + '-' + t">
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].inscrits.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].inscrits.df }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].presents.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].presents.df }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].admis.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].admis.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne['t' + t].admis.t, ligne['t' + t].presents.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(ligne['t' + t].admis.df, ligne['t' + t].presents.df) }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].echoues.t }}</td>
                                <td class="border border-gray-400 p-1">{{ ligne['t' + t].echoues.df }}</td>
                            </template>
                        </tr>
                        <tr class="font-bold bg-gray-50">
                            <td class="border border-gray-400 p-1">TOTAL</td>
                            <template v-for="t in [1, 2, 3]" :key="'tot-' + t">
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].inscrits.t }}</td>
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].inscrits.df }}</td>
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].presents.t }}</td>
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].presents.df }}</td>
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].admis.t }}</td>
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].admis.df }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(tableauCompare.totaux[t].admis.t, tableauCompare.totaux[t].presents.t) }}</td>
                                <td class="border border-gray-400 p-1">{{ pourcentage(tableauCompare.totaux[t].admis.df, tableauCompare.totaux[t].presents.df) }}</td>
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].echoues.t }}</td>
                                <td class="border border-gray-400 p-1">{{ tableauCompare.totaux[t].echoues.df }}</td>
                            </template>
                        </tr>
                    </tbody>
                </table>
                <p class="text-xs italic text-center mt-2">
                    En classe de 3ème, mettre les résultats du BEPC BLANC au 3ème trimestre
                </p>
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
    tableauxMatieres: Array,
    tableauAdmisRedoublants: Object,
    tableauCompare: Object,
});

function changerTrimestre(t) {
    router.get(route('statistiques.college'), { trimestre: t });
}

function pourcentage(valeur, total) {
    if (!total) return '0%';
    return Math.round((valeur / total) * 100) + '%';
}

function imprimer() {
    window.print();
}
</script>