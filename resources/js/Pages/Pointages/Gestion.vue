<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    professeurs: Array,
    professeurId: [String, Number, null],
    date: String,
    pointages: Array,
});

const professeurSelectionne = ref(props.professeurId ?? '');
const dateSelectionnee = ref(props.date);

function actualiser() {
    router.get(route('pointages.gestion'), {
        professeur_id: professeurSelectionne.value,
        date: dateSelectionnee.value,
    }, { preserveState: true });
}

const formAjout = useForm({
    professeur_id: '',
    type: 'arrivee',
    date: props.date,
    heure: '',
});

function ajouterPointage() {
    formAjout.professeur_id = professeurSelectionne.value;
    formAjout.date = dateSelectionnee.value;
    formAjout.post(route('pointages.ajouter'), {
        preserveScroll: true,
        onSuccess: () => {
            formAjout.reset('heure');
            actualiser();
        },
    });
}

function supprimerPointage(id) {
    if (confirm('Supprimer ce pointage ?')) {
        router.delete(route('pointages.supprimer', id), {
            preserveScroll: true,
            onSuccess: () => actualiser(),
        });
    }
}

function formatHeure(horodatage) {
    return new Date(horodatage).toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Africa/Brazzaville',
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Corriger un pointage
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Professeur</label>
                            <select v-model="professeurSelectionne" @change="actualiser" class="rounded-md border-gray-300">
                                <option value="" disabled>Sélectionnez un professeur</option>
                                <option v-for="prof in professeurs" :key="prof.id" :value="prof.id">
                                    {{ prof.nom }} {{ prof.prenom }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input
                                v-model="dateSelectionnee"
                                type="date"
                                @change="actualiser"
                                class="rounded-md border-gray-300"
                            />
                        </div>
                    </div>

                    <div v-if="professeurSelectionne">
                        <h3 class="font-semibold text-gray-700 mb-3">Pointages du jour</h3>

                        <div v-if="pointages.length === 0" class="text-gray-500 mb-4">
                            Aucun pointage enregistré pour ce professeur à cette date.
                        </div>

                        <table v-else class="w-full border-collapse text-sm mb-6">
                            <thead>
                                <tr class="bg-gray-100 text-left text-gray-600">
                                    <th class="p-2">Type</th>
                                    <th class="p-2">Heure</th>
                                    <th class="p-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in pointages" :key="p.id" class="border-b">
                                    <td class="p-2">
                                        <span :class="p.type === 'arrivee' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'" class="text-xs font-semibold px-2 py-1 rounded-full">
                                            {{ p.type === 'arrivee' ? 'Arrivée' : 'Départ' }}
                                        </span>
                                    </td>
                                    <td class="p-2">{{ formatHeure(p.horodatage) }}</td>
                                    <td class="p-2 text-right">
                                        <button @click="supprimerPointage(p.id)" class="text-red-600 hover:underline text-sm">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Ajouter un pointage manquant</h4>
                            <form @submit.prevent="ajouterPointage" class="flex flex-wrap items-end gap-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Type</label>
                                    <select v-model="formAjout.type" class="rounded-md border-gray-300">
                                        <option value="arrivee">Arrivée</option>
                                        <option value="depart">Départ</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Heure</label>
                                    <input v-model="formAjout.heure" type="time" required class="rounded-md border-gray-300" />
                                </div>
                                <button
                                    type="submit"
                                    :disabled="formAjout.processing"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md"
                                >
                                    Ajouter
                                </button>
                            </form>
                        </div>
                    </div>

                    <div v-else class="text-gray-500">
                        Sélectionnez un professeur pour voir et corriger ses pointages.
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>
