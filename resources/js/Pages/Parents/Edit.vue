<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  parent: Object,
  eleves: Array,
});

const form = useForm({
  nom: props.parent.nom,
  prenom: props.parent.prenom,
  fonction: props.parent.fonction,
  lien_parente: props.parent.lien_parente,
  telephone: props.parent.telephone,
  eleves: props.parent.eleves,
  montant_annuel_override: props.parent.montant_annuel_override,
});

function submit() {
  form.put(route('parents.update', props.parent.id));
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier un parent
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input v-model="form.nom" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.nom" class="text-red-600 text-sm mt-1">{{ form.errors.nom }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input v-model="form.prenom" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.prenom" class="text-red-600 text-sm mt-1">{{ form.errors.prenom }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lien de parenté</label>
                            <select v-model="form.lien_parente" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Choisir --</option>
                                <option value="Père">Père</option>
                                <option value="Mère">Mère</option>
                                <option value="Tuteur">Tuteur</option>
                            </select>
                            <p v-if="form.errors.lien_parente" class="text-red-600 text-sm mt-1">{{ form.errors.lien_parente }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                            <input v-model="form.fonction" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.fonction" class="text-red-600 text-sm mt-1">{{ form.errors.fonction }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input v-model="form.telephone" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.telephone" class="text-red-600 text-sm mt-1">{{ form.errors.telephone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Élève(s) rattaché(s)</label>
                            <div class="space-y-1 max-h-48 overflow-y-auto border rounded-md p-3">
                                <label v-for="eleve in eleves" :key="eleve.id" class="flex items-center gap-2">
                                    <input type="checkbox" :value="eleve.id" v-model="form.eleves" />
                                    {{ eleve.nom }} {{ eleve.prenom }}
                                </label>
                            </div>
                            <p v-if="form.errors.eleves" class="text-red-600 text-sm mt-1">{{ form.errors.eleves }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md disabled:opacity-50"
                        >
                            Enregistrer les modifications
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>