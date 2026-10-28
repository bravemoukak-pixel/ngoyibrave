<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  classe: Object,
  matiere: String,
  trimestre: Number,
  anneeScolaire: String,
  matieres: Array,
  types: Array,
  donnees: Array,
  estPrimaire: Boolean, // nouveau prop
});

const libelles = props.estPrimaire
    ? { 'Devoir 1': 'Comp. 1', 'Devoir 2': 'Comp. 2', 'Composition': 'Composition' }
    : { 'Devoir 1': 'Devoir 1', 'Devoir 2': 'Devoir 2', 'Composition': 'Composition' };

const matiereSelectionnee = ref(props.matiere);
const trimestreSelectionne = ref(props.trimestre);

const notesLocales = ref(
    props.donnees.map(eleve => ({
        eleve_id: eleve.eleve_id,
        nom: eleve.nom,
        prenom: eleve.prenom,
        notes: { ...eleve.notes },
    }))
);

function changerFiltre() {
    router.get(route('notes.saisie', props.classe.id), {
        matiere: matiereSelectionnee.value,
        trimestre: trimestreSelectionne.value,
        annee_scolaire: props.anneeScolaire,
    });
}

function enregistrer() {
    const notesAEnvoyer = [];

    notesLocales.value.forEach(eleve => {
        props.types.forEach(type => {
            notesAEnvoyer.push({
                eleve_id: eleve.eleve_id,
                type_evaluation: type,
                note: eleve.notes[type],
            });
        });
    });

    router.post(route('notes.enregistrer'), {
        matiere: matiereSelectionnee.value,
        trimestre: trimestreSelectionne.value,
        annee_scolaire: props.anneeScolaire,
        notes: notesAEnvoyer,
    }, {
        preserveScroll: true,
        onSuccess: () => alert('Notes enregistrées avec succès !'),
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Saisie des notes — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap gap-4 items-end mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
                            <select v-model="matiereSelectionnee" @change="changerFiltre" class="rounded-md border-gray-300 shadow-sm">
                                <option v-for="m in matieres" :key="m" :value="m">{{ m }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trimestre</label>
                            <select v-model="trimestreSelectionne" @change="changerFiltre" class="rounded-md border-gray-300 shadow-sm">
                                <option :value="1">Trimestre 1</option>
                                <option :value="2">Trimestre 2</option>
                                <option :value="3">Trimestre 3</option>
                            </select>
                        </div>
                    </div>

                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                <th class="p-3">Nom</th>
                                <th class="p-3">Prénom</th>
                                <th v-for="type in types" :key="type" class="p-3 text-center">{{ libelles[type] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="eleve in notesLocales" :key="eleve.eleve_id" class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ eleve.nom }}</td>
                                <td class="p-3">{{ eleve.prenom }}</td>
                                <td v-for="type in types" :key="type" class="p-3 text-center">
                                    <input
                                        type="number"
                                        min="0"
                                        max="20"
                                        step="0.25"
                                        v-model="eleve.notes[type]"
                                        class="w-20 text-center rounded-md border-gray-300 shadow-sm"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <button
                            @click="enregistrer"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md"
                        >
                            Enregistrer les notes
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>