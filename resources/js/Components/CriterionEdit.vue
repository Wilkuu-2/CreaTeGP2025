<script setup>
import VueSelect from 'vue3-select-component';
import {makeMilestoneOptions} from '@/milestones'
const props = defineProps({
    elock: Object,
    criterion: Object,
    name_id_map: Object,
    mid: Number,
});

const criterion = props.criterion;
const name_id_map = props.name_id_map;
const elock = props.elock;
</script>

<template>
    <div>
        <div class="flex flex-row justify-between">
            <div :style="{display: !criterion.noname() ? 'inline' : 'none'}" >
                <label for="name">Omschrijving</label><br/>
                <input type="text" id="name" :disabled="criterion.noname()" :required="criterion.noname()" v-model="criterion.name">
            </div>
            <div class="">
                <label for="operator">Criterion type</label><br/>
                 <select name="operator" @change="criterion.on_operator_change(event)" v-model="criterion.operator">
                    <option value="link">Behaal:</option>
                    <option value="check">Check:</option>
                    <option value="gte">Tenminste:</option>
                    <option value="lte">Hoogstens:</option>
                    <option value="gt">Meer dan:</option>
                    <option value="lt">Minder dan:</option>
                </select>
            </div>
            <div v-if="criterion.constant_type == 'data' || criterion.type != 'none'">
                <div v-if="criterion.type == 'double' || criterion.type == 'int'">
                    <label for="constant">Vergelijk met:</label><br/>
                    <input id="constant" name="constant" type="number" v-model="criterion.constant"/>
                    <input id="unit" name="unit" type="text" v-model="criterion.unit">
                </div>
                <div v-if="criterion.type == 'bool'">
                    <label for="constant">Correcte antwoord</label><br/>
                    <input id="constant" name="constant" type="checkbox" v-model="criterion.constant"/>
                </div>
            </div>
            <div v-if="criterion.constant_type == 'milestone'">
                <label for="constant">Doel:</label><br/>
                <VueSelect
                    v-model="criterion.constant"
                    :options="makeMilestoneOptions(name_id_map, mid)"
                    placeholder="selecteer een mijlpaal"
                />
            </div>
            <div>
                <button @click="criterion.save_criterion(elock)" type="button" id="crit_sav">Opslaan</button><br>
                <button @click="criterion.restore_criterion(elock)" type="button" id="crit_ret">Annuleren</button><br>
                <button @click="criterion.remove_criterion(elock)"  id="crit_del">Verwijder</button><br>
            </div>
        </div>
    </div>
</template>
