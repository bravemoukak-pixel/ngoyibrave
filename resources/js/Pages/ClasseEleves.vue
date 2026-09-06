<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
  classe: Object,
  eleves: Array,
});

function supprimer(id) {
    if (confirm('Voulez-vous vraiment supprimer cet élève ?')) {
        router.delete(route('eleves.destroy', id));
    }
}
function imprimer() {
    window.print();
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
    <AppLayout :title="classe.nom">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Élèves — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    
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

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-700">Liste des élèves</h3>

                    <div class="flex items-center gap-2">
                        <Link
                            :href="route('notes.saisie', classe.id)"
                            class="no-print bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            📝 Saisir les notes
                        </Link>
                        <Link
                            :href="route('assiduites.saisie', classe.id)"
                            class="no-print bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            📋 Assiduité
                        </Link>
                        <button
                            @click="imprimer"
                            class="no-print bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer
                        </button>
                    </div>
                </div>

                <div v-if="eleves.length === 0" class="text-gray-500">
                    Aucun élève inscrit pour cette classe pour le moment.
                </div>

                <table v-else class="w-full border-collapse border border-gray-400">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm text-gray-600">
                            <th class="p-3 border border-gray-400">Matricule</th>
                            <th class="p-3 border border-gray-400">Nom</th>
                            <th class="p-3 border border-gray-400">Prénom</th>
                            <th class="p-3 border border-gray-400">Sexe</th>
                            <th class="p-3 border border-gray-400">Date de naissance</th>
                            <th class="p-3 border border-gray-400">Lieu de naissance</th>
                            <th class="p-3 border border-gray-400">Adresse</th>
                            <th class="p-3 border border-gray-400 no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="eleve in eleves" :key="eleve.id" class="hover:bg-gray-50">
                            <td class="p-3 border border-gray-400">{{ eleve.matricule ?? '—' }}</td>
                            <td class="p-3 border border-gray-400">{{ eleve.nom }}</td>
                            <td class="p-3 border border-gray-400">{{ eleve.prenom }}</td>
                            <td class="p-3 border border-gray-400">{{ eleve.sexe }}</td>
                            <td class="p-3 border border-gray-400">{{ eleve.date_naissance }}</td>
                            <td class="p-3 border border-gray-400">{{ eleve.lieu_naissance }}</td>
                            <td class="p-3 border border-gray-400">{{ eleve.adresse }}</td>
                            <td class="p-3 border border-gray-400 space-x-2 no-print">
                                <Link :href="route('eleves.edit', eleve.id)" class="text-emerald-600 hover:underline text-sm">
                                    Modifier
                                </Link>
                                <Link :href="route('bulletin.show', eleve.id)" class="text-blue-600 hover:underline text-sm">
                                    Bulletin
                                </Link>
                                <button @click="supprimer(eleve.id)" class="text-red-600 hover:underline text-sm">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>