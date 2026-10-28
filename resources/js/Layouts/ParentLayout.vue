<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-emerald-800 text-white shadow px-6 py-4 flex justify-between items-center no-print">
            <h1 class="text-lg font-bold">
                <slot name="header">Complexe Scolaire Conventionné Catholique — LA DIVINE PROVIDENCE</slot>
            </h1>
            <div class="flex items-center gap-4">
                <span class="text-emerald-100 text-sm">{{ $page.props.auth.user.name }}</span>
                <Link href="/logout" method="post" as="button" class="text-sm bg-emerald-900 hover:bg-emerald-950 px-3 py-1.5 rounded-md">
                    Déconnexion
                </Link>
            </div>
        </header>

        <main class="p-6">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>