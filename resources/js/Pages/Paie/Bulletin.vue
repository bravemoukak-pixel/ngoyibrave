<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    professeurs: Array,
    professeurId: [String, Number, null],
    mois: String,
    bulletin: Object,
});

const professeurSelectionne = ref(props.professeurId ?? '');
const moisSelectionne = ref(props.mois);

function actualiser() {
    router.get(route('paie.index'), {
        professeur_id: professeurSelectionne.value,
        mois: moisSelectionne.value,
    }, { preserveState: true });
}

function imprimer() {
    window.print();
}

const formLigne = useForm({
    professeur_id: '',
    mois: '',
    type: 'prime',
    libelle: '',
    montant: '',
});

function ajouterLigne() {
    formLigne.professeur_id = professeurSelectionne.value;
    formLigne.mois = moisSelectionne.value;
    formLigne.post(route('paie.lignes.ajouter'), {
        preserveScroll: true,
        onSuccess: () => {
            formLigne.reset('libelle', 'montant');
            actualiser();
        },
    });
}

function supprimerLigne(id) {
    if (confirm('Supprimer cette ligne ?')) {
        router.delete(route('paie.lignes.supprimer', id), {
            preserveScroll: true,
            onSuccess: () => actualiser(),
        });
    }
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
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
                Bulletin de paie
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6 no-print">
                        <div class="flex flex-wrap items-end gap-4">
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                                <input
                                    v-model="moisSelectionne"
                                    type="month"
                                    @change="actualiser"
                                    class="rounded-md border-gray-300"
                                />
                            </div>
                        </div>

                        <button
                            v-if="bulletin"
                            @click="imprimer"
                            class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer
                        </button>
                    </div>

                    <div v-if="!bulletin" class="text-gray-500">
                        Sélectionnez un professeur et un mois pour générer le bulletin de paie.
                    </div>

                    <div v-else>

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
                            <p class="font-bold uppercase">Bulletin de paie</p>
                            <p class="text-sm capitalize">{{ bulletin.mois }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm mb-6">
                            <p><span class="font-semibold">Nom :</span> {{ bulletin.professeur.nom }}</p>
                            <p><span class="font-semibold">Niveau :</span> {{ bulletin.professeur.niveau }}</p>
                            <p><span class="font-semibold">Prénom :</span> {{ bulletin.professeur.prenom }}</p>
                            <p><span class="font-semibold">Matière :</span> {{ bulletin.professeur.matiere }}</p>
                        </div>

                        <table class="w-full border-collapse border border-gray-400 text-sm mb-6">
                            <tbody>
                                <tr class="border-b border-gray-400">
                                    <td class="p-2 border border-gray-400 font-medium">Heures de cours effectuées</td>
                                    <td class="p-2 border border-gray-400 text-right">{{ bulletin.heures_cours }} h</td>
                                </tr>
                                <tr v-if="bulletin.heures_td > 0" class="border-b border-gray-400">
                                    <td class="p-2 border border-gray-400 font-medium">Heures de TD</td>
                                    <td class="p-2 border border-gray-400 text-right">{{ bulletin.heures_td }} h</td>
                                </tr>
                                <tr class="border-b border-gray-400">
                                    <td class="p-2 border border-gray-400 font-semibold">Total heures</td>
                                    <td class="p-2 border border-gray-400 text-right font-semibold">{{ bulletin.heures }} h</td>
                                </tr>
                                <tr class="border-b border-gray-400">
                                    <td class="p-2 border border-gray-400 font-medium">Taux horaire</td>
                                    <td class="p-2 border border-gray-400 text-right">{{ formatMontant(bulletin.taux) }}</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="p-2 border border-gray-400 font-semibold">Salaire brut</td>
                                    <td class="p-2 border border-gray-400 text-right font-semibold">{{ formatMontant(bulletin.salaireBrut) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-2">Primes et déductions</h4>

                            <table v-if="bulletin.lignes.length > 0" class="w-full border-collapse border border-gray-400 text-sm mb-3">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="p-2 border border-gray-400">Type</th>
                                        <th class="p-2 border border-gray-400">Libellé</th>
                                        <th class="p-2 border border-gray-400 text-right">Montant</th>
                                        <th class="p-2 border border-gray-400 no-print"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="ligne in bulletin.lignes" :key="ligne.id">
                                        <td class="p-2 border border-gray-400">
                                            <span :class="ligne.type === 'prime' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'" class="text-xs font-semibold px-2 py-1 rounded-full">
                                                {{ ligne.type === 'prime' ? 'Prime' : 'Déduction' }}
                                            </span>
                                        </td>
                                        <td class="p-2 border border-gray-400">{{ ligne.libelle }}</td>
                                        <td class="p-2 border border-gray-400 text-right">{{ formatMontant(ligne.montant) }}</td>
                                        <td class="p-2 border border-gray-400 no-print text-right">
                                            <button @click="supprimerLigne(ligne.id)" class="text-red-600 hover:underline text-sm">
                                                Supprimer
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <form @submit.prevent="ajouterLigne" class="flex flex-wrap items-end gap-3 no-print">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Type</label>
                                    <select v-model="formLigne.type" class="rounded-md border-gray-300">
                                        <option value="prime">Prime</option>
                                        <option value="deduction">Déduction</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Libellé</label>
                                    <input v-model="formLigne.libelle" type="text" required class="rounded-md border-gray-300" placeholder="Ex: CNSS, Avance..." />
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Montant</label>
                                    <input v-model="formLigne.montant" type="number" min="0" step="0.01" required class="rounded-md border-gray-300 w-32" />
                                </div>
                                <button
                                    type="submit"
                                    :disabled="formLigne.processing"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md"
                                >
                                    Ajouter
                                </button>
                            </form>
                        </div>

                        <table class="w-full border-collapse border border-gray-400 text-sm mb-8">
                            <tbody>
                                <tr>
                                    <td class="p-2 border border-gray-400">Total primes</td>
                                    <td class="p-2 border border-gray-400 text-right text-emerald-700">+ {{ formatMontant(bulletin.totalPrimes) }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2 border border-gray-400">Total déductions</td>
                                    <td class="p-2 border border-gray-400 text-right text-red-700">− {{ formatMontant(bulletin.totalDeductions) }}</td>
                                </tr>
                                <tr class="bg-emerald-50 font-bold">
                                    <td class="p-2 border border-gray-400">Net à payer</td>
                                    <td class="p-2 border border-gray-400 text-right text-emerald-700">{{ formatMontant(bulletin.netAPayer) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t">
                            <div>
                                <p class="font-semibold mb-8">Signature de l'employeur</p>
                            </div>
                            <div>
                                <p class="font-semibold mb-8">Signature de l'employé</p>
                            </div>
                        </div>

                            <p class="text-right text-xs mt-4">Fait à Pointe-Noire le {{ new Date().toLocaleDateString('fr-FR') }}</p>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>
