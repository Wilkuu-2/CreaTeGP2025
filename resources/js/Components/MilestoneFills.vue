<script setup>
import {router, usePage} from '@inertiajs/vue3';
import CriteriaFill from './CriteriaFill.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {computed} from 'vue'
const page = usePage();
const props = defineProps({
    milestone: Object,
    eval_table: Object,
    id_name_map: Array,
})

const milestone = props.milestone;
const complete_milestone = computed(() => {
  return props.eval_table[milestone.id].milestone
});

function style_color(mst) {
    return  "width: 2em; height: 1.7em; background-color:" + mst.color + ";";
}
</script>


<template>
<div class="m-5">
    <div class="border rounded-b border-lime-500 p-1">
        <div class="flex flex-row justify-between items-center">
            <div class=" flex flex-row items-center">
                <input style="display: inline;" type="checkbox" disabled v-model="complete_milestone">&nbsp;
                <span  class="border border-black-600 rounded-b p-1" :style="style_color(milestone)"></span>
                {{milestone.name}}
             </div>
            <div class="flex flex-row">
                <label> Op de kaart:</label>
                <input class="m-1" type='checkbox' disabled v-model='milestone.do_map'>
            </div>
        </div>
    </div>
    <div class="ml-4">
        <div v-for="crit in milestone.criteria" class="border rounded-b border-lime-500 p-1 my-2" >
                <CriteriaFill
                    :criterion="crit"
                    :id_name_map="props.id_name_map"
                    :eval_table="props.eval_table"
                    :milestone_id="milestone.id"
                />
        </div>
    </div>
</div>
</template>
