<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    classe: Object,
    matiere: String,
    trimestre: Number,
    typeEvaluation: String,
    matieres: Array,
    types: Array,
    eleves: Array,
    professeur: String,
});

const matiereSelectionnee = ref(props.matiere);
const trimestreSelectionne = ref(props.trimestre);
const typeSelectionne = ref(props.typeEvaluation);

function actualiser() {
    router.get(route('releve-vierge.index', props.classe.id), {
        matiere: matiereSelectionnee.value,
        trimestre: trimestreSelectionne.value,
        type_evaluation: typeSelectionne.value,
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
                Relevé de notes à remplir — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6 no-print">
                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
                                <select v-model="matiereSelectionnee" @change="actualiser" class="rounded-md border-gray-300">
                                    <option v-for="m in matieres" :key="m" :value="m">{{ m }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Trimestre</label>
                                <select v-model="trimestreSelectionne" @change="actualiser" class="rounded-md border-gray-300">
                                    <option :value="1">Trimestre 1</option>
                                    <option :value="2">Trimestre 2</option>
                                    <option :value="3">Trimestre 3</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Évaluation</label>
                                <select v-model="typeSelectionne" @change="actualiser" class="rounded-md border-gray-300">
                                    <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
                                </select>
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

                    <div class="flex justify-between items-center mb-4 text-sm">
                        <p><span class="font-semibold">Classe :</span> {{ classe.nom }} — <span class="font-semibold">Effectif :</span> {{ eleves.length }}</p>
                        <div class="text-center border-2 border-gray-800 rounded-md px-4 py-1 font-bold uppercase">
                            {{ matiere }} — {{ typeEvaluation }} (Trimestre {{ trimestre }})
                        </div>
                    </div>

                    <table class="w-full border-collapse border border-gray-400 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border border-gray-400">Matricule</th>
                                <th class="p-2 border border-gray-400 text-left">Nom et Prénom</th>
                                <th class="p-2 border border-gray-400">Sexe</th>
                                <th class="p-2 border border-gray-400">Note /20</th>
                                <th class="p-2 border border-gray-400">Note supplémentaire</th>
                                <th class="p-2 border border-gray-400">Appréciation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="eleve in eleves" :key="eleve.id">
                                <td class="p-2 border border-gray-400 text-center">{{ eleve.matricule ?? '—' }}</td>
                                <td class="p-2 border border-gray-400">{{ eleve.nom }} {{ eleve.prenom }}</td>
                                <td class="p-2 border border-gray-400 text-center">{{ eleve.sexe }}</td>
                                <td class="p-2 border border-gray-400"></td>
                                <td class="p-2 border border-gray-400"></td>
                                <td class="p-2 border border-gray-400"></td>
                            </tr>
                        </tbody>
                    </table>

                   <div class="mt-8 flex justify-between text-sm">
                        <p>Professeur : <strong>{{ professeur ?? '____________________________' }}</strong></p>
                        <p>Signature : ____________________________</p>
                  </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>
<style scoped>
.pdf-print-mode .p-6 {
    padding: 0.15cm !important;
}

.pdf-print-mode table {
    table-layout: fixed;
    width: 100%;
}

.pdf-print-mode th:nth-child(1),
.pdf-print-mode td:nth-child(1) {
    width: 12%;
}

.pdf-print-mode th:nth-child(2),
.pdf-print-mode td:nth-child(2) {
    width: 32%;
}

.pdf-print-mode th:nth-child(3),
.pdf-print-mode td:nth-child(3) {
    width: 8%;
}

.pdf-print-mode th:nth-child(4),
.pdf-print-mode td:nth-child(4) {
    width: 13%;
}

.pdf-print-mode th:nth-child(5),
.pdf-print-mode td:nth-child(5) {
    width: 17%;
}

.pdf-print-mode th:nth-child(6),
.pdf-print-mode td:nth-child(6) {
    width: 18%;
}
.pdf-print-mode {
    font-size: 11px;
}

.pdf-print-mode table {
    font-size: 11px;
}
.pdf-print-mode table th,
.pdf-print-mode table td {
    font-size: 11px !important;
    padding: 3px !important;
    line-height: 1.2 !important;
}
</style>