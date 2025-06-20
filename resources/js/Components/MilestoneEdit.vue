<script setup>
import {style_color} from '@/util'
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
    <form>
        <div class="flex flex-row justify-between">
            <div>
                <input type="text" id="name" required v-model="milestone.name">
            </div>
            <div class="flex flex-row">
                <div class="mx-2">
                    <label for="do_map">Laat zien op de kaart</label><br>
                    <input id="do_map" type="checkbox" v-model="milestone.do_map">
                </div>
                <div class="mx-2" v-if="milestone.do_map">
                    <label for="color">Kleur op de kaart</label><br>
                    <input id="color" type=color v-model="milestone.color">
                </div>
            </div>
            <div>
                <button @click="milestone.save_milestone(elock)" type="button" id="mst_sav">Opslaan</button><br>
                <button @click="milestone.restore_milestone(elock)" type="button" id="mst_ret">Annuleren</button><br>
                <button @click="milestone.remove_milestone(elock)" type="button" id="mst_del">Verwijder</button><br>
            </div>
        </div>
    </form>
</template>
