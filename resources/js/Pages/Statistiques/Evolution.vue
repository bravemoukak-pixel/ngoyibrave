<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Évolution des résultats par classe
            </h2>
        </template>

        <div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="flex justify-end no-print">
            <button
                @click="imprimer"
                class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
            >
                🖶 Imprimer
            </button>
        </div>

        <div class="print-header justify-between flex">
            <div class="flex items-center gap-3">
                <img src="/images/logo.png" alt="Logo" class="w-12 h-12" />
                <div class="text-sm">
                    <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                    <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-700">Année scolaire {{ anneeScolaire }}</p>
        </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Taux de réussite — Collège</h3>
                    <Line :data="dataCollege" :options="options" />
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Taux de réussite — Lycée</h3>
                    <Line :data="dataLycee" :options="options" />
                </div>

            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, LinearScale, CategoryScale);

const props = defineProps({
    anneeScolaire: String,
    college: Array,
    lycee: Array,
});

const couleurs = ['#059669', '#2563eb', '#d97706', '#dc2626', '#7c3aed', '#0891b2', '#db2777'];

function construireData(series) {
    return {
        labels: ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'],
        datasets: series.map((s, i) => ({
            label: s.nom,
            data: s.valeurs,
            borderColor: couleurs[i % couleurs.length],
            backgroundColor: couleurs[i % couleurs.length],
            tension: 0.3,
        })),
    };
}

const dataCollege = computed(() => construireData(props.college));
const dataLycee = computed(() => construireData(props.lycee));

const options = {
    responsive: true,
    scales: {
        y: {
            min: 0,
            max: 100,
            ticks: { callback: (v) => v + '%' },
        },
    },
};
function imprimer() {
    document.body.classList.add('pdf-print-mode');

    function nettoyer() {
        document.body.classList.remove('pdf-print-mode');
        window.onafterprint = null;
    }

    window.onafterprint = nettoyer;
    window.print();
    setTimeout(nettoyer, 2000);
}
</script>
<style scoped>
.pdf-print-mode .p-6 {
    padding: 0.3cm !important;
}

canvas {
    background-color: white !important;
}

@media print {
    .bg-white.shadow {
        box-shadow: none !important;
        border: 1px solid #ddd;
        page-break-inside: avoid;
    }
}
</style>