<script setup>
import {router, usePage } from '@inertiajs/vue3';
const page = usePage();
const props = defineProps({
    criterion: Object,
    elock: Object,
});

const edit_lock = props.elock;
const criterion = props.criterion;
const emit = defineEmits(['destroy']);

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
        router.put(route("criteria.update", criterion.id), req)
    } else {
        console.log(req)
        router.post(route("criteria.store"), req)
    }
    router.visit(route("milestones.index"))
}
</script>
<template>
    <form v-if="edit_lock.matchCriterion(criterion.id)">
        <div class="flex flex-row justify-between">
            <div>
                <input type="text" id="name" required v-model="criterion.name">
            </div>
            <div class="flex flex-row">
            </div>
            <div>
                <button @click="save_criterion" id="crit_sav">Opslaan</button><br>
                <button @click="restore_criterion" id="crit_ret">Annuleren</button><br>
                <button @click="remove_criterion" id="crit_del">Verwijder</button><br>
            </div>
        </div>
    </form>
    <div v-else class="flex flex-row justify-between">
        <div class=" flex flex-row">
             {{criterion.name}}
         </div>
        <div class="flex flex-row">
            <button @click="edit_criterion" >Edit</button><br>
        </div>
    </div>
</template>
