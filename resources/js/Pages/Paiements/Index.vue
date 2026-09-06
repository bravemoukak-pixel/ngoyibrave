<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    parents: Array,
});

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Suivi des paiements
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <h3 class="text-lg font-semibold text-gray-700 mb-6">Paiements par parent</h3>

                    <div v-if="parents.length === 0" class="text-gray-500">
                        Aucun parent enregistré pour le moment.
                    </div>

                    <table v-else class="w-full border-collapse border border-gray-400 text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600">
                                <th class="p-3 border border-gray-400">Parent</th>
                                <th class="p-3 border border-gray-400">Téléphone</th>
                                <th class="p-3 border border-gray-400">Élève(s)</th>
                                <th class="p-3 border border-gray-400 text-right">Total dû</th>
                                <th class="p-3 border border-gray-400 text-right">Total payé</th>
                                <th class="p-3 border border-gray-400 text-right">Reste à payer</th>
                                <th class="p-3 border border-gray-400"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in parents" :key="p.id" class="hover:bg-gray-50">
                                <td class="p-3 border border-gray-400 font-medium">{{ p.nom }} {{ p.prenom }}</td>
                                <td class="p-3 border border-gray-400">{{ p.telephone }}</td>
                                <td class="p-3 border border-gray-400">
                                    <span v-for="eleve in p.eleves" :key="eleve" class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full mr-1">
                                        {{ eleve }}
                                    </span>
                                </td>
                                <td class="p-3 border border-gray-400 text-right">{{ formatMontant(p.totalDu) }}</td>
                                <td class="p-3 border border-gray-400 text-right text-emerald-700">{{ formatMontant(p.totalPaye) }}</td>
                                <td class="p-3 border border-gray-400 text-right font-semibold" :class="p.reste > 0 ? 'text-red-600' : 'text-emerald-700'">
                                    {{ formatMontant(p.reste) }}
                                </td>
                                <td class="p-3 border border-gray-400">
                                    <Link :href="route('paiements.show', p.id)" class="text-emerald-600 hover:underline text-sm">
                                        Détails
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>