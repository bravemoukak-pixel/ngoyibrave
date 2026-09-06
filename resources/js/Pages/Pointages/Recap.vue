<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    professeurs: Array,
    periode: String,
    date: String,
});

const periodeSelectionnee = ref(props.periode);
const dateSelectionnee = ref(props.date);

function actualiser() {
    router.get(route('pointages.recap'), {
        periode: periodeSelectionnee.value,
        date: dateSelectionnee.value,
    }, { preserveState: true });
}

function imprimer() {
    window.print();
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

const totalGeneral = props.professeurs.reduce((s, p) => s + p.montant, 0);

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
                Récapitulatif des heures et montants
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6 no-print">
                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                                <select v-model="periodeSelectionnee" @change="actualiser" class="rounded-md border-gray-300">
                                    <option value="jour">Jour</option>
                                    <option value="semaine">Semaine</option>
                                    <option value="mois">Mois</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de référence</label>
                                <input
                                    v-model="dateSelectionnee"
                                    type="date"
                                    @change="actualiser"
                                    class="rounded-md border-gray-300"
                                />
                            </div>
                        </div>

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

                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600">
                                <th class="p-3">Professeur</th>
                                <th class="p-3">Niveau</th>
                                <th class="p-3">Heures effectuées</th>
                                <th class="p-3">Taux horaire</th>
                                <th class="p-3">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="prof in professeurs" :key="prof.id" class="border-b hover:bg-gray-50">
                                <td class="p-3 font-medium">{{ prof.nom }} {{ prof.prenom }}</td>
                                <td class="p-3">
                                    <span :class="prof.niveau === 'Lycée' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'" class="text-xs font-semibold px-2 py-1 rounded-full">
                                        {{ prof.niveau }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">{{ prof.heures }} h</td>
                                <td class="p-3 text-center">{{ formatMontant(prof.taux) }}</td>
                                <td class="p-3 text-right font-semibold">{{ formatMontant(prof.montant) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-50">
                                <td class="p-3" colspan="4">Total général</td>
                                <td class="p-3 text-right text-emerald-700">{{ formatMontant(totalGeneral) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>