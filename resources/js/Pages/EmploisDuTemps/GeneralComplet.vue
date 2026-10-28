<template>
    <AdminLayout>
        <div class="p-6">
            <div class="mb-8">
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div class="flex items-center gap-3">
                        <img src="/images/logo.png" alt="Logo" class="w-14 h-14" />
                        <div>
                            <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                            <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                            <p>Paix-Joie-Travail</p>
                            <p>Pointe-Noire</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">Année scolaire {{ anneeScolaire }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold text-gray-800">
                    Emploi du temps général — Collège et Lycée
                </h1>
                <button @click="imprimer" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 no-print">
                    Imprimer
                </button>
            </div>

            <h2 class="font-bold text-gray-700 mb-2">{{ college.titre }}</h2>
            <div class="overflow-x-auto mb-8">
                <TableauGeneral
                    :classes="college.classes"
                    :jours="college.jours"
                    :creneaux="college.creneaux"
                    :emplois="college.emplois"
                />
            </div>

            <h2 class="font-bold text-gray-700 mb-2">{{ lycee.titre }}</h2>
            <div class="overflow-x-auto">
                <TableauGeneral
                    :classes="lycee.classes"
                    :jours="lycee.jours"
                    :creneaux="lycee.creneaux"
                    :emplois="lycee.emplois"
                />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TableauGeneral from './TableauGeneral.vue';

defineProps({
    college: Object,
    lycee: Object,
    anneeScolaire: String,
});

function imprimer() {
    document.body.classList.add('pdf-print-mode');

    const style = document.createElement('style');
    style.id = 'print-landscape-temp';
    style.textContent = '@page { size: A3 landscape; margin: 0.4cm; }';
    document.head.appendChild(style);

    function nettoyer() {
        document.getElementById('print-landscape-temp')?.remove();
        document.body.classList.remove('pdf-print-mode');
        window.onafterprint = null;
    }

    window.onafterprint = nettoyer;
    window.print();
    setTimeout(nettoyer, 2000);
}
</script>

<style scoped>
.pdf-print-mode .no-print {
    display: none !important;
}

.pdf-print-mode .p-6 {
    padding: 0.15cm !important;
}

.pdf-print-mode .mb-8 {
    margin-bottom: 0.2rem !important;
}

.pdf-print-mode .mb-6 {
    margin-bottom: 0.15rem !important;
}

.pdf-print-mode img {
    width: 28px !important;
    height: 28px !important;
}

.pdf-print-mode .text-sm {
    font-size: 9px !important;
}

.pdf-print-mode h1 {
    font-size: 12px !important;
    margin: 0 !important;
}

.pdf-print-mode h2 {
    font-size: 10px !important;
}

.pdf-print-mode .overflow-x-auto {
    overflow: visible !important;
}

.pdf-print-mode table {
    font-size: 7px !important;
    table-layout: fixed !important;
    width: 100% !important;
}

.pdf-print-mode table th,
.pdf-print-mode table td {
    padding: 0.5px !important;
    line-height: 1 !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: clip !important;
}

@media print {
    .no-print {
        display: none !important;
    }

    table {
        page-break-inside: avoid;
    }

    tr {
        page-break-inside: avoid;
    }
}
</style>