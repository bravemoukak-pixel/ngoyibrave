<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    eleves: Array,
    eleveId: [String, Number, null],
    trimestre: Number,
    suivi: Object,
    estPrimaire: Boolean, // nouveau prop
});

const libelles = props.estPrimaire
    ? { 'Devoir 1': 'Comp. 1', 'Devoir 2': 'Comp. 2', 'Composition': 'Composition' }
    : { 'Devoir 1': 'Devoir 1', 'Devoir 2': 'Devoir 2', 'Composition': 'Composition' };

const eleveSelectionne = ref(props.eleveId ?? '');
const trimestreSelectionne = ref(props.trimestre);

function actualiser() {
    router.get(route('suivi-notes.index'), {
        eleve_id: eleveSelectionne.value,
        trimestre: trimestreSelectionne.value,
    }, { preserveState: true });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Suivi détaillé des notes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Élève</label>
                            <select v-model="eleveSelectionne" @change="actualiser" class="rounded-md border-gray-300">
                                <option value="" disabled>Sélectionnez un élève</option>
                                <option v-for="eleve in eleves" :key="eleve.id" :value="eleve.id">
                                    {{ eleve.nom }} {{ eleve.prenom }}
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

                    <div v-if="!suivi" class="text-gray-500">
                        Sélectionnez un élève et un trimestre pour afficher le détail des notes.
                    </div>

                    <div v-else>
                        <div class="mb-4 text-sm">
                            <p><span class="font-semibold">Élève :</span> {{ suivi.eleve.nom }} {{ suivi.eleve.prenom }}</p>
                            <p><span class="font-semibold">Classe :</span> {{ suivi.eleve.classe }}</p>
                        </div>

                        <table class="w-full border-collapse border border-gray-400 text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="p-2 border border-gray-400">Matière</th>
                                    <th class="p-2 border border-gray-400 text-center">{{ libelles['Devoir 1'] }}</th>
                                    <th class="p-2 border border-gray-400 text-center">{{ libelles['Devoir 2'] }}</th>
                                    <th class="p-2 border border-gray-400 text-center">Composition</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ligne in suivi.lignes" :key="ligne.matiere">
                                    <td class="p-2 border border-gray-400 font-medium">{{ ligne.matiere }}</td>
                                    <td class="p-2 border border-gray-400 text-center">{{ ligne.d1 ?? '—' }}</td>
                                    <td class="p-2 border border-gray-400 text-center">{{ ligne.d2 ?? '—' }}</td>
                                    <td class="p-2 border border-gray-400 text-center">{{ ligne.composition ?? '—' }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-emerald-50 font-bold">
                                    <td class="p-2 border border-gray-400">Moyenne</td>
                                    <td class="p-2 border border-gray-400 text-center">{{ suivi.moyenneD1 ?? '—' }}</td>
                                    <td class="p-2 border border-gray-400 text-center">{{ suivi.moyenneD2 ?? '—' }}</td>
                                    <td class="p-2 border border-gray-400 text-center">{{ suivi.moyenneCompo ?? '—' }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>