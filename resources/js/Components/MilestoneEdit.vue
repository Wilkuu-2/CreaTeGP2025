<script setup>
import {style_color} from '@/util'
const props = defineProps({
    elock: Object,
    milestone: Object,
    name_id_map: Object,
});

const emit = defineEmits([ 'remove'  ]);
const milestone = props.milestone;
const name_id_map = props.name_id_map;
const elock = props.elock;
</script>

<template>
    <form>
        <div class="milestone_grid_edit">
            <div class="mx-2 w-min">
                <label for="do_map" class="my-1">Zichtbaarheid:</label>
                <div class="my-1 flex flex-row justify-between">
                    <input class="relative align-middle" id="do_map" type="checkbox" v-model="milestone.do_map">
                    <input v-if="milestone.do_map" id="color" class="border-lime-900 colorbox_input" type=color v-model="milestone.color">
                </div>
            </div>
            <div class="grid lg:grid-cols-2">
                <div>
                    <label for="name" class="my-1">Naam:</label><br/>
                    <input type="text" id="name" required v-model="milestone.name">
                </div>

                <div class="mx">
                    <label for="name" class="my-1">Benodigde criteria:</label><br/>
                    <select class="p-2 bg-none" id="is_any" v-model="milestone.is_any">
                        <option :value='true'>Minstens een criterion</option>
                        <option :value='false'>Alle criteria</option>
                    </select>
                </div>
            </div>
            <div>
                <button @click="milestone.save_milestone(elock)" type="button" id="mst_sav">Opslaan</button><br>
                <button @click="milestone.restore_milestone(elock)" type="button" id="mst_ret">Annuleren</button><br>
                <button @click="milestone.remove_milestone(elock, () => {emit('remove', id)})" type="button" id="mst_del">Verwijder</button><br>
            </div>
        </div>
    </form>
</template>
<style>
.milestone_grid_edit {
    display: grid;
    column-gap: 4px;
    grid-template-columns: 12rem 2fr 5rem;
}

.colorbox_input {
    border-radius: 6pt;
    border-width: 2px;
    margin-left:  4px;
    margin-right:  4px;
}

/* For Chrome/Edge */
.colorbox_input::-webkit-color-swatch-wrapper {
  padding: 0;
}
.colorbox_input::-webkit-color-swatch {
  border: none;
  border-radius: 6pt;
}

/* For Firefox */
.colorbox_input::-moz-color-swatch {
  border: none;
  border-radius: 6pt;
}
</style>
