<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue'; // ajoutez "computed" à l'import existant

const props = defineProps({
    classes: Array,
    classeId: [String, Number, null],
    classeNom: String,
    trimestre: Number,
    matieres: Array,
    coefficients: Object,
    eleves: Array,
    estPrimaire: Boolean, // nouveau prop
});

const classeSelectionnee = ref(props.classeId ?? '');
const trimestreSelectionne = ref(props.trimestre);

const devoirs = computed(() => props.estPrimaire
    ? [
        { titre: 'Comp. 1', cleNotes: 'notesD1', cleMoyenne: 'moyenneD1' },
        { titre: 'Comp. 2', cleNotes: 'notesD2', cleMoyenne: 'moyenneD2' },
        { titre: 'Composition', cleNotes: 'notesCompo', cleMoyenne: 'moyenneCompo' },
    ]
    : [
        { titre: 'Devoir 1', cleNotes: 'notesD1', cleMoyenne: 'moyenneD1' },
        { titre: 'Devoir 2', cleNotes: 'notesD2', cleMoyenne: 'moyenneD2' },
        { titre: 'Composition', cleNotes: 'notesCompo', cleMoyenne: 'moyenneCompo' },
    ]
);

function actualiser() {
    router.get(route('suivi-notes.classe'), {
        classe_id: classeSelectionnee.value,
        trimestre: trimestreSelectionne.value,
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
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Suivi des notes — Réunion parents
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
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
                        Sélectionnez une classe et un trimestre pour afficher la liste.
                    </div>

                    <div v-else>
                        

                       

                        <!-- ICI : un bloc par devoir, à la place de l'ancienne table unique -->
                        <!-- Remplacez tout le bloc "v-for devoir" par celui-ci -->
                            <div v-for="(devoir, index) in devoirs" :key="devoir.titre" class="mb-8" :class="{ 'print-page-break': index > 0 }">

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

                                <div class="text-center border-2 border-gray-800 rounded-md py-2 mb-4">
                                    <p class="font-bold uppercase">Suivi des notes — {{ classeNom }}</p>
                                    <p class="text-sm">Trimestre {{ trimestre }} — {{ devoir.titre }}</p>
                                </div>

                                <table class="w-full border-collapse border border-gray-400 text-xs mb-1">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="p-1 border border-gray-400 text-left">Nom</th>
                                            <th class="p-1 border border-gray-400 text-left">Prénom</th>
                                            <th v-for="matiere in matieres" :key="matiere" class="p-1 border border-gray-400">
                                                {{ abreger(matiere) }}
                                            </th>
                                            <th class="p-1 border border-gray-400">Moy.</th>
                                        </tr>
                                        <tr class="bg-emerald-50 font-semibold">
                                            <td class="p-1 border border-gray-400" colspan="2">Coefficient</td>
                                            <td v-for="matiere in matieres" :key="matiere" class="p-1 border border-gray-400 text-center">
                                                {{ coefficients[matiere] }}
                                            </td>
                                            <td class="p-1 border border-gray-400"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(eleve, i) in eleves" :key="i">
                                            <td class="p-1 border border-gray-400 font-medium">{{ eleve.nom }}</td>
                                            <td class="p-1 border border-gray-400">{{ eleve.prenom }}</td>
                                            <td v-for="matiere in matieres" :key="matiere" class="p-1 border border-gray-400 text-center">
                                                {{ eleve[devoir.cleNotes][matiere] ?? '—' }}
                                            </td>
                                            <td class="p-1 border border-gray-400 text-center font-semibold">
                                                {{ eleve[devoir.cleMoyenne] ?? '—' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <!-- FIN des 3 blocs -->

                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>