<script setup>
import ParentLayout from '@/Layouts/ParentLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    enfants: Array,
});
</script>

<template>
    <ParentLayout>
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Mes enfants</h2>

            <div class="bg-white shadow rounded-lg p-6">
                <div v-if="enfants.length === 0" class="text-gray-500">
                    Aucun enfant rattaché à votre compte.
                </div>

                <div v-else class="space-y-4">
                    <div v-for="enfant in enfants" :key="enfant.id" class="border rounded-lg p-4">
                        <p class="font-semibold text-lg">{{ enfant.nom }} {{ enfant.prenom }}</p>
                        <p class="text-gray-600 text-sm mb-3">Classe : {{ enfant.classe }}</p>

                        <div class="flex gap-2">
                            <Link
                                v-for="t in [1, 2, 3]"
                                :key="t"
                                :href="route('espace-parent.bulletin', enfant.id) + '?trimestre=' + t"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-3 py-1.5 rounded-md"
                            >
                                Bulletin Trimestre {{ t }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ParentLayout>
</template>