<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ParentLayout from '@/Layouts/ParentLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const layoutComponent = computed(() => page.props.auth.user.role === 'parent' ? ParentLayout : AdminLayout);
const props = defineProps({
  eleve: Object,
  classe: Object,
  estPrimaire: Boolean,
  estPrimaire: Boolean, // nouveau prop
  estLycee: Boolean,
  trimestre: Number,
  anneeScolaire: String,
  lignes: Array,
  anneesDisponibles: Array,
  moyenneGenerale: [Number, null],
  moyenneAnnuelle: [Number, null],
  rangGeneral: String,
  mention: String,
  appreciationTravail: String,
  effectifClasse: Number,
  moyenneClasse: [Number, null],
  moyennePlusHaute: [Number, null],
  moyennePlusBasse: [Number, null],
  moyenneControleEleve: [Number, null],
  rangAnnuel: String,
  moyennePlusHauteAnnuelle: [Number, null],
  moyennePlusBasseAnnuelle: [Number, null],
  appreciationTravailAnnuelle: String,
  mentionAnnuelle: String, 
  assiduite: Object,
  dateSignature: String,
  urlArchive: String,
});
const totalMC = computed(() =>
    props.lignes.reduce((s, l) => s + (l.mc ?? 0), 0)
);
const totalCoef = computed(() =>
    props.lignes.reduce((s, l) => s + (l.coefficient ?? 0), 0)
);
const totalTotal = computed(() =>
    props.lignes.reduce((s, l) => s + (l.totalBrut ?? 0), 0)
);

function changerTrimestre(t) {
  router.get(route('bulletin.show', props.eleve.id), { trimestre: t });
}

