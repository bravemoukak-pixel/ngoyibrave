<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    classes: Array,
    classeId: [String, Number, null],
    classeNom: String,
    mois: String,
    eleves: Array,
});

const classeSelectionnee = ref(props.classeId ?? '');
const moisSelectionne = ref(props.mois);

function actualiser() {
    router.get(route('suivi-paiements.index'), {
        classe_id: classeSelectionnee.value,
        mois: moisSelectionne.value,
    }, { preserveState: true });
}

function imprimer() {
    window.print();
}

const elevesAJour = computed(() => props.eleves?.filter(e => e.a_paye) ?? []);
const elevesNonAJour = computed(() => props.eleves?.filter(e => !e.a_paye) ?? []);

function moisEnLettres(moisStr) {
    if (!moisStr) return '';
    const [annee, mois] = moisStr.split('-');
    const noms = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    return `${noms[parseInt(mois) - 1]} ${annee}`;
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Suivi des paiements par classe
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                                <input type="month" v-model="moisSelectionne" @change="actualiser" class="rounded-md border-gray-300" />
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
                        Sélectionnez une classe et un mois pour afficher le suivi.
                    </div>

                    <div v-else>
                        <div class="text-center border-2 border-gray-800 rounded-md py-2 mb-6">
                            <p class="font-bold uppercase">Suivi des paiements — {{ classeNom }}</p>
                            <p class="text-sm">{{ moisEnLettres(mois) }}</p>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-md font-bold text-emerald-700 mb-3">
                                ✅ Ont payé ({{ elevesAJour.length }})
                            </h3>
                            <div v-if="elevesAJour.length === 0" class="text-gray-500 text-sm mb-4">
                                Aucun élève n'a encore payé pour ce mois.
                            </div>
                            <table v-else class="w-full border-collapse border border-gray-400 text-sm">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="p-2 border border-gray-400">Matricule</th>
                                        <th class="p-2 border border-gray-400">Nom</th>
                                        <th class="p-2 border border-gray-400">Prénom</th>
                                        <th class="p-2 border border-gray-400">Parent</th>
                                        <th class="p-2 border border-gray-400">Téléphone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="eleve in elevesAJour" :key="eleve.id">
                                        <td class="p-2 border border-gray-400">{{ eleve.matricule ?? '—' }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.nom }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.prenom }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.parent }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.telephone }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div>
                            <h3 class="text-md font-bold text-red-700 mb-3">
                                ❌ N'ont pas payé ({{ elevesNonAJour.length }})
                            </h3>
                            <div v-if="elevesNonAJour.length === 0" class="text-gray-500 text-sm">
                                Tous les élèves ont payé pour ce mois.
                            </div>
                            <table v-else class="w-full border-collapse border border-gray-400 text-sm">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="p-2 border border-gray-400">Matricule</th>
                                        <th class="p-2 border border-gray-400">Nom</th>
                                        <th class="p-2 border border-gray-400">Prénom</th>
                                        <th class="p-2 border border-gray-400">Parent</th>
                                        <th class="p-2 border border-gray-400">Téléphone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="eleve in elevesNonAJour" :key="eleve.id">
                                        <td class="p-2 border border-gray-400">{{ eleve.matricule ?? '—' }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.nom }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.prenom }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.parent }}</td>
                                        <td class="p-2 border border-gray-400">{{ eleve.telephone }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>