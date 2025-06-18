<script setup>
import {router, usePage } from '@inertiajs/vue3';
import {computed} from 'vue'
import VueSelect from 'vue3-select-component'
const page = usePage();


const props = defineProps({
    criterion: Object,
    eval_table: Object,
    id_name_map: Array,
    milestone_id: Number
});
const criterion = props.criterion;
const complete = computed(() => {
    return props.eval_table[props.milestone_id].criteria[criterion.id]
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
                case 'boolean':
                    return criterion.constant ? 'Is waar' : 'Is Onwaar';

            }
        break;
    }
})
const noname = computed(() => criterion.operator == 'link')
</script>

<template>
    <div class="flex flex-row items-center">
     <input type="checkbox" disabled v-model="complete">&nbsp;
     <div class="flex flex-row flex-grow justify-between items-center">
        <div :style="{display: noname ? 'none' : 'inline'}">
            <span>{{criterion.name}}</span>
        </div>
        <div class="">
            <span>{{display_operator}}:&nbsp;</span>
            <template v-if="criterion.constant_type == 'data' || criterion.type != 'none'">
                <template v-if="criterion.type == 'double' || criterion.type == 'int'">
                    <input :id="'fill' + criterion.id" :name="'fill' + criterion.id" type="number" step="any" v-model="criterion.fill.double1"/>
                    <span>&nbsp;/&nbsp;</span>
                </template>
                <template v-else>
                    <input  :id="'fill' + criterion.id" :name="'fill' + criterion.id" type="checkbox" v-model="criterion.fill.bool1"/>
                </template>
            </template>
            <span>{{display_value}}</span>
        </div>
        <div>
        </div>
    </div>
    </div>
</template>
