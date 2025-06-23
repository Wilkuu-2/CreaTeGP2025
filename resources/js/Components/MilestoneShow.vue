<script setup>
import {style_color} from '@/util'
import {computed} from 'vue';
import DragIcon from './DragIcon.vue';
const props = defineProps({
    elock: Object,
    milestone: Object,
    name_id_map: Object,
});
const milestone = props.milestone;
const name_id_map = props.name_id_map;
const elock = props.elock;

const columnstyle = computed(() =>{
    return elock.isEditing() ? "1fr 1fr" : "1fr 1fr 3em";
})
const lastcol_style = computed(() => {
    return elock.isEditing() ? "text-right" : "text-left";
});
</script>

<template>
    <div class="milestone_grid" :style="{'grid-template-columns': columnstyle }">
        <div class="flex-row flex h-min">
            <slot name="corner" :milestone="milestone" />
            <div v-if="milestone.do_map" class="border-lime-900 mx-1 p-1 align-text-top colorbox" :style="style_color(milestone)">&nbsp;&nbsp;&nbsp;</div>
            <div v-else class="border-lime-900 mx-1 p-1 align-text-top colorbox colorbox-crossed">
                <svg class="crosss" xmlns='http://www.w3.org/2000/svg' version='1.1' preserveAspectRatio='none' viewBox='0 0 100 100'><path d='M100 0 L0 100 ' style='stroke: #365314' stroke-width='8'/></svg>
            </div>
            <div>{{milestone.name}}</div>
         </div>
        <div :class="lastcol_style">
            <label>Benodigde criteria:</label>
            <select class="m-1 p-0 border-none bg-none" disabled :value='milestone.is_any'>
                <option :value='true'>Een van de {{milestone.criteria.length}}</option>
                <option :value='false'>Alle {{milestone.criteria.length}}</option>
            </select>
        </div>
        <div v-if="!elock.isEditing()"  class="flex flex-row w-min mx-3">
            <button type="button" @click="milestone.edit_milestone(elock)" >Edit</button><br>
        </div>
    </div>
</template>

<style>
.milestone_grid {
    display: grid;
    column-gap: 4px;
}

.crosss {
    margin: -4px;
}

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
