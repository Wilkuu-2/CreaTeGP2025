
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MilestoneList from '@/Components/MilestoneList.vue';
import CriteriaFill from '@/Components/CriteriaFill.vue';
import MilestoneShow from '@/Components/MilestoneShow.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { onMounted, onUpdated, ref, computed} from 'vue';
import {make_eval_table} from '@/util';
import "axios";
import { EditLock, constructTree} from '@/milestones';

const elock = new EditLock(false);

const props = defineProps({
        milestones: Array,
        criteria: Array,
        fills: Array,
        farmstead: Object
    }
)


const tree = ref([]);
const eval_table = computed(() => {
    return make_eval_table(tree.value);
})

onMounted(() => {
    tree.value = constructTree(props.milestones, props.criteria, props.fills);
})

const bulksubmit = async () => {
    for (var mst of tree.value) {
        for (var crit of mst.criteria) {
            console.log(crit);
            const fill = crit.fill;

            axios.put(route('fills.update', [fill.farmstead_id, fill.criterion_id]), fill);

            // const res = await fetch(route('fills.update', [fill.farmstead_id, fill.criterion_id]),
            //     {
            //     method: "PUT",
            //     body: JSON.stringify(fill),
            //     headers: { "Content-Type": "application/json"},
            // });
        }
    }
}


</script>


<template>
    <AppLayout title="Data" >
        <template #header>
            <div class="flex justify-between">
                <h2 class="flex-0 font-semibold text-xl text-gray-800 leading-tight">
                    Informatie van boerderij {{props.farmstead.name}} voor {{$page.props.auth.user.current_team.name}}
                </h2>
            </div>
        </template>

        <form>
            <MilestoneList v-model="tree" :elock="elock" :can_edit="false">
                <template #milestones="{milestone, elock, name_id_map}">
                        <MilestoneShow :milestone="milestone" :elock="
                        elock" :name_id_map="name_id_map">
                            <template #corner={milestone}>
                                <input style="display: inline;" type="checkbox" disabled :checked="eval_table[milestone.id].milestone">&nbsp;
                            </template>
                        </MilestoneShow>
                </template>
                <template #criteria="{criterion, elock, name_id_map, mid}">
                    <CriteriaFill
                        :criterion="criterion"
                        :id_name_map="name_id_map"
                        :eval_table="eval_table"
                        :milestone_id="mid"
                    />
                </template>
            </MilestoneList>
            <PrimaryButton type="button" :onclick="bulksubmit" >Opslaan</PrimaryButton>
        </form>
        <!-- <pre>{{props.errors}}</pre> -->
        <!-- <pre>{{elock}}</pre> -->
        <!-- <pre>{{tree}}</pre> -->
    </AppLayout>
</template>
