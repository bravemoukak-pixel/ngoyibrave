<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    classes: Array,
    annees: Array,
    classeId: [String, Number, null],
    anneeScolaire: String,
    classeNom: String,
    eleves: Array,
});

const classeSelectionnee = ref(props.classeId ?? '');
const anneeSelectionnee = ref(props.anneeScolaire ?? '');

function rechercher() {
    router.get(route('historique-classes.index'), {
        classe_id: classeSelectionnee.value,
        annee_scolaire: anneeSelectionnee.value,
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
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historique des classes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6 no-print">
                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                                <select v-model="classeSelectionnee" @change="rechercher" class="rounded-md border-gray-300">
                                    <option value="" disabled>Sélectionnez une classe</option>
                                    <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                                        {{ classe.nom }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Année scolaire</label>
                                <select v-model="anneeSelectionnee" @change="rechercher" class="rounded-md border-gray-300">
                                    <option value="" disabled>Sélectionnez une année</option>
                                    <option v-for="annee in annees" :key="annee" :value="annee">
                                        {{ annee }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <button
                            v-if="eleves"
                            @click="imprimer"
                            class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer
                        </button>
                    </div>

                    <div v-if="!eleves" class="text-gray-500">
                        Sélectionnez une classe et une année scolaire pour afficher la liste.
                    </div>

                    <div v-else>
                        <div class="text-center border-2 border-gray-800 rounded-md py-2 mb-4">
                            <p class="font-bold uppercase">{{ classeNom }} — Année scolaire {{ anneeScolaire }}</p>
                        </div>

                        <div v-if="eleves.length === 0" class="text-gray-500">
                            Aucun élève trouvé pour cette classe cette année-là.
                        </div>

                        <table v-else class="w-full border-collapse border border-gray-400 text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-left text-gray-600">
                                    <th class="p-2 border border-gray-400">Matricule</th>
                                    <th class="p-2 border border-gray-400">Nom</th>
                                    <th class="p-2 border border-gray-400">Prénom</th>
                                    <th class="p-2 border border-gray-400">Sexe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="eleve in eleves" :key="eleve.id">
                                    <td class="p-2 border border-gray-400">{{ eleve.matricule ?? '—' }}</td>
                                    <td class="p-2 border border-gray-400 font-medium">{{ eleve.nom }}</td>
                                    <td class="p-2 border border-gray-400">{{ eleve.prenom }}</td>
                                    <td class="p-2 border border-gray-400">{{ eleve.sexe }}</td>
                                </tr>
                            </tbody>
                        </table>
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
    table-layout: fixed;
    width: 100%;
    font-size: 12px;
}

.pdf-print-mode th:nth-child(1),
.pdf-print-mode td:nth-child(1) {
    width: 16%;
}

.pdf-print-mode th:nth-child(2),
.pdf-print-mode td:nth-child(2) {
    width: 35%;
}

.pdf-print-mode th:nth-child(3),
.pdf-print-mode td:nth-child(3) {
    width: 35%;
}

.pdf-print-mode th:nth-child(4),
.pdf-print-mode td:nth-child(4) {
    width: 10%;
}

.pdf-print-mode table th,
.pdf-print-mode table td {
    font-size: 12px !important;
    padding: 3px !important;
    line-height: 1.2 !important;
}
</style>