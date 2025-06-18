<script setup>
import {router, usePage } from '@inertiajs/vue3';
import {computed} from 'vue'
import VueSelect from 'vue3-select-component'
const page = usePage();



const props = defineProps({
    criterion: Object,
    elock: Object,
    id_name_map: Array,
    milestone_id: Number
});

const edit_lock = props.elock;
const criterion = props.criterion;
const emit = defineEmits(['destroy']);
var prev_crit = structuredClone({ ...criterion})
var cur_crit = structuredClone(prev_crit)
const options = computed(() => {
    const arr = [];
    props.id_name_map.forEach((m) => {
        if (m.id !== props.milestone_id){
            arr.push({ label: m.name, value: m.id});
        }
    })
    return arr;
});
const noname = computed(() => criterion.operator == 'link')
const canedit = computed(() => !edit_lock.isEditing())

const display_value = computed(() => {
    switch (criterion.constant_type) {
        case 'milestone':
            for (const m of props.id_name_map) {
                if (criterion.constant == m.id) {
                    return m.name;
                }
            }
            return "";
        case 'data':
            switch (criterion.type) {
                case 'int':
                case 'double':
                    return criterion.constant +" " + criterion.unit
                case 'bool':
                    return criterion.constant ? 'Is waar' : 'Is Onwaar';

            }
        break;
    }
})
const operator_table = {
    'gte': "Tenminste",
    'gt': "Meer dan",
    'lte': "Hoogstens",
    'lt': "Minder dan",
    'link': "Behaal",
    'check': "Als",
}
const display_operator = computed(() => {
    return operator_table[criterion.operator];
});

function edit_criterion() {
    if (!edit_lock.takeCriterion(criterion.id)) {
        console.warn("currently editing, editing cancelled")
        return
    }
    // const estone = tree.value.find((mst) => mst.id == id )
    // milestone.value = estone;
}

function remove_criterion() {
    if (!edit_lock.isEditing()) {
        console.warn("no milestone to delete")
        return
    }
    if (edit_lock.criteria <= -1) {
        console.warn("currently editing a milestone, deletion cancelled")
        return
    }
    const id = criterion.id;
    if (confirm("Ben je zeker dat je de criterion wilt verwijderen?")) {
        edit_lock.release()
        emit('destroy', id)
        if (id != undefined && id > 0) {
            router.delete(
                route('criteria.destroy', id))
        }
        else {
            router.get(route('criteria.index'), {tid: page.props.tid})
        }
    }

}

function restore_criterion() {
    edit_lock.release()
    router.get(route('milestones.index'), {tid: page.props.tid})
}

function on_operator_change(event) {
    switch (criterion.operator) {
        case 'link':
            criterion.type = 'none'
            criterion.constant_type = 'milestone'
            criterion.constant = '-1'
        break;
        case 'check':
            criterion.type = 'bool'
            criterion.constant_type = 'data'
            criterion.constant = 'true'
        break;
        case 'gt':
        case 'gte':
        case 'lt':
        case 'lte':
            criterion.type = 'double'
            criterion.constant_type = 'data'
            criterion.constant = '0'
        break;
    }
    if (criterion.type != cur_crit.type && criterion.id && criterion.id > 0 && !confirm("Dit verandering kan tot dataverlies lijden, ben je zeker?")){
        criterion.operator = cur_crit.operator;
        criterion.type = cur_crit.type;
        criterion.constant_type = cur_crit.constant_type;
        criterion.constant = cur_crit.constant;
        return
    }
    prev_crit = cur_crit
    cur_crit = structuredClone({...criterion})
}


function save_criterion() {
    if (!edit_lock.isEditing()) {
        console.warn("no criterion to save")
        return
    }
    if (edit_lock.criteria <= -1) {
        console.warn("currently editing a milestone, saving cancelled")
        return
    }
    var req = {
        milestone_id: criterion.milestone_id,
        operator: criterion.operator,
        type: criterion.type,
        constant: criterion.constant,
        constant_type: criterion.constant_type,
        name : criterion.name,
        unit: criterion.unit,
    };
    edit_lock.release()
    // For now just push the results to the DB
    if (criterion.id != null && criterion.id > 1) {
        console.log(req)
        router.put(route("criteria.update", criterion.id), req)
    } else {
        console.log(req)
        router.post(route("criteria.store"), req)
    }
    // router.visit(route("milestones.index"))
}
</script>
<template>
    <div v-if="edit_lock.matchCriterion(criterion.id)">
        <div class="flex flex-row justify-between">
            <div :style="{display: !noname ? 'inline' : 'none'}" >
                <label for="name">Omschrijving</label><br/>
                <input type="text" id="name" :disabled="noname" :required="noname" v-model="criterion.name">
            </div>
            <div class="">
                <label for="operator">Criterion type</label><br/>
                 <select name="operator" @change="on_operator_change" v-model="criterion.operator">
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
                    :options="options"
                    placeholder="selecteer een mijlpaal"
                />
            </div>
            <div>
                <button @click="save_criterion" type="button" id="crit_sav">Opslaan</button><br>
                <button @click="restore_criterion" type="button" id="crit_ret">Annuleren</button><br>
                <button @click="remove_criterion"  id="crit_del">Verwijder</button><br>
            </div>
        </div>
    </div>
    <div v-else class="flex flex-row justify-between">
        <div :style="{display: !noname ? 'inline' : 'none'}" >
             {{criterion.name}}
         </div>
        <div class="">
            <span>{{display_operator}}:&nbsp;</span>
            <span>{{display_value}}</span>
        </div>
        <div :style="{visibility: elock.isEditing() ? 'hidden' : 'visible'}">
            <button @click="edit_criterion" >Edit</button><br>
        </div>
    </div>
</template>

<style lang="css">

.display_select {
    padding: 2px;
    width: fit-content;
    margin-right: 1em;
    background-image: none;
    border: none;
}
</style>
