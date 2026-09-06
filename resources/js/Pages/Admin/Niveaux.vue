<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  niveaux: Array,
});

const couleurs = [
  { valeur: 'blue', classe: 'bg-blue-600' },
  { valeur: 'green', classe: 'bg-green-600' },
  { valeur: 'purple', classe: 'bg-purple-600' },
  { valeur: 'red', classe: 'bg-red-600' },
];

const formNiveau = useForm({
  titre: '',
  couleur: 'blue',
  icone: '🏫',
});

function ajouterNiveau() {
  formNiveau.post(route('admin.niveaux.store'), {
    preserveScroll: true,
    onSuccess: () => formNiveau.reset(),
  });
}

const classeOuverte = ref(null);

const formClasse = useForm({
  niveau_id: null,
  nom: '',
  code_matricule: '',
});
function ouvrirFormClasse(niveauId) {
  classeOuverte.value = classeOuverte.value === niveauId ? null : niveauId;
  formClasse.niveau_id = niveauId;
  formClasse.nom = '';
}

function ajouterClasse() {
  formClasse.post(route('admin.classes.store'), {
    preserveScroll: true,
    onSuccess: () => {
      formClasse.reset();
      classeOuverte.value = null;
    },
  });
}

function supprimerClasse(classeId) {
  if (confirm('Voulez-vous vraiment supprimer cette classe ? Les élèves qui y sont inscrits seront affectés.')) {
    formClasse.delete(route('admin.classes.destroy', classeId), { preserveScroll: true });
  }
}

const classeEnEdition = ref(null);

const formEditionClasse = useForm({
  nom: '',
  code_matricule: '',
});

function ouvrirEditionClasse(classe) {
  classeEnEdition.value = classe.id;
  formEditionClasse.nom = classe.nom;
  formEditionClasse.code_matricule = classe.code_matricule;
}

function enregistrerEditionClasse(classeId) {
  formEditionClasse.put(route('admin.classes.update', classeId), {
    preserveScroll: true,
    onSuccess: () => {
      classeEnEdition.value = null;
    },
  });
}

const formMontant = useForm({
  montant_annuel_defaut: null,
});

function modifierMontant(niveau) {
  formMontant.montant_annuel_defaut = niveau.montant_annuel_defaut;
  formMontant.put(route('admin.niveaux.montant', niveau.id), {
    preserveScroll: true,
  });
}

function supprimerNiveau(niveauId) {
  if (confirm('Voulez-vous vraiment supprimer ce niveau ? Toutes ses classes et les élèves qui y sont inscrits seront affectés.')) {
    router.delete(route('admin.niveaux.destroy', niveauId), { preserveScroll: true });
  }
}
</script>
<template>
    <AdminLayout title="Administration des niveaux">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestion des niveaux et classes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- Formulaire ajout niveau -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Ajouter un niveau</h3>

                    <form @submit.prevent="ajouterNiveau" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                            <input v-model="formNiveau.titre" type="text" placeholder="ex: Lycée - Série B" class="w-full rounded-md border-gray-300 shadow-sm" />
                            <p v-if="formNiveau.errors.titre" class="text-red-600 text-sm mt-1">{{ formNiveau.errors.titre }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icône</label>
                            <input v-model="formNiveau.icone" type="text" class="w-16 text-center rounded-md border-gray-300 shadow-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                            <div class="flex gap-2">
                                <button
                                    v-for="c in couleurs"
                                    :key="c.valeur"
                                    type="button"
                                    @click="formNiveau.couleur = c.valeur"
                                    :class="[
                                        'w-8 h-8 rounded-full transition-transform',
                                        c.classe,
                                        formNiveau.couleur === c.valeur ? 'ring-2 ring-offset-2 ring-gray-800 scale-110' : '',
                                    ]"
                                ></button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="formNiveau.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md disabled:opacity-50"
                        >
                            Ajouter
                        </button>
                    </form>
                </div>

                <!-- Liste des niveaux avec classes -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Niveaux existants</h3>

                    <div class="space-y-4">
                        <div v-for="niveau in niveaux" :key="niveau.id" class="border rounded-lg p-4">
                           <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ niveau.icone }}</span>
                                    <span class="font-semibold">{{ niveau.titre }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="ouvrirFormClasse(niveau.id)"
                                        class="text-sm bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md"
                                    >
                                        + Classe
                                    </button>
                                    <button
                                        @click="supprimerNiveau(niveau.id)"
                                        class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md"
                                    >
                                        Supprimer
                                    </button>
                                </div>
                            </div>

                          <div class="flex flex-wrap gap-2 mt-3">
                                <div v-for="classe in niveau.classes" :key="classe.id">
                                    <span
                                        v-if="classeEnEdition !== classe.id"
                                        class="flex items-center gap-1 bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full"
                                    >
                                        {{ classe.nom }}
                                        <button @click="ouvrirEditionClasse(classe)" class="text-blue-500 hover:text-blue-700 ml-1">✎</button>
                                        <button @click="supprimerClasse(classe.id)" class="text-red-500 hover:text-red-700 ml-1">✕</button>
                                    </span>

                                    <form
                                        v-else
                                        @submit.prevent="enregistrerEditionClasse(classe.id)"
                                        class="flex items-center gap-1"
                                    >
                                        <input
                                            v-model="formEditionClasse.nom"
                                            type="text"
                                            class="w-24 rounded-md border-gray-300 shadow-sm text-xs px-2 py-1"
                                        />
                                        <input
                                            v-model="formEditionClasse.code_matricule"
                                            type="text"
                                            placeholder="Code"
                                            maxlength="10"
                                            class="w-16 rounded-md border-gray-300 shadow-sm text-xs px-2 py-1"
                                        />
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold">✓</button>
                                        <button type="button" @click="classeEnEdition = null" class="text-gray-500 hover:text-gray-700 text-xs">✕</button>
                                    </form>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mt-3 pt-3 border-t">
                                <label class="text-sm text-gray-600">Montant annuel par défaut :</label>
                                <input
                                    :value="niveau.montant_annuel_defaut"
                                    @change="e => { niveau.montant_annuel_defaut = e.target.value; modifierMontant(niveau); }"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="Non défini"
                                    class="w-32 rounded-md border-gray-300 shadow-sm text-sm"
                                />
                                <span class="text-sm text-gray-500">FCFA</span>
                            </div>

                           <form
                                v-if="classeOuverte === niveau.id"
                                @submit.prevent="ajouterClasse"
                                class="flex gap-2 mt-3"
                            >
                                <input
                                    v-model="formClasse.nom"
                                    type="text"
                                    placeholder="ex: 6ᵉ B"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm text-sm"
                                />
                                <input
                                    v-model="formClasse.code_matricule"
                                    type="text"
                                    placeholder="ex: 6, PA, TD..."
                                    maxlength="10"
                                    class="w-24 rounded-md border-gray-300 shadow-sm text-sm"
                                />
                                <button
                                    type="submit"
                                    :disabled="formClasse.processing"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-1.5 rounded-md disabled:opacity-50"
                                >
                                    Valider
                                </button>
                            </form>
                            <p v-if="classeOuverte === niveau.id" class="text-xs text-gray-500 mt-1">
                                Le code matricule est court, sans "DP" ni année (ex : 6, 5, PA, PS, TD). Il est ajouté automatiquement lors de la génération du matricule de l'élève.
                            </p>
                            <p v-if="formClasse.errors.nom" class="text-red-600 text-sm mt-1">{{ formClasse.errors.nom }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>