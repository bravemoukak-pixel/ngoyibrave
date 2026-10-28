<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
   <aside class="w-64 bg-emerald-800 text-white flex flex-col no-print">
      <div class="p-4 border-b border-emerald-700">
        <h1 class="text-lg font-bold leading-tight">
          Complexe Scolaire<br />
          Conventionné Catholique<br />
          <span class="text-emerald-300">LA DIVINE PROVIDENCE</span>
        </h1>
      </div>
        <nav class="flex-1 p-4 space-y-2">
   <Link :href="route('dashboard')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>🏠</span>
        <span>Tableau de bord</span>
   </Link>
    <Link :href="route('niveaux.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>🎓</span>
        <span>Niveaux</span>
    </Link>
    <Link :href="route('emplois-du-temps.general.college')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
    <span>🗓️</span>
    <span>Emploi du temps — Collège</span>
</Link>
<Link :href="route('emplois-du-temps.general.lycee')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
    <span>🗓️</span>
    <span>Emploi du temps — Lycée</span>
</Link>
<Link :href="route('emplois-du-temps.general.complet')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
    <span>🗓️</span>
    <span>Emploi du temps général — Complet</span>
</Link>
<Link :href="route('statistiques.evolution')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
    <span>📈</span>
    <span>Évolution des résultats</span>
</Link>
    <Link :href="route('professeurs.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>👨‍🏫</span>
        <span>Enseignants</span>
    </Link>
    <Link :href="route('professeurs.annuaire')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>📞</span>
        <span>Annuaire téléphonique</span>
    </Link>
    <Link :href="route('pointage.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>📍</span>
        <span>Pointage</span>
    </Link>
    <Link :href="route('pointages.recap')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>⏱️</span>
        <span>Suivi des heures</span>
    </Link>
    <Link :href="route('paie.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>💰</span>
        <span>Bulletins de paie</span>
    </Link>
    <Link :href="route('pointages.gestion')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>🛠️</span>
        <span>Corriger un pointage</span>
    </Link>
    <Link v-if="$page.props.auth.user.role !== 'direction'" :href="route('admin.niveaux')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>⚙️</span>
        <span>Gérer les niveaux</span>
    </Link>
    <Link :href="route('parents.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>👪</span>
        <span>Parents</span>
    </Link>
    <Link :href="route('parents.par-niveau')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>👪</span>
        <span>Parents par niveau</span>
    </Link>
    <Link :href="route('paiements.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>💳</span>
        <span>Paiements</span>
    </Link>
   <Link :href="route('suivi-paiements.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>💵</span>
        <span>Suivi des paiements</span>
   </Link>
    <Link :href="route('suivi-notes.classe')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>📋</span>
        <span>Suivi des évaluations</span>
    </Link>
    <Link :href="route('historique-classes.index')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>🗂️</span>
        <span>Historique des classes</span>
    </Link>
    <Link :href="route('statistiques.college')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>📊</span>
        <span>Résultats globaux — Collège</span>
    </Link>
    <Link :href="route('statistiques.lycee')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>📊</span>
        <span>Résultats globaux — Lycée</span>
    </Link>
    <Link :href="route('statistiques.repartition-age')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
        <span>🎂</span>
        <span>Répartition par âge</span>
    </Link>
   <a v-if="$page.props.auth.user.role !== 'direction'" :href="route('sauvegarde.exporter')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-emerald-700 transition">
      <span>💾</span>
      <span>Sauvegarder la base</span>
    </a>
    </nav>
    </aside>

    <!-- Contenu principal -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Barre du haut -->
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center no-print">
        <h2 class="text-xl font-semibold text-gray-700">
          <slot name="header">Tableau de bord</slot>
        </h2>
        <div class="flex items-center gap-4">
          <span class="text-gray-600">{{ $page.props.auth.user.name }}</span>
          <Link href="/logout" method="post" as="button" class="text-sm text-red-600 hover:underline">
            Déconnexion
          </Link>
        </div>
      </header>

      <!-- Zone de contenu -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<style>
@media print {
    .flex.h-screen,
    .flex-1.flex.flex-col.overflow-hidden,
    main.flex-1.overflow-y-auto {
        display: block !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }
}
</style>