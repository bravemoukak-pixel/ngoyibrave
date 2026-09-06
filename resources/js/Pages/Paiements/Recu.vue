<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    paiement: Object,
    parent: Object,
    totalDu: Number,
    totalPaye: Number,
    reste: Number,
    telephonesEcole: Array,
});

function imprimer() {
    window.print();
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

function formatMois(mois) {
    return new Date(mois).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
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
                Reçu de paiement
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-8">

                    <div class="flex justify-end mb-4 no-print">
                        <button
                            @click="imprimer"
                            class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer
                        </button>
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

                    <div class="text-center border-2 border-gray-800 rounded-md py-2 mb-6">
                        <p class="font-bold uppercase">Reçu de paiement</p>
                        <p class="text-sm capitalize">{{ formatMois(paiement.mois) }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-sm mb-6">
                        <p><span class="font-semibold">Parent :</span> {{ parent.nom }} {{ parent.prenom }}</p>
                        <p><span class="font-semibold">Téléphone :</span> {{ parent.telephone }}</p>
                        <p class="col-span-2"><span class="font-semibold">Élève(s) :</span> {{ parent.eleves.join(', ') }}</p>
                    </div>

                    <table class="w-full border-collapse border border-gray-400 text-sm mb-8">
                        <tbody>
                            <tr>
                                <td class="p-2 border border-gray-400 font-medium">Mois concerné</td>
                                <td class="p-2 border border-gray-400 text-right capitalize">{{ formatMois(paiement.mois) }}</td>
                            </tr>
                            <tr class="bg-emerald-50 font-bold">
                                <td class="p-2 border border-gray-400">Montant payé</td>
                                <td class="p-2 border border-gray-400 text-right text-emerald-700">{{ formatMontant(paiement.montant) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="w-full border-collapse border border-gray-400 text-sm mb-8">
                        <tbody>
                            <tr>
                                <td class="p-2 border border-gray-400">Total dû (année)</td>
                                <td class="p-2 border border-gray-400 text-right">{{ formatMontant(totalDu) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 border border-gray-400">Total payé à ce jour</td>
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

                    <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t">
                        <div>
                            <p class="font-semibold mb-8">Signature du parent</p>
                        </div>
                        <div>
                            <p class="font-semibold mb-8">Signature de la caisse</p>
                        </div>
                    </div>
                    <p v-if="telephonesEcole.length > 0" class="text-center text-xs text-gray-500 mt-6">
                        En cas d'erreur sur ce reçu, contactez l'école : {{ telephonesEcole.join(' / ') }}
                    </p>
                    <p class="text-right text-xs mt-4">Fait à Pointe-Noire le {{ new Date().toLocaleDateString('fr-FR') }}</p>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>