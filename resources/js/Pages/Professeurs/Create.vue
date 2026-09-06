<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  classes: Array,
});

const form = useForm({
  nom: '',
  prenom: '',
  sexe: '',
  date_naissance: '',
  lieu_naissance: '',
  matiere: '',
  telephone: '',
  email: '',
  classes: [],
});

function submit() {
  form.post(route('professeurs.store'));
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ajouter un professeur
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                            <select v-model="form.sexe" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Choisir --</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                            <p v-if="form.errors.sexe" class="text-red-600 text-sm mt-1">{{ form.errors.sexe }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                            <input v-model="form.date_naissance" type="date" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.date_naissance" class="text-red-600 text-sm mt-1">{{ form.errors.date_naissance }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lieu de naissance</label>
                            <input v-model="form.lieu_naissance" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.lieu_naissance" class="text-red-600 text-sm mt-1">{{ form.errors.lieu_naissance }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Matière enseignée</label>
                            <input v-model="form.matiere" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.matiere" class="text-red-600 text-sm mt-1">{{ form.errors.matiere }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input v-model="form.telephone" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.telephone" class="text-red-600 text-sm mt-1">{{ form.errors.telephone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="form.email" type="email" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Classes assignées</label>
                            <div class="space-y-1">
                                <label v-for="classe in classes" :key="classe.id" class="flex items-center gap-2">
                                    <input type="checkbox" :value="classe.id" v-model="form.classes" />
                                    {{ classe.nom }}
                                </label>
                            </div>
                            <p v-if="form.errors.classes" class="text-red-600 text-sm mt-1">{{ form.errors.classes }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Matière enseignée</label>

                            <label class="flex items-center gap-2 mb-2 text-sm text-gray-600">
                                <input
                                    type="checkbox"
                                    :checked="form.matiere === 'Toutes les matières'"
                                    @change="form.matiere = $event.target.checked ? 'Toutes les matières' : ''"
                                />
                                Enseignant du primaire (toutes les matières)
                            </label>

                            <input
                                v-model="form.matiere"
                                type="text"
                                required
                                :disabled="form.matiere === 'Toutes les matières'"
                                class="w-full rounded-md border-gray-300 shadow-sm disabled:bg-gray-100"
                            />
                            <p v-if="form.errors.matiere" class="text-red-600 text-sm mt-1">{{ form.errors.matiere }}</p>
                        </div>
                     

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md disabled:opacity-50"
                        >
                            Ajouter le professeur
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>