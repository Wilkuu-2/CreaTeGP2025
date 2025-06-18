<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const page = usePage();

const form = defineForm({
    name: '',
    email: '',
    phone_number: '',
    show_email: Boolean,
    show_number: Boolean,
    location: Object,
})

const submit () => {
    form.put(route('farmstead.update'))
}

const props =  defineProps(
)

</script>

<template title="Extra informatie voor boeren">
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Boerderij registreren
            </h2>
            <p>
                In dit formulier vult u informatie over uw boerderij in. Dit is nodig om u op de kaart te plaatsen.
            </p>
        </template>

        <form @submit.prevent="submit">
            <div class="mt-4">
                <InputLabel for="name" value="Naam van de boerderij">
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                </InputLabel>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4 flex flex-row">
                <div>
                    <InputLabel for="email" value="Contact E-mail">
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="email"
                        />
                    </InputLabel>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel for="show_email" value="Toon email op de kaart">
                        <Checkbox id="show_email" name="show_email"/>
                    </InputLabel>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>
         </form>
    </AppLayout>
</template>
