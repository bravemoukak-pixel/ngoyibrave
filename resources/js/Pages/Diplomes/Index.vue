<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    classe: Object,
    anneeScolaire: String,
    eleves: Array,
    estLycee: Boolean,
});
const dateSignature = new Date().toLocaleDateString('fr-FR');

function imprimer() {
    window.print();
}

function ordinal(rang, sexe) {
    if (rang === 1) {
        return sexe === 'F' ? '1ère' : '1er';
    }
    return rang + 'ème';
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Diplômes d'honneur — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex justify-between items-center mb-6 no-print">
                        <p class="text-sm text-gray-600">
                            {{ eleves.length }} diplôme(s) — Top 5 de la classe
                        </p>
                        <button
                            @click="imprimer"
                            class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer les diplômes
                        </button>
                    </div>

                    <div v-if="eleves.length === 0" class="text-gray-500">
                        Aucun élève avec une moyenne annuelle calculable pour cette classe.
                    </div>

                    <div class="diplomes-grille">
                        <div v-for="eleve in eleves" :key="eleve.id" class="diplome">

                            <div class="diplome-entete">
                                <div class="diplome-entete-gauche">
                                    <img src="/images/logo.png" alt="Logo" class="diplome-logo" />
                                    <div class="diplome-entete-texte">
                                        <p class="diplome-ecole-principal">Complexe Scolaire Conventionné Catholique</p>
                                        <p class="diplome-ecole">LA DIVINE PROVIDENCE</p>
                                        <p class="diplome-devise">Paix-Joie-Travail — Pointe-Noire</p>
                                    </div>
                                </div>
                                <p class="diplome-annee">Année scolaire {{ anneeScolaire }}</p>
                            </div>

                            <p class="diplome-titre">Diplôme d'Honneur</p>
                           <div class="diplome-corps">
                                <p class="diplome-intro">Décerné à</p>
                                <p class="diplome-nom">{{ eleve.nom }} {{ eleve.prenom }}</p>
                                <p class="diplome-detail">Classe : {{ classe.nom }}</p>
                                <p class="diplome-rang">{{ ordinal(eleve.rang, eleve.sexe) }} de la classe</p>
                                <p class="diplome-detail">Moyenne annuelle : {{ eleve.moyenneAnnuelle }}/20</p>
                                <p class="diplome-mention">Mention : {{ eleve.mention }}</p>
                                <p class="diplome-encouragement">{{ eleve.phraseEncouragement }}</p>
                            </div>

                           <div class="diplome-signature">
                                <p class="diplome-signature-date">Fait à Pointe-Noire le {{ dateSignature }}</p>
                                <p class="diplome-signature-texte">{{ estLycee ? 'La Proviseure' : 'La Directrice' }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.diplomes-grille {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.diplome {
    border: 4px double blue;
    border-radius: 8px;
    padding: 30px 40px;
    text-align: center;
    background: white;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 550px;
}
@media print {
    .diplome {
        min-height: 95vh;
    }
}
.diplome-entete {
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-align: left;
    border-bottom: 1px solid #ccc;
    padding-bottom: 8px;
    margin-bottom: 8px;
}

.diplome-entete-gauche {
    display: flex;
    align-items: center;
    gap: 8px;
}

.diplome-logo {
    width: 36px;
    height: 36px;
    object-fit: contain;
}

.diplome-ecole-principal {
    font-weight: bold;
    font-size: 11px;
    margin: 0;
}

.diplome-ecole {
    font-weight: bold;
    color: #047857;
    font-size: 13px;
    margin: 0;
}

.diplome-devise {
    font-size: 10px;
    color: #575656;
    margin: 0;
}

.diplome-titre {
    font-size: 44px;
    font-weight: bold;
    text-transform: uppercase;
    color:blue;
    margin: 20px 0 8px 0;
    font-family: Georgia, serif;
}
.diplome-annee {
    font-size: 18px;
    color: #3f3f3f;
    white-space: nowrap;
}
.diplome-intro {
    font-size: 20px;
    font-style: italic;
    margin: 8px 0;
}

.diplome-nom {
    font-size: 36px;
    font-weight: bold;
    margin: 12px 0;
    text-transform: uppercase;
}

.diplome-detail {
    font-size: 18px;
    margin: 6px 0;
}

.diplome-rang {
    font-size: 26px;
    font-weight: bold;
    color:blue;
    margin: 10px 0;
}

.diplome-mention {
    font-size: 22px;
    font-weight: bold;
    margin-top: 10px;
}

.diplome-signature-texte {
    font-size: 17px;
    font-weight: bold;
}
.diplome-signature {
    margin-top: auto;
    padding-bottom: 95px;
    text-align: right;
}
.diplome-signature-date {
    font-size: 12px;
    color: #555;
    margin: 0 0 4px 0;
}
.diplome-encouragement {
    font-size: 17px;
    font-style: italic;
    color: rgb(18, 18, 18)a1a;
    margin-top: 16px;
    padding: 0 20px;
    line-height: 1.5;
}
@page {
    margin: 1cm 0;
}


@media print {
    .py-12 {
        padding: 0 !important;
    }
    .max-w-5xl {
        max-width: 100% !important;
    }
    .mx-auto {
        margin: 0 !important;
    }
    .sm\:px-6, .lg\:px-8 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .bg-white {
        background: white !important;
    }
    .shadow {
        box-shadow: none !important;
    }
    .rounded-lg {
        border-radius: 0 !important;
    }
    .p-6 {
        padding: 0 !important;
    }
}

</style>