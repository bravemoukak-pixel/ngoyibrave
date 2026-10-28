<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  niveaux: {
    type: Array,
    required: true,
  },
});

const couleurClasses = {
  blue: {
    bg: 'bg-blue-50',
    border: 'border-blue-200',
    text: 'text-blue-700',
    badge: 'bg-blue-600',
    hover: 'hover:border-blue-400 hover:shadow-blue-100',
  },
  green: {
    bg: 'bg-green-50',
    border: 'border-green-200',
    text: 'text-green-700',
    badge: 'bg-green-600',
    hover: 'hover:border-green-400 hover:shadow-green-100',
  },
  purple: {
    bg: 'bg-purple-50',
    border: 'border-purple-200',
    text: 'text-purple-700',
    badge: 'bg-purple-600',
    hover: 'hover:border-purple-400 hover:shadow-purple-100',
  },
  red: {
    bg: 'bg-red-50',
    border: 'border-red-200',
    text: 'text-red-700',
    badge: 'bg-red-600',
    hover: 'hover:border-red-400 hover:shadow-red-100',
  },
};

function allerVersEleves(niveauId) {
  router.visit(route('niveaux.eleves', niveauId));
}
</script>

<template>
    <AdminLayout title="Niveaux">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Niveaux scolaires
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-gray-900">Primaire-Collège-Lycée</h1>
                    <p class="text-gray-500 mt-1">Cliquez sur un niveau pour voir la liste des élèves</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div
                        v-for="niveau in niveaux"
                        :key="niveau.id"
                        @click="allerVersEleves(niveau.id)"
                        :class="[
                            'rounded-2xl border-2 p-6 shadow-sm transition-all duration-200 cursor-pointer',
                            couleurClasses[niveau.couleur].bg,
                            couleurClasses[niveau.couleur].border,
                            couleurClasses[niveau.couleur].hover,
                            'hover:-translate-y-1 hover:shadow-lg',
                        ]"
                    >
                        <div class="flex items-center gap-3 mb-4">
                            <span class="text-4xl">{{ niveau.icone }}</span>
                            <h2 :class="['text-xl font-bold', couleurClasses[niveau.couleur].text]">
                                {{ niveau.titre }}
                            </h2>
                        </div>

                      <div class="flex flex-wrap gap-2">
                            <div
                                v-for="classe in niveau.classes"
                                :key="classe.id"
                                class="flex items-center gap-1"
                            >
                                <Link
                                    :href="route('classes.eleves', classe.id)"
                                    @click.stop
                                    :class="[
                                        'text-white text-sm font-semibold px-3 py-1.5 rounded-full transition-transform hover:scale-105',
                                        couleurClasses[niveau.couleur].badge,
                                    ]"
                                >
                                    {{ classe.nom }}
                                </Link>
                                <Link
                                    :href="route('notes.saisie', classe.id)"
                                    @click.stop
                                    class="text-xs bg-white/70 hover:bg-white text-gray-700 px-2 py-1.5 rounded-full transition-transform hover:scale-105"
                                    title="Saisir les notes"
                                >
                                    📝
                                </Link>
                               <Link
    :href="route('emplois-du-temps.classe', classe.id)"
    @click.stop
    class="text-xs bg-white/70 hover:bg-white text-gray-700 px-2 py-1.5 rounded-full transition-transform hover:scale-105"
    title="Emploi du temps"
>
                🗓️
                            </Link>
                           <Link
                            :href="route('passage-classe.index', classe.id)"
                            @click.stop
                            class="text-xs bg-white/70 hover:bg-white text-gray-700 px-2 py-1.5 rounded-full transition-transform hover:scale-105"
                            title="Passage de classe"
                        >
                            🎓
                        </Link>
                        <Link
                            :href="route('diplomes.index', classe.id)"
                            @click.stop
                            class="text-xs bg-white/70 hover:bg-white text-gray-700 px-2 py-1.5 rounded-full transition-transform hover:scale-105"
                            title="Diplômes"
                        >
                            🏆
                        </Link>
                        </div>
                        </div>

                        <!-- BOUTON D'INSCRIPTION -->
                        <div class="mt-4 pt-4 border-t border-white/60 text-sm text-gray-500 flex items-center justify-between">
                            <span>{{ niveau.classes.length }} classe{{ niveau.classes.length > 1 ? 's' : '' }}</span>
                            <Link
                                :href="route('eleves.create', niveau.id)"
                                @click.stop
                                class="text-white text-xs font-semibold px-3 py-1.5 rounded-full bg-gray-800 hover:bg-gray-900"
                            >
                                + Inscrire
                            </Link>
                        </div>
                        <!-- FIN BOUTON D'INSCRIPTION -->

                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>