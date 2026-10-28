<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    classe: Object,
    classeSuivante: Object,
    estTerminale: Boolean,
    necessiteChoixFiliere: Boolean,
    classesLycee: Array,
    eleves: Array,
    anneeActuelle: String,
    anneeSuivante: String,
});

const filieresChoisies = ref({});
const decisionsManuelles = ref({});

props.eleves.forEach(e => {
    if (e.passe && props.necessiteChoixFiliere) {
        filieresChoisies.value[e.id] = '';
    }
    if (e.moyenneManquante) {
        decisionsManuelles.value[e.id] = 'redouble';
    }
});

function elevePasse(eleve) {
    if (eleve.moyenneManquante) {
        return decisionsManuelles.value[eleve.id] === 'passe';
    }
    return eleve.passe;
}

function classeProposeeLabel(eleve) {
    const passe = elevePasse(eleve);
    if (!passe) {
        return `Redouble (reste en ${props.classe.nom})`;
    }
    if (props.estTerminale) {
        return 'Diplômé — Sortie';
    }
    if (props.necessiteChoixFiliere) {
        return null;
    }
    return props.classeSuivante ? props.classeSuivante.nom : 'Aucune classe suivante configurée';
}

function valider() {
    if (props.necessiteChoixFiliere) {
        const manquants = props.eleves.filter(e => elevePasse(e) && !filieresChoisies.value[e.id]);
        if (manquants.length > 0) {
            alert('Choisis une filière pour tous les élèves qui passent avant de valider.');
            return;
        }
    }

    if (!props.estTerminale && !props.necessiteChoixFiliere && !props.classeSuivante) {
        alert("Aucune classe suivante n'est configurée pour cette classe. Configure-la avant de valider.");
        return;
    }

    if (!confirm(`Confirmer le passage de classe pour ${props.classe.nom} (année ${props.anneeSuivante}) ? Cette action est définitive.`)) {
        return;
    }

    const decisions = props.eleves.map(eleve => {
        const passe = elevePasse(eleve);
        if (!passe) {
            return { eleve_id: eleve.id, classe_id: props.classe.id, diplome: false };
        }
        if (props.estTerminale) {
            return { eleve_id: eleve.id, classe_id: null, diplome: true };
        }
        if (props.necessiteChoixFiliere) {
            return { eleve_id: eleve.id, classe_id: filieresChoisies.value[eleve.id], diplome: false };
        }
        return { eleve_id: eleve.id, classe_id: props.classeSuivante.id, diplome: false };
    });

    router.post(route('passage-classe.valider', props.classe.id), { decisions }, {
        onSuccess: () => alert('Passage de classe validé avec succès.'),
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Passage de classe — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <p class="text-sm text-gray-600 mb-6">
                        Année en cours : <strong>{{ anneeActuelle }}</strong> → Année suivante : <strong>{{ anneeSuivante }}</strong>
                    </p>

                    <table class="w-full border-collapse border border-gray-400 text-sm mb-6">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600">
                                <th class="p-2 border border-gray-400">Nom</th>
                                <th class="p-2 border border-gray-400">Prénom</th>
                                <th class="p-2 border border-gray-400 text-center">Moyenne annuelle</th>
                                <th class="p-2 border border-gray-400 text-center">Décision</th>
                                <th class="p-2 border border-gray-400">Classe / Filière proposée</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="eleve in eleves" :key="eleve.id">
                                <td class="p-2 border border-gray-400 font-medium">{{ eleve.nom }}</td>
                                <td class="p-2 border border-gray-400">{{ eleve.prenom }}</td>
                                <td class="p-2 border border-gray-400 text-center">{{ eleve.moyenneAnnuelle ?? '—' }}</td>
                               <td class="p-2 border border-gray-400 text-center">
                               <div v-if="eleve.moyenneManquante">
                                        <p class="text-orange-600 font-semibold text-xs mb-1">⚠️ Notes manquantes</p>
                                        <select v-model="decisionsManuelles[eleve.id]" class="rounded-md border-gray-300 text-xs">
                                            <option value="redouble">Redouble</option>
                                            <option value="passe">Passe quand même</option>
                                        </select>
                                    </div>
                                    <span v-else :class="eleve.passe ? 'text-emerald-700 font-semibold' : 'text-red-600 font-semibold'">
                                        {{ eleve.passe ? 'PASSE' : 'REDOUBLE' }}
                                    </span>
                                </td>
                                <td class="p-2 border border-gray-400">
                                    <select
                                        v-if="elevePasse(eleve) && necessiteChoixFiliere"
                                        v-model="filieresChoisies[eleve.id]"
                                        class="rounded-md border-gray-300 text-sm"
                                    >
                                        <option value="">-- Choisir la filière --</option>
                                        <option v-for="c in classesLycee" :key="c.id" :value="c.id">{{ c.nom }}</option>
                                    </select>
                                    <span v-else>{{ classeProposeeLabel(eleve) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button
                        @click="valider"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-3 rounded-md"
                    >
                        Valider le passage de classe
                    </button>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>