<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    classe: Object,
    anneeScolaire: String,
    eleves: Array,
    estLycee: Boolean
});

function imprimer() {
    window.print();
}

const dateSignature = new Date().toLocaleDateString('fr-FR');
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cartes d'identité scolaire — {{ classe.nom }}
            </h2>
        </template>

        <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">

                    <div class="flex justify-between items-center mb-6 no-print">
                        <p class="text-sm text-gray-600">
                            {{ eleves.length }} élève(s) — Format 5,5 × 8,5 cm
                        </p>
                        <button
                            @click="imprimer"
                            class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-md"
                        >
                            🖶 Imprimer la planche
                        </button>
                    </div>

                    <div class="cartes-grille">
                        <div v-for="eleve in eleves" :key="eleve.id" class="carte">

                           <div class="carte-entete">
                                <img src="/images/logo.png" alt="Logo" class="carte-logo" />
                                <div class="carte-entete-texte">
                                    <p class="carte-ecole-principal">Complexe Scolaire Conventionné Catholique</p>
                                    <p class="carte-ecole">LA DIVINE PROVIDENCE</p>
                                    <p class="carte-devise">Paix-Joie-Travail</p>
                                </div>
                            </div>

                            <p class="carte-titre">CARTE D'IDENTITÉ SCOLAIRE</p>
                            <p class="carte-annee">{{ anneeScolaire }}</p>

                            <div class="carte-corps">
                                <div class="carte-photo">
                                    <img v-if="eleve.photo" :src="`/storage/${eleve.photo}`" />
                                    <div v-else class="carte-photo-vide">Photo</div>
                                </div>

                               <div class="carte-infos">
                                    <p><strong>Nom :</strong> {{ eleve.nom }}</p>
                                    <p><strong>Prénom :</strong> {{ eleve.prenom }}</p>
                                    <p><strong>Matricule :</strong> {{ eleve.matricule ?? '—' }}</p>
                                    <p><strong>Classe :</strong> {{ classe.nom }}</p>
                                    <p><strong>Date de naissance :</strong> {{ eleve.date_naissance }}</p>
                                    <p><strong>Lieu de naissance :</strong> {{ eleve.lieu_naissance }}</p>
                                    <p><strong>Adresse :</strong> {{ eleve.adresse }}</p>
                                </div>
                            </div>

                          <div class="carte-signature-haut">
                            <p class="carte-signature-date">Fait le {{ dateSignature }}</p>
                            <p class="carte-signature-texte">{{ estLycee ? 'La Proviseure' : 'La Directrice' }}</p>
                            <div class="carte-signature-images">
                                <img src="/images/signature-secondaire.png" class="carte-signature-img" />
                                <img :src="estLycee ? '/images/cachet-lycee.png' : '/images/cachet-college.png'" class="carte-cachet-img" />
                            </div>
                            <p class="carte-nom-signataire">Soeur Sandrine BOTSOULA</p>
                        </div>

                            <p class="carte-mention">Cette carte est strictement personnelle.</p>

                       </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.cartes-grille {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    justify-items: center;
}

.carte {
    width: 8.5cm;
    height: 5.5cm;
    border: 1px solid #333;
    border-radius: 6px;
    padding: 4px 6px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-size: 9px;
    line-height: 1.3;
    background: white;
}

.carte-entete {
    display: flex;
    align-items: center;
    gap: 4px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 2px;
}

.carte-logo {
    width: 22px;
    height: 22px;
    object-fit: contain;
}

.carte-entete-texte {
    line-height: 1.1;
}

.carte-ecole-principal {
    font-weight: bold;
    font-size: 6.5px;
    margin: 0;
    line-height: 1.1;
}

.carte-ecole {
    font-weight: bold;
    color: #047857;
    font-size: 8px;
    margin: 0;
}

.carte-devise {
    font-size: 6.5px;
    color: #555;
}

.carte-titre {
    text-align: center;
    font-weight: bold;
    font-size: 10.5px;
    margin-top: 2px;
    text-transform: uppercase;
}

.carte-annee {
    text-align: center;
    font-size: 8.5px;
    color: #555;
    margin-bottom: 2px;
}

.carte-corps {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 90px;
    flex: 1;
}

.carte-photo {
    width: 1.8cm;
    height: 2.2cm;
    border: 1px solid #999;
    flex-shrink: 0;
    overflow: hidden;
}

.carte-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.carte-photo-vide {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6px;
    color: #999;
    background: #f3f4f6;
}
.carte-infos {
    margin-left: 6px;
}

.carte-infos p {
    margin: 0 0 1.5px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.carte-signature-haut {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    position: relative;
    top: -8px;      /* réduit, au lieu de -10px */
    right: 5px;    /* garde ta valeur actuelle, ajuste si besoin */
}

.carte-signature-images {
    position: relative;
    width: 45px;    /* réduit, au lieu de 60px */
    height: 30px;   /* réduit, au lieu de 42px */
    margin-top: 1px;
}

.carte-signature-img {
    position: absolute;
    top: 5px;
    left: 28px;   /* réduit, au lieu de 30px */
    height: 18px;   /* réduit, au lieu de 26px */
    object-fit: contain;
}

.carte-cachet-img {
    position: absolute;
    top: -2px;      /* réduit, au lieu de 18px */
    left: -2px;      /* réduit, au lieu de 10px */
    height: 24px;   /* réduit, au lieu de 34px */
    object-fit: contain;
    opacity: 0.9;
}

.carte-signature-texte {
    font-size: 8px;
    font-weight: bold;
    margin: 0;
    text-align: right;
}

.carte-signature-date {
    font-size: 7.5px;
    color: #555;
    margin: 0;
    text-align: right;
}

.carte-mention {
    text-align: center;
    font-size: 7.5px;
    font-style: italic;
    color: #555;
    border-top: 1px solid #ccc;
    padding-top: 1px;
    margin: 0;
    margin-top: -9px;
}
.carte-nom-signataire {
    font-size: 5.5px;   /* légèrement réduit */
    font-weight: bold;
    color: #333;
    margin: 0;
    margin-top: -9px;   /* resserre l'espace avec le cachet au-dessus */
    text-align: right;
}

@media print {
    .no-print {
        display: none !important;
    }

    .cartes-grille {
        gap: 4mm;
    }

    .carte {
        page-break-inside: avoid;
    }
   .carte-corps {
    display: flex;
    justify-content: space-between;
    gap: 6px;
    flex: 1;
}
}

</style>
