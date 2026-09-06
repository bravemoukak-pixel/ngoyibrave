<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>
    <AdminLayout>
        <template #header>
           <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tableau de bord
           </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div v-if="$page.props.flash?.error" class="bg-red-100 text-red-800 text-sm font-semibold rounded-md p-3 mb-4">
                {{ $page.props.flash.error }}
            </div>
            </div>  

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label="Total élèves" :value="stats.totalEleves" icon="👨‍🎓" :sousTexte="`dont ${stats.filles} filles`" />
                    <StatCard label="Élèves Collège" :value="stats.elevesCollege" icon="🏫" :sousTexte="`dont ${stats.fillesCollege} filles`" />
                    <StatCard label="Élèves Lycée" :value="stats.elevesLycee" icon="🎓" :sousTexte="`dont ${stats.fillesLycee} filles`" />
                    <StatCard label="Élèves Primaire" :value="stats.elevesPrimaire" icon="🏫" :sousTexte="`dont ${stats.fillesPrimaire} filles`" />
                    <StatCard label="Professeurs" :value="stats.totalProfesseurs" icon="👨‍🏫" />
                    <StatCard label="Professeurs Collège" :value="stats.professeursCollege" icon="🏫" />
                    <StatCard label="Professeurs Lycée" :value="stats.professeursLycee" icon="🎓" />
                    <StatCard label="Parents enregistrés" :value="stats.totalParents" icon="👪" />
                    <StatCard label="Classes" :value="stats.totalClasses" icon="🏫" />
                    <StatCard label="Niveaux" :value="stats.totalNiveaux" icon="🎓" />
                    <StatCard
                        v-for="classe in stats.effectifsParClasse.filter(c => c.niveau === 'Primaire')"
                        :key="classe.nom"
                        :label="classe.nom"
                        :value="classe.effectif"
                        icon="🏫"
                        :sousTexte="`dont ${classe.filles} filles`"
                    />
                    <StatCard label="Enseignants Primaire" :value="stats.professeursPrimaire" icon="👨‍🏫" />
             </div>

                <div class="bg-white shadow rounded-lg p-6 mt-4">
                          <h3 class="text-lg font-semibold text-gray-700 mb-4">Accès rapide</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <Link :href="route('niveaux.index')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                            <span class="text-3xl">🎓</span>
                            <span class="text-sm font-medium text-gray-700">Niveaux</span>
                        </Link>
                        <Link :href="route('professeurs.index')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                            <span class="text-3xl">👨‍🏫</span>
                            <span class="text-sm font-medium text-gray-700">Professeurs</span>
                        </Link>
                        <Link :href="route('parents.index')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                            <span class="text-3xl">👪</span>
                            <span class="text-sm font-medium text-gray-700">Parents</span>
                        </Link>
                        <Link :href="route('pointage.index')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                            <span class="text-3xl">📍</span>
                            <span class="text-sm font-medium text-gray-700">Pointage</span>
                        </Link>
                        <Link :href="route('pointages.recap')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                            <span class="text-3xl">⏱️</span>
                            <span class="text-sm font-medium text-gray-700">Suivi des heures</span>
                        </Link>
                        <Link :href="route('paie.index')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                            <span class="text-3xl">💰</span>
                            <span class="text-sm font-medium text-gray-700">Paie</span>
                        </Link>
                        <Link :href="route('admin.niveaux')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                            <span class="text-3xl">⚙️</span>
                            <span class="text-sm font-medium text-gray-700">Gérer les niveaux</span>
                        </Link>
                    </div>
                </div>
              
            </div>
        
    </AdminLayout>
</template>