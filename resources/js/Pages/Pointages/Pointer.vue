<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

defineProps({
    professeurs: Array,
});

const page = usePage();

const form = useForm({
    professeur_id: '',
    pin: '',
});

function pointer() {
    form.post(route('pointage.enregistrer'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('pin');
        },
    });
}
</script>

<template>
    <div class="min-h-screen bg-emerald-800 flex items-center justify-center px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
            <div class="text-center mb-6">
                <img src="/images/logo.png" alt="Logo" class="w-16 h-16 mx-auto mb-2" />
                <h1 class="text-lg font-bold text-gray-800">LA DIVINE PROVIDENCE</h1>
                <p class="text-sm text-gray-500">Pointage des professeurs</p>
            </div>

            <div v-if="page.props.flash?.success" class="bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-md p-3 mb-4 text-center">
                {{ page.props.flash.success }}
            </div>

            <form @submit.prevent="pointer" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Professeur</label>
                    <select v-model="form.professeur_id" required class="w-full border-gray-300 rounded-md">
                        <option value="" disabled>Sélectionnez votre nom</option>
                        <option v-for="prof in professeurs" :key="prof.id" :value="prof.id">
                            {{ prof.nom }} {{ prof.prenom }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Code PIN</label>
                    <input
                        v-model="form.pin"
                        type="password"
                        inputmode="numeric"
                        maxlength="4"
                        required
                        class="w-full border-gray-300 rounded-md text-center text-2xl tracking-widest"
                        placeholder="••••"
                    />
                    <p v-if="form.errors.pin" class="text-red-600 text-sm mt-1">{{ form.errors.pin }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-md"
                >
                    Pointer
                </button>
            </form>
        </div>
    </div>
</template>