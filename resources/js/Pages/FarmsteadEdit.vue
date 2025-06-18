<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {ref, onMounted} from 'vue'
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {LMap, LTileLayer, LGeoJson, LMarker } from "@vue-leaflet/vue-leaflet"
import "leaflet"
import { latlng_to_gjson_point, gjson_point_to_latlng} from "@/util";

// Todo: Move picker to separate component
const page = usePage();
const markers = ref([]);

const props =  defineProps({
    farmstead: Object,
    prefill: Object
})
const form = useForm({
    name: props.farmstead?.name || props.prefill?.name || '',
    email: props.farmstead?.email || props.prefill?.email || '',
    phone_number: props.farmstead?.phone_number || props.prefill?.phone_number || '',
    show_email: props.farmstead?.show_email || props.prefill?.show_email || false ,
    show_number: props.farmstead?.show_number || props.prefill?.show_number || false,
    location: props.farmstead?.location || props.prefill?.location || null,
});

function moveMarker(event) {
    markers.value = []
    markers.value.push(event.latlng)
    form.location = latlng_to_gjson_point(event.latlng)
    console.log(form.location)
}


const center = ref([52.05140211668126, 6.3209299468511535]);
const zoom = ref(8);
const submit = () => {
    if (props.farmstead !== undefined) {
        form.put(route('farmstead.update', {farmstead: props.farmstead.id}));
    } else {
        form.post(route('farmstead.store'))
    }
};

onMounted(() => {
    center.value = [52.05140211668126, 6.3209299468511535];
    if (form.location) {
        markers.value.push(gjson_point_to_latlng(form.location));
        center.value = markers.value[0];
    }
})


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
        <AuthenticationCard>
        <form @submit.prevent="submit">
            <div class="mt-4">
                <InputLabel for="name" value="Naam van de boerderij"/>
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4 flex flex-row">
                <div class="flex-grow">
                    <InputLabel for="email" value="Contact E-mail"/>
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel for="show_email" value="Publiek beschikbaar"/>
                    <div class="flex justify-center w-full my-2">
                        <Checkbox id="show_email" name="show_email" v-model:checked="form.show_email" class="w-7 h-7"/>
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>
            <div class="mt-4 flex flex-row">
                <div class="flex-grow">
                    <InputLabel for="phone_number" value="Contact nummer"/>
                    <TextInput
                        id="phone_number"
                        v-model="form.phone_number"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="phone_number"
                    />
                    <InputError class="mt-2" :message="form.errors.phone_number" />
                </div>
                <div>
                    <InputLabel for="show_number" value="Publiek beschikbaar"/>
                    <div class="flex justify-center w-full my-2">
                        <Checkbox id="show_number" name="show_number" v-model:checked="form.show_number" class="w-7 h-7"/>
                    </div>
                    <InputError class="mt-2" :message="form.errors.phone_number" />
                </div>
            </div>
             <div class="w-full h-72 mt-4">
                <l-map ref="picker_map" v-model:zoom="zoom" :center="center" @click="moveMarker">
                    <l-tile-layer
                            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                            layer-type="base"
                            name="OpenStreetMap"
                        ></l-tile-layer>
                    <l-marker v-for="marker, index in markers" class="map-marker" :lat-lng="marker">
                        </l-marker>

                </l-map>
             </div>
            <div class="flex flex-row justify-center">
                <PrimaryButton class="mt-4 ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing || markers.length < 1">
                    Opslaan
                </PrimaryButton>
            </div>
         </form>
        </AuthenticationCard>
    </AppLayout>
</template>

<style lang="css">
/* .map-marker::before { */
/*    content: '<svg xmlns="http://www.w3.org/2000/svg" */
/*                         xmlns:xlink="http://www.w3.org/1999/xlink" */
/*                         xmlns:krita="http://krita.org/namespaces/svg/krita" */
/*                         xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" */
/*                         width="184.32pt" */
/*                         height="184.32pt" */
/*                         viewBox="0 0 184.32 184.32"> */
/*                     <defs/> */
/*                     <path id="shape0" transform="translate(48.96, 4.59780240509141)" fill="#ce742a" fill-rule="evenodd" stroke="#4f2d1d" stroke-width="5.4" stroke-linecap="square" stroke-linejoin="bevel" d="M87.12 43.5101L87.1048 42.3497C85.7542 -13.7108 0.106305 -14.5209 0.0151577 42.3497L0 43.5101C1.30721 51.464 1.50574 55.8394 3.63954 60.9683C14.0185 86.3774 27.5094 104.526 43.5049 125.336C60.815 103.31 71.131 85.1488 83.0294 61.9639C84.7719 57.8376 87.3559 50.4714 87.12 43.5101M59.2362 39.9796C59.3401 20.241 28.3039 19.0911 27.9577 39.9796C28.0421 57.8122 58.5364 59.2472 59.4162 40.1596" sodipodi:nodetypes="ccccccccccc"/> */
/*             </svg>'; */
/* } */



</style>
