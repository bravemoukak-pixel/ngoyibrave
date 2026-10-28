<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

defineProps({
  professeursCollege: Array,
  professeursLycee: Array,
  professeursPrimaire: Array,
});

function supprimer(id) {
    if (confirm('Voulez-vous vraiment supprimer ce professeur ?')) {
        router.delete(route('professeurs.destroy', id));
    }
}

const sectionsAMasquer = ref([]);
const sectionImprimee = ref(null);
const toutesSections = ['college', 'lycee', 'primaire'];

async function imprimerSection(section) {
    sectionsAMasquer.value = toutesSections.filter(s => s !== section);
    sectionImprimee.value = section;
    document.body.classList.add('pdf-print-mode');
    await nextTick();
    window.print();
    sectionsAMasquer.value = [];
    sectionImprimee.value = null;
    document.body.classList.remove('pdf-print-mode');
}
async function imprimerTout() {
    sectionsAMasquer.value = [];
    sectionImprimee.value = 'tout';
    document.body.classList.add('pdf-print-mode');
    await nextTick();
    window.print();
    sectionsAMasquer.value = [];
    sectionImprimee.value = null;
    document.body.classList.remove('pdf-print-mode');
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
                Enseignants
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    
                    <div class="print-header justify-between" :class="{ 'cache-si-section-specifique': sectionImprimee && sectionImprimee !== 'tout' && sectionImprimee !== 'college' }">
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
                        <div class="flex justify-between items-center mb-6">
                     <h3 class="text-lg font-semibold text-gray-700" :class="{ 'no-print': sectionImprimee && sectionImprimee !== 'tout' && sectionImprimee !== 'college' }">Liste des enseignants</h3>

                        <div class="flex items-center gap-2">
                            <button
                                @click="imprimerSection('college')"
                                class="no-print bg-blue-700 hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded-md"
                            >
                                🖶 Imprimer Collège
                            </button>
                            <button
                                @click="imprimerSection('lycee')"
                                class="no-print bg-green-700 hover:bg-green-800 text-white font-semibold px-4 py-2 rounded-md"
                            >
                                🖶 Imprimer Lycée
                            </button>
                            <button
                                @click="imprimerSection('primaire')"
                                class="no-print bg-yellow-700 hover:bg-yellow-800 text-white font-semibold px-4 py-2 rounded-md"
                            >
                                🖶 Imprimer Primaire
                            </button>
                            <button
                                    @click="imprimerTout"
                                    class="no-print bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                                >
                                    🖶 Imprimer tout
                                </button>

                            <Link :href="route('professeurs.create')" class="no-print bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md">
                                + Ajouter un enseignant
                            </Link>
                        </div>
                    </div>

                    <!-- Collège -->
                    <div :class="{ 'no-print': sectionsAMasquer.includes('college') }">

                        <h4 class="text-md font-bold text-blue-700 mb-3">Collège</h4>
                        <div v-if="professeursCollege.length === 0" class="text-gray-500 mb-8">
                            Aucun professeur enregistré pour le collège.
                        </div>
                        <div v-if="professeursCollege.length === 0" class="text-gray-500 mb-8">
                            Aucun professeur enregistré pour le collège.
                        </div>
                        <table v-else class="w-full border-collapse border border-gray-400 mb-8">
                            <thead>
                                <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                    <th class="p-3 border border-gray-400 w-10">N°</th>
                                    <th class="p-3 border border-gray-400">Nom</th>
                                    <th class="p-3 border border-gray-400">Prénom</th>
                                    <th class="p-3 border border-gray-400">Sexe</th>
                                    <th class="p-3 border border-gray-400">Date de naissance</th>
                                    <th class="p-3 border border-gray-400">Lieu de naissance</th>
                                    <th class="p-3 border border-gray-400">Matière</th>
                                    <th class="p-3 border border-gray-400">Contact</th>
                                    <th class="p-3 border border-gray-400">Classes</th>
                                    <th class="p-3 border border-gray-400 no-print">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prof, index) in professeursCollege" :key="prof.id" class="hover:bg-gray-50">
                                    <td class="p-3 border border-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.nom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.prenom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.sexe }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.date_naissance }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.lieu_naissance }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.matiere }}</td>
                                    <td class="p-3 border border-gray-400">
                                        <div>{{ prof.telephone }}</div>
                                        <div class="text-gray-500 text-sm">{{ prof.email }}</div>
                                    </td>
                                    <td class="p-3 border border-gray-400">
                                        <span v-for="classe in prof.classes" :key="classe" class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2 py-1 rounded-full mr-1">
                                            {{ classe }}
                                        </span>
                                    </td>
                                    <td class="p-3 border border-gray-400 space-x-2 no-print">
                                        <Link :href="route('professeurs.edit', prof.id)" class="text-emerald-600 hover:underline text-sm">
                                            Modifier
                                        </Link>
                                        <Link :href="route('emplois-du-temps.professeur', prof.id)" class="text-indigo-600 hover:underline text-sm">
                                            Emploi du temps
                                        </Link>
                                        <button @click="supprimer(prof.id)" class="text-red-600 hover:underline text-sm">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    

                    <!-- Lycée -->
                        <div :class="{ 'no-print': sectionsAMasquer.includes('lycee') }">
                           <div class="print-header justify-between" :class="{ 'saut-page': sectionImprimee === 'tout' }">
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
                        <h3 class="text-lg font-semibold text-gray-700">Liste des enseignants</h3>
                        <h4 class="text-md font-bold text-green-700 mb-3">Lycée</h4>
                        <div v-if="professeursLycee.length === 0" class="text-gray-500">
                            Aucun professeur enregistré pour le lycée.
                        </div>
                        <table v-else class="w-full border-collapse border border-gray-400">
                            <thead>
                                <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                   <th class="p-3 border border-gray-400 w-10">N°</th>
                                <th class="p-3 border border-gray-400">Nom</th>
                                    <th class="p-3 border border-gray-400">Prénom</th>
                                    <th class="p-3 border border-gray-400">Sexe</th>
                                    <th class="p-3 border border-gray-400">Date de naissance</th>
                                    <th class="p-3 border border-gray-400">Lieu de naissance</th>
                                    <th class="p-3 border border-gray-400">Matière</th>
                                    <th class="p-3 border border-gray-400">Contact</th>
                                    <th class="p-3 border border-gray-400">Classes</th>
                                    <th class="p-3 border border-gray-400 no-print">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prof, index) in professeursLycee" :key="prof.id" class="hover:bg-gray-50">
                                    <td class="p-3 border border-gray-400 text-center">{{ index + 1 }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.nom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.prenom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.sexe }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.date_naissance }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.lieu_naissance }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.matiere }}</td>
                                    <td class="p-3 border border-gray-400">
                                        <div>{{ prof.telephone }}</div>
                                        <div class="text-gray-500 text-sm">{{ prof.email }}</div>
                                    </td>
                                    <td class="p-3 border border-gray-400">
                                        <span v-for="classe in prof.classes" :key="classe" class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2 py-1 rounded-full mr-1">
                                            {{ classe }}
                                        </span>
                                    </td>
                                 <td class="p-3 border border-gray-400 space-x-2 no-print">
                                    <Link :href="route('professeurs.edit', prof.id)" class="text-emerald-600 hover:underline text-sm">
                                        Modifier
                                    </Link>
                                    <Link :href="route('emplois-du-temps.professeur', prof.id)" class="text-indigo-600 hover:underline text-sm">
                                        Emploi du temps
                                    </Link>
                                    <button @click="supprimer(prof.id)" class="text-red-600 hover:underline text-sm">
                                        Supprimer
                                    </button>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Primaire -->
                    <div :class="{ 'no-print': sectionsAMasquer.includes('primaire') }" class="mt-8">
                        <div class="print-header justify-between" :class="{ 'saut-page': sectionImprimee === 'tout' }">
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
                       <h3 class="text-lg font-semibold text-gray-700">Liste des enseignants</h3>
                         <h4 class="text-md font-bold text-green-700 mb-3">Primaire</h4>
                        <div v-if="professeursPrimaire.length === 0" class="text-gray-500">
                            Aucun enseignant enregistré pour le primaire.
                        </div>
                        <table v-else class="w-full border-collapse border border-gray-400">
                            <thead>
                                <tr class="bg-gray-100 text-left text-sm text-gray-600">
                                   <th class="p-3 border border-gray-400 w-10">N°</th>
                                    <th class="p-3 border border-gray-400">Nom</th>
                                    <th class="p-3 border border-gray-400">Prénom</th>
                                    <th class="p-3 border border-gray-400">Sexe</th>
                                    <th class="p-3 border border-gray-400">Date de naissance</th>
                                    <th class="p-3 border border-gray-400">Lieu de naissance</th>
                                    <th class="p-3 border border-gray-400">Matière</th>
                                    <th class="p-3 border border-gray-400">Contact</th>
                                    <th class="p-3 border border-gray-400">Classes</th>
                                    <th class="p-3 border border-gray-400 no-print">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prof, index) in professeursPrimaire" :key="prof.id" class="hover:bg-gray-50">
                                    <td class="p-3 border border-gray-400 text-center">{{ index + 1 }}</td> 
                                    <td class="p-3 border border-gray-400">{{ prof.nom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.prenom }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.sexe }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.date_naissance }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.lieu_naissance }}</td>
                                    <td class="p-3 border border-gray-400">{{ prof.matiere }}</td>
                                    <td class="p-3 border border-gray-400">
                                        <div>{{ prof.telephone }}</div>
                                        <div class="text-gray-500 text-sm">{{ prof.email }}</div>
                                    </td>
                                    <td class="p-3 border border-gray-400">
                                        <span v-for="classe in prof.classes" :key="classe" class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2 py-1 rounded-full mr-1">
                                            {{ classe }}
                                        </span>
                                    </td>
                                    <td class="p-3 border border-gray-400 space-x-2 no-print">
                                        <Link :href="route('professeurs.edit', prof.id)" class="text-emerald-600 hover:underline text-sm">
                                            Modifier
                                        </Link>
                                        <Link :href="route('emplois-du-temps.professeur', prof.id)" class="text-indigo-600 hover:underline text-sm">
                                            Emploi du temps
                                        </Link>
                                        <button @click="supprimer(prof.id)" class="text-red-600 hover:underline text-sm">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
<style scoped>
.pdf-print-mode .p-6 {
    padding-top: 0.3cm !important;
    padding-bottom: 0.3cm !important;
    padding-left: 0.15cm !important;
    padding-right: 0.15cm !important;
}

.pdf-print-mode table {
    font-size: 10px;
}

.pdf-print-mode table th,
.pdf-print-mode table td {
    font-size: 10px !important;
    padding: 3px !important;
    line-height: 1.2 !important;
}

@media print {
    .saut-page {
        page-break-before: always;
        break-before: page;
    }
}
@media print {
    .cache-si-section-specifique {
        display: none !important;
    }
}
</style>