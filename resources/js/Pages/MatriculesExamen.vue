<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  classe: Object,
  eleves: Array,
});

const matriculesLocaux = ref(
    props.eleves.map(eleve => ({
        eleve_id: eleve.id,
        nom: eleve.nom,
        prenom: eleve.prenom,
        matricule_examen: eleve.matricule_examen,
    }))
);

function enregistrer() {
    router.post(route('matricules-examen.enregistrer'), {
        matricules: matriculesLocaux.value.map(e => ({
            eleve_id: e.eleve_id,
            matricule_examen: e.matricule_examen,
        })),
    }, {
        preserveScroll: true,
        onSuccess: () => alert('Matricules enregistrés avec succès !'),
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Matricules d'examen — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <p class="text-sm text-gray-600 mb-4">
                        Saisissez le matricule attribué par la Direction des Examens et Concours pour chaque candidat.
                    </p>

                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                <th class="p-3">Nom</th>
                                <th class="p-3">Prénom</th>
                                <th class="p-3">Matricule d'examen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="eleve in matriculesLocaux" :key="eleve.eleve_id" class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ eleve.nom }}</td>
                                <td class="p-3">{{ eleve.prenom }}</td>
                                <td class="p-3">
                                    <input
                                        type="text"
                                        v-model="eleve.matricule_examen"
                                        class="w-48 rounded-md border-gray-300 shadow-sm"
                                        placeholder="Ex: 012345"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <button
                            @click="enregistrer"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md"
                        >
                            Enregistrer les matricules
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>