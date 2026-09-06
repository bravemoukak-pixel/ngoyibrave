<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    classes: Array,
    classeId: [String, Number, null],
    classeNom: String,
    trimestre: Number,
    typeEvaluation: String,
    matieres: Array,
    coefficients: Object,
    eleves: Array,
    estPrimaire: Boolean, // nouveau prop
});

const libelles = props.estPrimaire
    ? { 'Devoir 1': 'Comp. 1', 'Devoir 2': 'Comp. 2', 'Composition': 'Composition' }
    : { 'Devoir 1': 'Devoir 1', 'Devoir 2': 'Devoir 2', 'Composition': 'Composition' };

const classeSelectionnee = ref(props.classeId ?? '');
const auto = calculerTrimestreEtType();
const trimestreSelectionne = ref(props.classeId ? props.trimestre : auto.trimestre);
const typeSelectionne = ref(props.classeId ? props.typeEvaluation : auto.type);

function actualiser() {
    router.get(route('suivi-notes.releve'), {
        classe_id: classeSelectionnee.value,
        trimestre: trimestreSelectionne.value,
        type_evaluation: typeSelectionne.value,
    }, { preserveState: true });
}

function imprimer() {
    window.print();
}

function abreger(matiere) {
    return matiere.length > 8 ? matiere.slice(0, 3) : matiere;
}

function anneeScolaire() {
    const maintenant = new Date();
    const mois = maintenant.getMonth() + 1;
    const annee = maintenant.getFullYear();

    if (mois >= 10) return `${annee}-${annee + 1}`;
    if (mois <= 6) return `${annee - 1}-${annee}`;
    return `${annee}-${annee + 1}`;
}
function calculerTrimestreEtType() {
    const mois = new Date().getMonth() + 1; // 1 = janvier, 12 = décembre

    const mapping = {
        10: { trimestre: 1, type: 'Devoir 1' },
        11: { trimestre: 1, type: 'Devoir 2' },
        12: { trimestre: 1, type: 'Composition' },
        1: { trimestre: 2, type: 'Devoir 1' },
        2: { trimestre: 2, type: 'Devoir 2' },
        3: { trimestre: 2, type: 'Composition' },
        4: { trimestre: 3, type: 'Devoir 1' },
        5: { trimestre: 3, type: 'Devoir 2' },
        6: { trimestre: 3, type: 'Composition' },
    };

    return mapping[mois] ?? { trimestre: 1, type: 'Devoir 1' };
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relevé de notes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6 no-print">
                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                                <select v-model="classeSelectionnee" @change="actualiser" class="rounded-md border-gray-300">
                                    <option value="" disabled>Sélectionnez une classe</option>
                                    <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                                        {{ classe.nom }}
                                    </option>
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
                                    <option value="Devoir 1">{{ libelles['Devoir 1'] }}</option>
                                    <option value="Devoir 2">{{ libelles['Devoir 2'] }}</option>
                                    <option value="Composition">{{ libelles['Composition'] }}</option>
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
                        Sélectionnez une classe, un trimestre et une évaluation pour afficher le relevé.
                    </div>

                    <div v-else>
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
                            <p><span class="font-semibold">Classe :</span> {{ classeNom }} — <span class="font-semibold">Effectif :</span> {{ eleves.length }}</p>
                            <div class="text-center border-2 border-gray-800 rounded-md px-4 py-1 font-bold uppercase">
                                    Relevé de notes — {{ libelles[typeEvaluation] }} (Trimestre {{ trimestre }})
                            </div>
                        </div>

                        <table class="w-full border-collapse border border-gray-400 text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="p-1 border border-gray-400">Matricule</th>
                                    <th class="p-1 border border-gray-400 text-left">Nom</th>
                                    <th class="p-1 border border-gray-400 text-left">Prénom</th>
                                    <th class="p-1 border border-gray-400">Sexe</th>
                                    <th v-for="matiere in matieres" :key="matiere" class="p-1 border border-gray-400">
                                        {{ abreger(matiere) }}
                                    </th>
                                    <th class="p-1 border border-gray-400">Total</th>
                                    <th class="p-1 border border-gray-400">Moy.</th>
                                    <th class="p-1 border border-gray-400">Rang</th>
                                </tr>
                                <tr class="bg-emerald-50 font-semibold">
                                    <td class="p-1 border border-gray-400" colspan="4">Coefficient</td>
                                    <td v-for="matiere in matieres" :key="matiere" class="p-1 border border-gray-400 text-center">
                                        {{ coefficients[matiere] }}
                                    </td>
                                    <td class="p-1 border border-gray-400" colspan="3"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="eleve in eleves" :key="eleve.matricule">
                                    <td class="p-1 border border-gray-400">{{ eleve.matricule ?? '—' }}</td>
                                    <td class="p-1 border border-gray-400">{{ eleve.nom }}</td>
                                    <td class="p-1 border border-gray-400">{{ eleve.prenom }}</td>
                                    <td class="p-1 border border-gray-400 text-center">{{ eleve.sexe }}</td>
                                    <td v-for="matiere in matieres" :key="matiere" class="p-1 border border-gray-400 text-center">
                                        {{ eleve.notes[matiere] ?? '—' }}
                                    </td>
                                    <td class="p-1 border border-gray-400 text-center font-semibold">{{ eleve.total }}</td>
                                    <td class="p-1 border border-gray-400 text-center font-semibold">{{ eleve.moyenne ?? '—' }}</td>
                                    <td class="p-1 border border-gray-400 text-center">{{ eleve.rang }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>