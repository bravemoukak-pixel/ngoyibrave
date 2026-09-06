<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  classe: Object,
  trimestre: Number,
  donnees: Array,
});

const trimestreSelectionne = ref(props.trimestre);

const lignesLocales = ref(
    props.donnees.map(e => ({ ...e }))
);

function changerTrimestre() {
    router.get(route('assiduites.saisie', props.classe.id), { trimestre: trimestreSelectionne.value });
}

function enregistrer() {
    router.post(route('assiduites.enregistrer'), {
        trimestre: trimestreSelectionne.value,
        lignes: lignesLocales.value,
    }, {
        preserveScroll: true,
        onSuccess: () => alert('Assiduité enregistrée avec succès !'),
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Assiduité — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trimestre</label>
                        <select v-model="trimestreSelectionne" @change="changerTrimestre" class="rounded-md border-gray-300 shadow-sm">
                            <option :value="1">Trimestre 1</option>
                            <option :value="2">Trimestre 2</option>
                            <option :value="3">Trimestre 3</option>
                        </select>
                    </div>

                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600">
                                <th class="p-2">Nom</th>
                                <th class="p-2">Prénom</th>
                                <th class="p-2 text-center">Abs. justifiées</th>
                                <th class="p-2 text-center">Abs. injustifiées</th>
                                <th class="p-2 text-center">Retards justifiés</th>
                                <th class="p-2 text-center">Retards injustifiés</th>
                                <th class="p-2">Avertissements</th>
                                <th class="p-2">Blâme</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ligne in lignesLocales" :key="ligne.eleve_id" class="border-b">
                                <td class="p-2">{{ ligne.nom }}</td>
                                <td class="p-2">{{ ligne.prenom }}</td>
                                <td class="p-2 text-center">
                                    <input type="number" min="0" v-model.number="ligne.absences_justifiees" class="w-16 text-center rounded-md border-gray-300 shadow-sm" />
                                </td>
                                <td class="p-2 text-center">
                                    <input type="number" min="0" v-model.number="ligne.absences_injustifiees" class="w-16 text-center rounded-md border-gray-300 shadow-sm" />
                                </td>
                                <td class="p-2 text-center">
                                    <input type="number" min="0" v-model.number="ligne.retards_justifies" class="w-16 text-center rounded-md border-gray-300 shadow-sm" />
                                </td>
                                <td class="p-2 text-center">
                                    <input type="number" min="0" v-model.number="ligne.retards_injustifies" class="w-16 text-center rounded-md border-gray-300 shadow-sm" />
                                </td>
                                <td class="p-2">
                                    <input type="text" v-model="ligne.avertissements" class="w-32 rounded-md border-gray-300 shadow-sm text-xs" />
                                </td>
                                <td class="p-2">
                                    <input type="text" v-model="ligne.blame" class="w-32 rounded-md border-gray-300 shadow-sm text-xs" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <button
                            @click="enregistrer"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md"
                        >
                            Enregistrer
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>