<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  niveau: Object,
  classes: Array,
});

const form = useForm({
  nom: '',
  prenom: '',
  date_naissance: '',
  lieu_naissance: '',
  sexe: '',
  adresse: '',
  classe_id: props.classes.length ? props.classes[0].id : '',
  montant_annuel: props.niveau.montant_annuel_defaut ?? '',
  photo: null,
});

function gererPhoto(event) {
    form.photo = event.target.files[0];
}

function submit() {
    form.post(route('eleves.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <AdminLayout :title="`Inscription — ${niveau.titre}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inscription d'un élève — {{ niveau.titre }}
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                            <input type="file" accept="image/*" @change="gererPhoto" class="w-full text-sm" />
                            <p v-if="form.errors.photo" class="text-red-600 text-sm mt-1">{{ form.errors.photo }}</p>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input v-model="form.adresse" type="text" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.adresse" class="text-red-600 text-sm mt-1">{{ form.errors.adresse }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                            <select v-model="form.classe_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                                    {{ classe.nom }}
                                </option>
                            </select>
                            <p v-if="form.errors.classe_id" class="text-red-600 text-sm mt-1">{{ form.errors.classe_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant annuel de scolarité (FCFA)</label>
                            <input v-model="form.montant_annuel" type="number" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="form.errors.montant_annuel" class="text-red-600 text-sm mt-1">{{ form.errors.montant_annuel }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-md disabled:opacity-50"
                        >
                            Inscrire l'élève
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>