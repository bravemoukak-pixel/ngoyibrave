<script setup>
import ParentLayout from '@/Layouts/ParentLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    eleve: Object,
    trimestre: Number,
    moisRequis: Array,
});

const nomsMois = {
    '01': 'Janvier', '02': 'Février', '03': 'Mars', '04': 'Avril',
    '05': 'Mai', '06': 'Juin', '07': 'Juillet', '08': 'Août',
    '09': 'Septembre', '10': 'Octobre', '11': 'Novembre', '12': 'Décembre',
};

function libelleMois(moisAnnee) {
    const [annee, mois] = moisAnnee.split('-');
    return nomsMois[mois] + ' ' + annee;
}
</script>

<template>
    <ParentLayout>
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <p class="text-5xl mb-4">🔒</p>
                <h2 class="text-xl font-bold text-gray-800 mb-2">
                    Bulletin non disponible
                </h2>
                <p class="text-gray-600 mb-4">
                    Le bulletin du Trimestre {{ trimestre }} de <strong>{{ eleve.prenom }} {{ eleve.nom }}</strong>
                    n'est pas encore accessible.
                </p>
                <p class="text-gray-600 mb-6">
                    Merci de régulariser le paiement de la scolarité pour le(s) mois suivant(s) :
                </p>
                <ul class="inline-block text-left mb-6">
                    <li v-for="mois in moisRequis" :key="mois" class="text-red-600 font-semibold">
                        • {{ libelleMois(mois) }}
                    </li>
                </ul>
                <p class="text-sm text-gray-500 mb-6">
                    Une fois le paiement effectué auprès de l'administration, le bulletin sera automatiquement accessible.
                </p>
                <Link :href="route('espace-parent.index')" class="text-emerald-700 font-semibold hover:underline">
                    ← Retour à mes enfants
                </Link>
            </div>
        </div>
    </ParentLayout>
</template>