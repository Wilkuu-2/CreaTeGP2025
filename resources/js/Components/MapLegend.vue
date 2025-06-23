<script setup>
import axios from 'axios';
import {style_color} from '@/util'
import { ref, onMounted, watch, computed, watchEffect, Suspense} from 'vue';

const props = defineProps({
    tid: Number,
    mode: String,
});

const default_legend = [ {name: "Ingeschreven boer", id: 1, color: '#FFFFFF', enabled: true}]
const entries = defineModel();
entries.value = default_legend;

watchEffect(async () => {
    await reloadLegend(props.mode,props.tid);
});

async function reloadLegend(mode,tid) {
    if (mode == 'org'){
        const result = await axios.get(route('api:legend', tid));
        const milestones = result.data.map((mst) => {
            mst.enabled = true;
            return mst;
        });

        milestones.push({
            name: "Niet ingeschreven bij initiatief", id: -1, color: "#FFFFFF", enabled: true})
        entries.value = milestones;
    } else {
        entries.value = default_legend;
    }
}
</script>

<template>
    <div class="my-2">
        <Suspense>
            <div v-for="entry in entries" class="flex my-3 flex-row">
                <div class="border-lime-900 mx-1 p-1 align-text-top colorbox" :style="style_color(entry)">
                      <input type="checkbox" class="mb-1 ml-0.5" v-model='entry.enabled'/>
                </div>
                <span>{{entry.name}}</span>
            </div>
        </Suspense>
    </div>
</template>

<style>
.colorbox {
    margin-top: -3pt;
    width: 2em;
    border-radius: 6pt;
    border-width: 2px;
    margin-left:  4px;
    margin-right:  4px;
    padding: 4px;
}
</style>
