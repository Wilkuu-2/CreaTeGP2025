<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import "leaflet";
import "proj4leaflet"
import "leaflet-geoserver-request";
import { ref, onMounted} from 'vue'
import axios from "axios"

const url = 'https://{s}.tile.osm.org/{z}/{x}/{y}.png';
const attribution = '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors';
const center = [52.23886540758533, 6.856291104103328];
const zoom = 15;
var debounce = false;
var queue = false;
// const center = ref([51.505, -0.159]);

var map = null;
var wfsLayer = null;

const BRTA_ATTRIBUTION = 'Kaartgegevens: © <a href="http://www.cbs.nl">CBS</a>, <a href="http://www.kadaster.nl">Kadaster</a>, <a href="http://openstreetmap.org">OpenStreetMap</a><span class="printhide">-auteurs (<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>).</span>'

function getWMTSLayer (layername, attribution) {
  return L.tileLayer(`https://service.pdok.nl/brt/achtergrondkaart/wmts/v2_0/${layername}/EPSG:3857/{z}/{x}/{y}.png`, { // eslint-disable-line no-undef
    WMTS: false,
    attribution: attribution,
    crossOrigin: true
  })
}

const brtRegular = getWMTSLayer('standaard', BRTA_ATTRIBUTION)
const brtGrijs = getWMTSLayer('grijs', BRTA_ATTRIBUTION)
const brtPastel = getWMTSLayer('pastel', BRTA_ATTRIBUTION)
const brtWater = getWMTSLayer('water', BRTA_ATTRIBUTION)


// see "Nederlandse richtlijn tiling" https://www.geonovum.nl/uploads/standards/downloads/nederlandse_richtlijn_tiling_-_versie_1.1.pdf
// Resolution (in pixels per meter) for each zoomlevel
var res = [3440.640, 1720.320, 860.160, 430.080, 215.040, 107.520, 53.760, 26.880, 13.440, 6.720, 3.360, 1.680, 0.840, 0.420]

// Define the CRS, note that you need to change the CRS AND The name in accordance with the specification of the CRS
var crs_name = 'EPSG:28992';
var crs = new L.Proj.CRS(crs_name, '+proj=sterea +lat_0=52.15616055555555 +lon_0=5.38763888888889 +k=0.9999079 +x_0=155000 +y_0=463000 +ellps=bessel +units=m +towgs84=565.2369,50.0087,465.658,-0.406857330322398,0.350732676542563,-1.8703473836068,4.0812 +no_defs', {
            transformation: L.Transformation(-1, -1, 0, 0), // eslint-disable-line no-undef
            resolutions: res,
            origin: [-285401.920, 903401.920],
            bounds: L.bounds([-285401.920, 903401.920], [595401.920, 22598.080]) // eslint-disable-line no-undef
});

function formatCoord(c) {
    return c
}

var ids = new Set([0]);

function setup_map() {
    map = L.map('map', {
        continousWorld: true,
        //crs: crs,
    });

    map.setView(center,zoom)

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(map);


    wfsLayer = L.geoJSON([], {
        onEachFeature: processPoint,
        //filter: filterPoint,
        style: stylePolygon,
    });

    refreshMap(map.getBounds())
    wfsLayer.addTo(map)

    map.on('moveend',() => {
        if (!debounce) {
            refreshMap(map.getBounds());
            debounce = true;
        } else {
            queue = true;
        }
        console.log(map.getZoom());
    });


}

function stylePolygon(feature){
    if (feature.geometry.type = 'polygon') {
        let color = "#3333ee";
        switch (feature.properties.category) {
            case "Grasland":
                color = "#11ee11";
                break;
            case "Bouwland":
                color = "#eeee33";
                break;
        }

        switch (feature.properties.gewascode) {
            case 1940:
                color = "#ee8888";
            break;
        }

        return {
            "color": color,
            "weight": 2,
            "opacity": 0.65
        };
    } else {
        return {
            "color": "#AAAAAA",
            "weight": 2,
            "opacity": 0.65
        };
    }
}

function filterPoint(feature, layer) {
    // return !ids.has(feature.properties.id);
}

function processPoint(feature, layer) {
    console.log()
    // ids.add(feature.properties.id);
   //  layer.bindPopup(feature.properties.gewas)
}

function refreshMap(bounds) {
    const c1 = bounds.getNorthWest();
    const c2 = bounds.getSouthEast();
    const request = {
        blatA: formatCoord(c1.lat),
        blonA: formatCoord(c1.lng),
        blatB: formatCoord(c2.lat),
        blonB: formatCoord(c2.lng),
    }
    wfsLayer.clearLayers();
    axios.post(
        // route('api:tiles'),
        route('api:farmers'),
        request).then()
        .then(response => {
            const data = response.data
            // console.log(data);
            // console.log(ids)
            wfsLayer.addData(data);
            debounce = false;
            if (queue) {
                refreshMap(map.getBounds());
                queue = false;
            }
        }, error => {console.error(error)});

}

onMounted(() => setup_map())


</script>

<template>
    <AppLayout title="Welcome" >
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                De kaart
            </h2>
        </template>

    <p>Welkom bij de kaart, de kaart komt nogg.</p>
    <div class="map-div">
        <div id="map">
        </div>
    </div>
    </AppLayout>
</template>


<style>
.map-div {
    width: 100%;
    height: 700px;
}

#map {
    height: 100%;
}

</style>
