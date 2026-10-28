<template>
    <table class="w-full border-collapse border border-gray-300 text-xs">
        <colgroup>
            <col style="width: 4%;" />
            <col v-for="n in nombreColonnes" :key="'col-' + n" :style="{ width: largeurColonne }" />
        </colgroup>
        <thead>
            <tr class="bg-emerald-600 text-white">
                <th class="border border-gray-300 p-1 w-24" rowspan="2">Horaire</th>
                <th
                    v-for="jour in jours"
                    :key="jour"
                    :colspan="classes.length"
                    class="border border-gray-300 p-1 capitalize"
                >
                    {{ jour }}
                </th>
            </tr>
            <tr class="bg-emerald-500 text-white">
                <template v-for="jour in jours" :key="'sub-' + jour">
                    <th
                        v-for="classe in classes"
                        :key="jour + '-' + classe.id"
                        class="border border-gray-300 p-1"
                    >
                        {{ classe.nom }}
                    </th>
                </template>
            </tr>
        </thead>
        <tbody>
            <tr v-for="creneau in creneaux" :key="creneau.id">
                <td class="border border-gray-300 p-1 font-medium text-center bg-gray-50">
                    {{ formatHeure(creneau.heure_debut) }} - {{ formatHeure(creneau.heure_fin) }}
                </td>

                <td
                    v-if="creneau.type !== 'cours'"
                    :colspan="jours.length * classes.length"
                    class="border border-gray-300 p-1 text-center bg-gray-200 font-semibold text-gray-600 uppercase"
                >
                    {{ creneau.type === 'recreation' ? 'Récréation' : 'Pause' }}
                </td>

                <template v-else>
                    <template v-for="jour in jours" :key="jour + '-' + creneau.id">
                        <td
                            v-for="classe in classes"
                            :key="jour + '-' + creneau.id + '-' + classe.id"
                            class="border border-gray-300 p-1 text-center"
                        >
                            {{ getMatiere(jour, creneau.id, classe.id, classe.nom, creneau) }}
                        </td>
                    </template>
                </template>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    classes: Array,
    jours: Array,
    creneaux: Array,
    emplois: Array,
});

const nombreColonnes = computed(() => props.jours.length * props.classes.length);
const largeurColonne = computed(() => (96 / nombreColonnes.value) + '%');

const abreviations = {
    'MATHS': 'Maths',
    'FRANCAIS': 'Franç.',
    'ANGLAIS': 'Angl.',
    'HISTOIRE-GEO': 'HG',
    'PHYSIQUE-CHIMIE': 'PC',
    'PHILOSOPHIE': 'Philo',
    'ESPAGNOL': 'Esp.',
    'MUSIQUE': 'Mus.',
    'SVT': 'SVT',
    'EPS': 'EPS',
};

function abregerMatiere(matiere) {
    if (!matiere) return '-';
    return abreviations[matiere] ?? (matiere.length > 8 ? matiere.slice(0, 8) + '.' : matiere);
}

const classesSansCoursWeekend = ['6ᵉ 1', '6ᵉ 2', '5ᵉ', '4ᵉ', 'Seconde A', 'Seconde C', 'Première A', 'Première S'];
const classesSamediLimite = ['3ᵉ', 'Terminale A', 'Terminale C', 'Terminale D'];

function pasDeCours(jour, creneau, classeNom) {
    if (jour === 'lundi' && creneau.heure_debut >= '13:45') return true;
    if (jour === 'samedi') {
        if (classesSansCoursWeekend.includes(classeNom)) return true;
        if (classesSamediLimite.includes(classeNom) && creneau.heure_debut >= '11:45') return true;
    }
    return false;
}

function getMatiere(jour, creneauId, classeId, classeNom, creneau) {
    const emploi = props.emplois.find(
        e => e.jour === jour && e.creneau_horaire_id === creneauId && e.classe_id === classeId
    );
    if (emploi) return abregerMatiere(emploi.matiere);
    return pasDeCours(jour, creneau, classeNom) ? '—' : 'TG';
}

function formatHeure(heure) {
    return heure.slice(0, 5);
}
</script>