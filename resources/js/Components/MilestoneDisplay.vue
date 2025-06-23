
<script setup>
import {router, usePage} from '@inertiajs/vue3';
import Criteria from './Criteria.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
const page = usePage();
const props = defineProps({
    milestone: Object,
    can_edit: Boolean,
    id_name_map: Array,
    elock: Object
})

const emit = defineEmits(['destroy'])
const edit_lock = props.elock;
const milestone = props.milestone;

function style_color(mst) {
    return  "width: 2em; background-color:" + mst.color + ";";
}
function edit_milestone() {
    if (!edit_lock.takeMilestone(milestone.id)) {
        console.warn("currently editing, editing cancelled")
        return
    }
    // const estone = tree.value.find((mst) => mst.id == id )
    // milestone.value = estone;
}

function remove_milestone() {
    if (!edit_lock.isEditing()) {
        console.warn("no milestone to delete")
        return
    }
    if (edit_lock.criteria > -1) {
        console.warn("currently editing a criteria, deletion cancelled")
        return
    }
    const id = milestone.id;
    // Todo: Pop out a confirmation
    console.log("url")
    if (confirm("Ben je zeker dat je de mijlpaal wilt verwijderen?")) {
        edit_lock.release();
        emit('destroy',id);
        if (id != undefined && id > 0) {
            router.delete(
                route('milestones.destroy', id))
        } else {
            router.get(route('milestones.index'))
        }
    }

}

function restore_milestone() {
    edit_lock.release()
    router.get(route('milestones.index'), {tid: page.props.tid})
}

function create_criterion() {
    if (!edit_lock.takeCriterion(0, milestone.id)) {
        console.warn("currently editing, creation cancelled");
        return;
    }
   const criterion = {
            id: 0,
            milestone_id: milestone.id,
            order : milestone.criteria.length,
            name : "Meer dan 10 gloobs",
            operator: 'gte',
            constant: "10",
            type: "int",
            constant_type: "data",
            unit: "ton"
    };
    milestone.criteria.push(criterion);
}


function destroy_criterion(id) {
    const index = milestone.criteria.findIndex((cr) => cr.id == id);
    if (index >= 0) {
        milestone.criteria.splice(index,index);
    }
}

function save_milestone() {
    if (!edit_lock.isEditing()) {
        console.warn("no milestone to save")
        return
    }
    if (edit_lock.criteria > -1) {
        console.warn("currently editing a criteria, saving cancelled")
        return
    }
    var req = {
        tid: milestone.tid,
        hold_duration: milestone.hold_duration,
        needs_approval: milestone.needs_aproval,
        color: milestone.color,
        name : milestone.name ,
        is_any : milestone.is_any ,
        do_map : milestone.do_map ,
    };
    edit_lock.release()
    // For now just push the results to the DB
    if (milestone.id != null && milestone.id > 1) {
        router.put(route("milestones.update", milestone.id), req)
    } else {
        console.log(req)
        router.post(route("milestones.store"), req)
    }
}
</script>


<template>
<div class="m-5">
    <div class="border rounded-b border-lime-500 p-1">
        <form v-if="edit_lock.matchMilestone(milestone.id)">
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
                    <button @click="save_milestone" type="button" id="mst_sav">Opslaan</button><br>
                    <button @click="restore_milestone" type="button" id="mst_ret">Annuleren</button><br>
                    <button @click="remove_milestone" type="button" id="mst_del">Verwijder</button><br>
                </div>
            </div>
        </form>
        <div v-else class="flex flex-row justify-between">
            <div class=" flex flex-row">
                    <div class="mdrag">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="8" cy="4" r="1" transform="rotate(90 8 4)" stroke="black" stroke-width="2"/>
                            <circle cx="16" cy="4" r="1" transform="rotate(90 16 4)" stroke="black" stroke-width="2"/>
                            <circle cx="8" cy="12" r="1" transform="rotate(90 8 12)" stroke="black" stroke-width="2"/>
                            <circle cx="16" cy="12" r="1" transform="rotate(90 16 12)" stroke="black" stroke-width="2"/>
                            <circle cx="8" cy="20" r="1" transform="rotate(90 8 20)" stroke="black" stroke-width="2"/>
                            <circle cx="16" cy="20" r="1" transform="rotate(90 16 20)" stroke="black" stroke-width="2"/>
                        </svg>
                    </div>
                <span  class="border border-black-600 rounded-b" :style="style_color(milestone)"></span> {{milestone.name}}
             </div>
            <div class="flex flex-row">
                <label> Op de kaart:</label>
                <input class="m-1" type='checkbox' disabled v-model='milestone.do_map'>
            </div>
            <div :style="{visibility: elock.isEditing() ? 'hidden' : 'visible'}"  class="flex flex-row">
                <button @click="edit_milestone" >Edit</button><br>
            </div>
        </div>
    </div>
    <div class="ml-4">
        <div v-for="crit in milestone.criteria" class="border rounded-b border-lime-500 p-1 my-2" >
                <Criteria
                    :elock="elock"
                    :criterion="crit"
                    :id_name_map="props.id_name_map"
                    :milestone_id="milestone.id"
                    @destroy="destroy_criterion"
                />
        </div>
    </div>
    <div v-if="!edit_lock.isEditing()" class="ml-4">
            <SecondaryButton @click="create_criterion">Maak een criterion</SecondaryButton>
    </div>
</div>
</template>