function imprimer() {
  window.print();
}
const trimestreEnLettres = computed(() => {
    const noms = { 1: 'premier', 2: 'deuxième', 3: 'troisième' };
    return noms[props.trimestre] ?? props.trimestre;
});
const decisionAnnuelle = computed(() => {
    if (props.trimestre !== 3 || props.moyenneAnnuelle == null) return null;
    return props.moyenneAnnuelle >= 10 ? 'PASSE EN CLASSE SUPÉRIEURE' : 'REDOUBLE LA CLASSE';
});
function changerAnnee(annee) {
    router.get(route('bulletin.show', props.eleve.id), {
        trimestre: props.trimestre,
        annee_scolaire: annee,
    });
}
</script>
<template>
    <component :is="layoutComponent">
        <template #header>
            <h2 :class="page.props.auth.user.role === 'parent' ? 'font-semibold text-xl text-white leading-tight' : 'font-semibold text-xl text-gray-800 leading-tight'">
                Bulletin — {{ eleve.nom }} {{ eleve.prenom }}
            </h2>
        </template>

        <div class="py-12 bulletin-page">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-8">

                 <div class="flex justify-between items-center mb-6 no-print">
                        <div class="flex items-center gap-4">
                            <div class="flex gap-2">
                                <button
                                    v-for="t in [1, 2, 3]"
                                    :key="t"
                                    @click="changerTrimestre(t)"
                                    :class="[
                                        'px-4 py-2 rounded-md text-sm font-semibold',
                                        trimestre === t ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                    ]"
                                >
                                    Trimestre {{ t }}
                                </button>
                            </div>

                            <select
                                :value="anneeScolaire"
                                @change="changerAnnee($event.target.value)"
                                class="rounded-md border-gray-300 text-sm"
                            >
                                <option v-for="annee in anneesDisponibles" :key="annee" :value="annee">
                                    {{ annee }}
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <a :href="urlArchive"
                               target="_blank"
                               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-md">
                                📄 PDF archivé
                            </a>
                            <button @click="imprimer"
                                    class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md">
                                🖶 Imprimer
                            </button>
                        </div>
                    </div>

                    <div class="mb-8">
                       <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                            <div class="flex items-center gap-3">
                                <img src="/images/logo.png" alt="Logo" class="w-14 h-14" />
                                <div>
                                    <p class="font-bold">Complexe Scolaire Conventionné Catholique</p>
                                    <p class="font-bold text-emerald-700">LA DIVINE PROVIDENCE</p>
                                    <p>Paix-Joie-Travail</p>
                                    <p>Pointe-Noire</p>
                                </div>
                            </div>
                          <div class="text-center">
                            <p class="font-bold">République du Congo</p>
                            <p>Ministère de l'Enseignement, Préscolaire, Primaire,</p>
                            <p>Secondaire et de l'Alphabétisation</p>
                        </div>
                        </div>

                        <div class="text-center border-2 border-gray-800 rounded-md py-2 mb-6">
                           <p class="font-bold uppercase">Bulletin de notes du {{ trimestreEnLettres }} trimestre</p>
                            <p class="text-sm">Année scolaire {{ anneeScolaire }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <p><span class="font-semibold">Matricule :</span> {{ eleve.matricule ?? '—' }}</p>
                            <p><span class="font-semibold">Classe :</span> {{ classe.nom }}</p>
                            <p><span class="font-semibold">Nom :</span> {{ eleve.nom }}</p>
                            <p><span class="font-semibold">Sexe :</span> {{ eleve.sexe }}</p>
                            <p><span class="font-semibold">Prénom :</span> {{ eleve.prenom }}</p>
                            <p></p>
                            <p><span class="font-semibold">Date de naissance :</span> {{ eleve.sexe === 'F' ? 'Née' : 'Né' }} le {{ eleve.date_naissance ?? '—' }}</p>
                            <p></p>
                            <p><span class="font-semibold">Lieu de naissance :</span> {{ eleve.lieu_naissance ?? '—' }}</p>
                        </div>
                    </div>

                    <table class="w-full border-collapse text-xs border border-gray-400">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-600">
                            <th class="p-1.5 border border-gray-400">Matière</th>
                            <th class="p-1.5 text-center border border-gray-400">{{ estPrimaire ? 'Comp. 1' : 'D1' }}</th>
                            <th class="p-1.5 text-center border border-gray-400">{{ estPrimaire ? 'Comp. 2' : 'D2' }}</th>
                            <th v-if="!estPrimaire" class="p-1.5 text-center border border-gray-400">MC</th>
                            <th class="p-1.5 text-center border border-gray-400">{{ estPrimaire ? 'Comp.T' : 'Compo' }}</th>    
                            <th class="p-1.5 text-center border border-gray-400">Coef</th>
                            <th class="p-1.5 text-center border border-gray-400">Total</th>
                            <th class="p-1.5 text-center border border-gray-400">{{ estPrimaire ? 'Moy.' : 'Moy /20' }}</th>
                            <th class="p-1.5 text-center border border-gray-400">Rang</th>
                            <th class="p-1.5 border border-gray-400">Appréciation</th>
                            <th class="p-1.5 border border-gray-400">{{ estPrimaire ? 'Enseignant' : 'Professeur' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(ligne, index) in lignes" :key="ligne.matiere">
                            <td class="p-1.5 font-medium border border-gray-400">{{ ligne.matiere }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ ligne.d1 ?? '—' }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ ligne.d2 ?? '—' }}</td>
                            <td v-if="!estPrimaire" class="p-1.5 text-center border border-gray-400">{{ ligne.mc ?? '—' }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ ligne.composition ?? '—' }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ ligne.coefficient }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ ligne.totalBrut ?? '—' }}</td>
                            <td class="p-1.5 text-center font-semibold border border-gray-400">{{ ligne.moyenne ?? '—' }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ ligne.rang }}</td>
                            <td class="p-1.5 border border-gray-400">{{ ligne.appreciation }}</td>
                        <td
                            v-if="estPrimaire && index === 0"
                            :rowspan="lignes.length"
                            class="p-1.5 text-center text-gray-700 font-medium align-middle"
                        >
                            {{ lignes[0]?.professeur }}
                        </td>
                        <td v-else-if="!estPrimaire" class="p-1.5 text-gray-600 border border-gray-400">
                            {{ ligne.professeur }}
                        </td>
                        </tr>
                    </tbody>
                   <tfoot>
                        <tr v-if="estPrimaire" class="font-semibold bg-gray-50">
                            <td class="p-1.5 border border-gray-400" colspan="4">Total</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ totalCoef }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ totalTotal.toFixed(2) }}</td>
                            <td class="p-1.5 border border-gray-400" colspan="4"></td>
                        </tr>
                        <tr v-else class="font-semibold bg-gray-50">
                            <td class="p-1.5 border border-gray-400" colspan="3">Total</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ totalMC.toFixed(2) }}</td>
                            <td class="p-1.5 border border-gray-400" colspan="1"></td>
                            <td class="p-1.5 text-center border border-gray-400">{{ totalCoef }}</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ totalTotal.toFixed(2) }}</td>
                            <td class="p-1.5 border border-gray-400" colspan="3"></td>
                        </tr>

                        <tr v-if="estPrimaire" class="font-bold bg-gray-50">
                            <td class="p-1.5 border border-gray-400" colspan="6">Moyenne trimestrielle</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ moyenneGenerale ?? '—' }}</td>
                            <td class="p-1.5 border border-gray-400" colspan="3"></td>
                        </tr>
                        <tr v-else class="font-bold bg-gray-50">
                            <td class="p-1.5 border border-gray-400" colspan="7">Moyenne trimestrielle</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ moyenneGenerale ?? '—' }}</td>
                            <td class="p-1.5 border border-gray-400" colspan="3"></td>
                        </tr>

                        <tr v-if="trimestre === 3 && estPrimaire" class="font-bold bg-emerald-50">
                            <td class="p-1.5 border border-gray-400" colspan="6">Moyenne annuelle</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ moyenneAnnuelle ?? '—' }}</td>
                            <td class="p-1.5 border border-gray-400" colspan="3"></td>
                        </tr>
                        <tr v-if="trimestre === 3 && !estPrimaire" class="font-bold bg-emerald-50">
                            <td class="p-1.5 border border-gray-400" colspan="7">Moyenne annuelle</td>
                            <td class="p-1.5 text-center border border-gray-400">{{ moyenneAnnuelle ?? '—' }}</td>
                            <td class="p-1.5 border border-gray-400" colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
                    <div class="mt-8 text-sm">
   <div :class="trimestre === 3 ? 'grid grid-cols-3 gap-4' : 'grid grid-cols-2 gap-4'">
            <div class="border rounded-md p-3">
               <p class="font-bold mb-2">Résultat trimestriel</p>
                        <table class="w-full text-xs border-collapse border border-gray-400">
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Rang</td>
                                <td class="py-1 px-2 text-right font-semibold border border-gray-400">{{ rangGeneral ?? '—' }}</td>
                            </tr>
                            <tr v-if="!estPrimaire">
                                <td class="py-1 px-2 border border-gray-400">Moyenne de classe</td>
                                <td class="py-1 px-2 text-right font-semibold border border-gray-400">{{ moyenneControleEleve ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Moyenne la plus haute</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ moyennePlusHaute ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Moyenne la plus basse</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ moyennePlusBasse ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Effectif total de la classe</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ effectifClasse }}</td>
                            </tr>
                            <tr v-if="trimestre !== 3">
                                <td class="py-1 px-2 font-semibold border border-gray-400">Appréciation du travail</td>
                                <td class="py-1 px-2 text-right font-semibold border border-gray-400">{{ appreciationTravail }}</td>
                            </tr>
                        </table>
                       <p v-if="!estPrimaire" class="mt-2 text-center font-bold text-emerald-700 border-t pt-2">Mention : {{ mention }}</p>
                    </div>

                    <div class="border rounded-md p-3">
                        <p class="font-bold mb-2">Discipline</p>
                        <table class="w-full text-xs border-collapse border border-gray-400">
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Absences justifiées</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ assiduite.absences_justifiees }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Absences injustifiées</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ assiduite.absences_injustifiees }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Retards justifiés</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ assiduite.retards_justifies }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Retards injustifiés</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ assiduite.retards_injustifies }}</td>
                            </tr>
                        </table>
                        <p class="mt-2 text-xs"><span class="font-semibold">Avertissements :</span> {{ assiduite.avertissements }}</p>
                        <p class="text-xs"><span class="font-semibold">Blâme :</span> {{ assiduite.blame }}</p>
                    </div>
                    <div v-if="trimestre === 3" class="border rounded-md p-3 bg-emerald-50">
                        <p class="font-bold mb-2">Résultat annuel</p>
                        <table class="w-full text-xs border-collapse border border-gray-400">
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Rang</td>
                                <td class="py-1 px-2 text-right font-semibold border border-gray-400">{{ rangAnnuel ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Moyenne annuelle la plus haute</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ moyennePlusHauteAnnuelle ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Moyenne annuelle la plus basse</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ moyennePlusBasseAnnuelle ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 border border-gray-400">Effectif total de la classe</td>
                                <td class="py-1 px-2 text-right border border-gray-400">{{ effectifClasse }}</td>
                            </tr>
                        </table>
                      <p v-if="!estPrimaire" class="mt-2 text-center font-bold text-emerald-700 border-t pt-2">Mention annuelle : {{ mentionAnnuelle }}</p>
                    </div>
                 </div>
                    <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t">
                        <div>
                            <p class="font-semibold mb-8">Visa des parents ou tuteurs</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold mb-8">Visa de {{ estLycee ? 'la Proviseure' : 'la Directrice' }}</p>
                            <p v-if="decisionAnnuelle" class="text-center font-bold uppercase text-emerald-700 border border-gray-400 rounded-md py-2 mt-2">
                                {{ decisionAnnuelle }}
                            </p>
                        </div>
                    </div>

              <p class="text-right text-xs mt-4">Pointe-Noire le {{ dateSignature }}</p> 
            </div>

                </div>
            </div>
        </div>
    </component>
</template>
