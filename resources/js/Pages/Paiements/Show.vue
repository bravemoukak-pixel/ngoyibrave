<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    parent: Object,
    paiements: Array,
    totalDu: Number,
    totalPaye: Number,
    reste: Number,
});

const form = useForm({
    parent_eleve_id: props.parent.id,
    mois: '',
    montant: '',
});

function ajouterPaiement() {
    form.post(route('paiements.ajouter'), {
        preserveScroll: true,
        onSuccess: () => form.reset('mois', 'montant'),
    });
}

function supprimerPaiement(id) {
    if (confirm('Supprimer ce paiement ?')) {
        router.delete(route('paiements.supprimer', id), { preserveScroll: true });
    }
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

function formatMois(mois) {
    return new Date(mois).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Paiements — {{ parent.nom }} {{ parent.prenom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="grid grid-cols-2 gap-2 text-sm mb-6">
                        <p><span class="font-semibold">Nom :</span> {{ parent.nom }} {{ parent.prenom }}</p>
                        <p><span class="font-semibold">Téléphone :</span> {{ parent.telephone }}</p>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-2">Élève(s) rattaché(s)</h4>
                        <table class="w-full border-collapse border border-gray-400 text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="p-2 border border-gray-400">Élève</th>
                                    <th class="p-2 border border-gray-400 text-right">Montant annuel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(eleve, i) in parent.eleves" :key="i">
                                    <td class="p-2 border border-gray-400">{{ eleve.nom }}</td>
                                    <td class="p-2 border border-gray-400 text-right">{{ eleve.montant_annuel ? formatMontant(eleve.montant_annuel) : '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <table class="w-full border-collapse border border-gray-400 text-sm mb-6">
                        <tbody>
                            <tr>
                                <td class="p-2 border border-gray-400">Total dû (année)</td>
                                <td class="p-2 border border-gray-400 text-right">{{ formatMontant(totalDu) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 border border-gray-400">Total payé</td>
                                <td class="p-2 border border-gray-400 text-right text-emerald-700">{{ formatMontant(totalPaye) }}</td>
                            </tr>
                            <tr class="font-bold" :class="reste > 0 ? 'bg-red-50' : 'bg-emerald-50'">
                                <td class="p-2 border border-gray-400">Reste à payer</td>
                                <td class="p-2 border border-gray-400 text-right" :class="reste > 0 ? 'text-red-600' : 'text-emerald-700'">
                                    {{ formatMontant(reste) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Historique des paiements</h4>

                        <div v-if="paiements.length === 0" class="text-gray-500 mb-4">
                            Aucun paiement enregistré pour le moment.
                        </div>

                        <table v-else class="w-full border-collapse border border-gray-400 text-sm mb-4">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="p-2 border border-gray-400">Mois</th>
                                    <th class="p-2 border border-gray-400 text-right">Montant</th>
                                    <th class="p-2 border border-gray-400"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="paiement in paiements" :key="paiement.id">
                                    <td class="p-2 border border-gray-400 capitalize">{{ formatMois(paiement.mois) }}</td>
                                    <td class="p-2 border border-gray-400 text-right">{{ formatMontant(paiement.montant) }}</td>
                                    <td class="p-2 border border-gray-400 text-right space-x-2">
                                    <Link :href="route('paiements.recu', paiement.id)" class="text-emerald-600 hover:underline text-sm">
                                        Reçu
                                    </Link>
                                    <button @click="supprimerPaiement(paiement.id)" class="text-red-600 hover:underline text-sm">
                                        Supprimer
                                    </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <form @submit.prevent="ajouterPaiement" class="flex flex-wrap items-end gap-3">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Mois</label>
                                <input v-model="form.mois" type="month" required class="rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Montant</label>
                                <input v-model="form.montant" type="number" min="0" step="0.01" required class="rounded-md border-gray-300 w-32" />
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md"
                            >
                                Enregistrer le paiement
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>