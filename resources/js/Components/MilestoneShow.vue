<script setup>
import {style_color} from '@/util'
import DragIcon from './DragIcon.vue';
const props = defineProps({
    elock: Object,
    milestone: Object,
    name_id_map: Object,
});
const milestone = props.milestone;
const name_id_map = props.name_id_map;
const elock = props.elock;
</script>

<template>
    <div class="flex flex-row justify-between">
        <div class=" flex flex-row">
                <slot name="corner" :milestone="milestone" />
            <span  class="border border-black-600 rounded-b" :style="style_color(milestone)"></span> {{milestone.name}}
         </div>
        <div class="flex flex-row">
            <label> Op de kaart:</label>
            <input class="m-1" type='checkbox' disabled v-model='milestone.do_map'>
        </div>
        <div :style="{visibility: elock.isEditing() ? 'hidden' : 'visible'}"  class="flex flex-row">
            <button @click="milestone.edit_milestone(elock)" >Edit</button><br>
        </div>
    </div>
</template>
