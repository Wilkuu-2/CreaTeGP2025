<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import FarmerMarker from '@/Components/FarmerMarker.vue'
import {LMap, LTileLayer, LGeoJson, LMarker, LPopup } from "@vue-leaflet/vue-leaflet"
import "leaflet";
import { ref, onMounted, watch} from 'vue'
import {gjson_point_to_latlng} from '@/util'

const url = ref();
const attribution = ref('&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors');
const center = ref([52.23886540758533, 6.856291104103328]);
const zoom = ref(8);
const markers = ref([])
const bounds = ref(L.latLngBounds(L.latLng(-100,-100),L.latLng(100,100)));
var debounce = false;
var queue = false;


watch(zoom, async(newZ, oldZ) => {
    await refreshMap();
});
watch(center, async(newC, oldC) => {
    await refreshMap();
});

async function refreshMap() {
    console.log("refreshing")
    const c1 = bounds.value.getNorthWest();
    const c2 = bounds.value.getSouthEast();
    console.log([c1, c2])

    if (!c1 || !c2){
        console.error('bounds cooked');
        return
    }

    const request = {
        blatA: c1.lat,
        blonA: c1.lng,
        blatB: c2.lat,
        blonB: c2.lng,
        tid: 1
    }
    if (debounce) {
        // queue = true;
        return;
    }

    // debounce = true;
    try {
        const res = await fetch(
            // route('api:tiles'),
            // route('api:farmers'),
            route('api:results'),
            {
                method: "POST",
                body: JSON.stringify(request),
                headers: { "Content-Type": "application/json"},
             });

        markers.value = await res.json();

        // console.log(ids)
    } catch (error) {
        console.log(error);
    } finally {
        debounce = false;
    }

}

onMounted(async () => {
    await refreshMap();
})


</script>

<template>
    <AppLayout>
    <div class="grid grid-cols-4">
        <aside class="border border-blue-400 bg-white p-2">
                <h4 class="text-lg font-bold">Lagen</h4>
                <a href="">Alle boeren</a>
                <h5 class="text-sm font-bold">Publiek</h5>
                <h5 class="text-sm font-bold">Initiatief {{}}</h5>
        </aside>
        <div class="map-div h-full col-span-3">
            <l-map ref="main_map" v-model:zoom="zoom" v-model:center="center" v-model:bounds="bounds" >
                    <l-tile-layer
                        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                        layer-type="base"
                        name="OpenStreetMap"
                    ></l-tile-layer>
                    <FarmerMarker v-for="marker in markers" :marker="marker"/>
              </l-map>
        </div>
    </div>
    </AppLayout>
</template>

<style lang="css">
.map-div {
    width: 100%;
    height: 1000px
}

#map {
    width: 100%;
}

.fmarker {
    width: 40pt;
    height: 40pt;
    margin-top: -25pt;
    margin-left: -15pt;
}
.d-icon {
    background: none;
}

.as {
    height: 1000;
}

</style>
