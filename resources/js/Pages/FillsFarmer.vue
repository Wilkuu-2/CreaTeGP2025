
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MilestoneFills from '@/Components/MilestoneFills.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { onMounted, onUpdated, ref, computed} from 'vue';
import {make_eval_table} from '@/util';
import "axios";

const props = defineProps({
        milestones: Array,
        criteria: Array,
        fills: Array,
        farmstead: Object
    }
)


const tree = ref([]);
const milestone_name_id = computed(() => {
    var arr = [];
    tree.value.forEach(m => {
        arr.push({id: m.id ,name: m.name, complete:  m.complete})
    })
    return arr;
})

const eval_table = computed(() => {
    return make_eval_table(tree.value);
})

function constructTree(milestones, criteria, fills) {
    var arr = []
    milestones.forEach(m => {
        const m2 = {...m};
        m2.criteria = [];
        arr.push(m2);
    });
    criteria.forEach(criterion => {
        var crit = {...criterion};
        const fill = fills.find((fl) => fl.criterion_id == criterion.id);
        crit.fill = {...fill};
        const milestone = arr.find((mst) => mst.id == criterion.milestone_id);
        milestone.criteria.push(crit);
    });

    arr.forEach(milestone => {
        milestone.criteria.sort(function(c1,c2){return c1.id - c2.id});
    })
    return arr;
}

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
            <MilestoneFills v-for="milestone in tree"
                :id_name_map="milestone_name_id"
                :eval_table="eval_table"
                :milestone="milestone"
            />
            <PrimaryButton type="button" :onclick="bulksubmit" >Opslaan</PrimaryButton>
        </form>
        <!-- <pre>{{props.errors}}</pre> -->
        <!-- <pre>{{elock}}</pre> -->
        <!-- <pre>{{tree}}</pre> -->
    </AppLayout>
</template>
