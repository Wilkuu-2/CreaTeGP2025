<script setup>
import draggable from 'vuedraggable';
draggable.compatConfig = { MODE: 3 };
import { Link, router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { onMounted, onUpdated, ref, computed} from 'vue';

const props = defineProps({
    can_edit: Boolean,
    elock: Object,
});

const tree = defineModel();
const elock = props.elock;


// onMounted(() => {
//     tree = constructTree(props.milestones, props.criteria, props.fills);
// })
const name_id_map = computed(() => {
    var arr = [];
    tree.value.forEach(m => {
        arr.push({id: m.id ,name: m.name, order: m.order, color: m.color})
    })
    return arr;
})
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center">
        <draggable
            v-model="tree"
            :disabled="elock.isEditing()"
            group="milestones"
            handle=".milestone_drag"
            @start="drag=true"
            @end="drag=false"
            item-key="id"
            style="min-width: 80%;"
        >
            <template #item="{element}">
                <div :id="element.getTag()" class="mcard p-4 mt-4 border border-green-800 bg-white rounded-xl">
                    <div class="milestone_inner mb-2">
                        <slot name="milestones" :milestone="element" :elock="elock" :name_id_map="name_id_map"/>
                    </div>
                    <hr class="mt-1 mb-1 border-green-600" />

                    <div class="criterion_container">
                         <div v-for="criterion in element.criteria" :id="criterion.getTag()" class="criterion_inner py-1">
                            <slot name="criteria" :criterion="criterion" :elock="elock" :mid="element.id" :name_id_map="name_id_map" />
                         </div>
                    </div>
                    <slot name="footer"
                        :milestone="element"
                        :name_id_map="name_id_map"
                        :elock="elock"
                        />
                </div>
            </template>
        </draggable>
    </div>
</template>
