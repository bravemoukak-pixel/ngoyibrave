<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
  parents: Array,
});

function supprimer(id) {
    if (confirm('Voulez-vous vraiment supprimer ce parent ?')) {
        router.delete(route('parents.destroy', id));
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
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Parents
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex justify-between items-center mb-6 no-print">
                        <h3 class="text-lg font-semibold text-gray-700">Liste des parents</h3>
                        <div class="flex items-center gap-2">
                            <button
                                @click="imprimer"
                                class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                            >
                                🖶 Imprimer
                            </button>
                            <Link :href="route('parents.create')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md">
                                + Ajouter un parent
                            </Link>
                        </div>
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

                    <div v-if="parents.length === 0" class="text-gray-500">
                        Aucun parent enregistré pour le moment.
                    </div>

                   <table v-else class="w-full border-collapse border border-gray-400">
                        <thead>
                            <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                <th class="p-3 border border-gray-400">Nom</th>
                                <th class="p-3 border border-gray-400">Prénom</th>
                                <th class="p-3 border border-gray-400">Lien</th>
                                <th class="p-3 border border-gray-400">Fonction</th>
                                <th class="p-3 border border-gray-400">Téléphone</th>
                                <th class="p-3 border border-gray-400">Élève(s)</th>
                                <th class="p-3 border border-gray-400 no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in parents" :key="p.id" class="hover:bg-gray-50">
                                <td class="p-3 border border-gray-400">{{ p.nom }}</td>
                                <td class="p-3 border border-gray-400">{{ p.prenom }}</td>
                                <td class="p-3 border border-gray-400">{{ p.lien_parente }}</td>
                                <td class="p-3 border border-gray-400">{{ p.fonction ?? '—' }}</td>
                                <td class="p-3 border border-gray-400">{{ p.telephone }}</td>
                                <td class="p-3 border border-gray-400">
                                    <span v-for="eleve in p.eleves" :key="eleve" class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full mr-1">
                                        {{ eleve }}
                                    </span>
                                </td>
                                <td class="p-3 border border-gray-400 space-x-2 no-print">
                                    <Link :href="route('parents.edit', p.id)" class="text-emerald-600 hover:underline text-sm">
                                        Modifier
                                    </Link>
                                    <button @click="supprimer(p.id)" class="text-red-600 hover:underline text-sm">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>