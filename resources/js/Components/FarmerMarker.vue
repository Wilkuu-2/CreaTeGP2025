<script setup>
import {LMap, LTileLayer, LGeoJson, LMarker, LPopup } from "@vue-leaflet/vue-leaflet"
import {gjson_point_to_latlng} from '@/util'
import "leaflet";
import { computed } from "vue";

const props = defineProps({
    marker: Object,
    legend: Object,
});

const enabled = computed(()=>{
    const id = props.marker.properties.milestone_id || -1;
    const le = props.legend.find((el) => el.id == id);
    return le?.enabled || false;
})

function make_marker_icon(marker){
    const color = marker.properties.color || "#ffffff";
    const marker_html = `<svg class="fmarker" xmlns="http://www.w3.org/2000/svg"                         xmlns:xlink="http://www.w3.org/1999/xlink"                         xmlns:krita="http://krita.org/namespaces/svg/krita"                         xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"                          viewBox="0 0 184.32 184.32">                     <defs/>                     <path id="shape0" transform="translate(48.96, 4.59780240509141)" fill="${color}" fill-rule="evenodd" stroke="#4f2d1d" stroke-width="5.4" stroke-linecap="square" stroke-linejoin="bevel" d="M87.12 43.5101L87.1048 42.3497C85.7542 -13.7108 0.106305 -14.5209 0.0151577 42.3497L0 43.5101C1.30721 51.464 1.50574 55.8394 3.63954 60.9683C14.0185 86.3774 27.5094 104.526 43.5049 125.336C60.815 103.31 71.131 85.1488 83.0294 61.9639C84.7719 57.8376 87.3559 50.4714 87.12 43.5101M59.2362 39.9796C59.3401 20.241 28.3039 19.0911 27.9577 39.9796C28.0421 57.8122 58.5364 59.2472 59.4162 40.1596" sodipodi:nodetypes="ccccccccccc"/>             </svg>`;
    return L.divIcon({html: marker_html,
        className: 'd-icon' })
}


</script>

<template>
    <l-marker v-if="enabled"
        :latLng="gjson_point_to_latlng(marker.geometry)"
        :icon="make_marker_icon(marker)">
        <l-popup :options="{offset: {x: 0, y: -12}}">
            <div>
                <b>{{marker.properties.name}}</b>
            </div>
            <div v-if="marker.properties.show_email">
                <span>E-mail:&nbsp;</span>
                <a :href="'mailto:' + marker.properties.email" >
                        {{marker.properties.email}}
                </a>
            </div>
            <div v-if="marker.properties.show_number">
                <span>Telefoon:&nbsp;</span>
                <a :href="'tel:' + marker.properties.number" >
                    {{marker.properties.phone_number}}
                </a>
            </div>
            <hr>
            <div v-if="marker.properties.milestone_name != null">
                <span>Mijlpaal: </span><span>{{marker.properties.milestone_name}}</span>
            </div>

        </l-popup>
    </l-marker>
</template>
