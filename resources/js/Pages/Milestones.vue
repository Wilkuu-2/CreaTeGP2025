
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MilestoneDisplay from '@/Components/MilestoneDisplay.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { onMounted, onUpdated, ref } from 'vue';
import draggable from 'vuedraggable'


const edit_lock = {
    milestone: -1,
    criterion: -1,
    isEditing: function () { return this.milestone > -1 || this.criterion > -1 },
    takeCriterion: function (criterion, milestone) {
        if (this.isEditing()) {
            return false;
        }
        this.criterion = criterion;
        this.milestone = milestone;
        // this.criterion = criterion.id;
        // this.milestone = criterion.milestone_id;
        return true;
    },
    takeMilestone: function (milestone) {
        if (this.isEditing() || this.criterion > -1) {
            return false;
        }
        this.criterion = -1;
        this.milestone = milestone // When moving this to the proper component, change this.
        // this.milestone = milestone.id;
        return true;
    },
    release: function () {
        this.criterion = -1
        this.milestone = -1
    },
    matchMilestone: function(mid) {
        return this.criterion < 0 && this.milestone == mid;
    },
    matchCriterion: function(cid) {
        return this.criterion == cid;
    }

};
const elock = ref(edit_lock);

var drag = false;
const page = usePage();
const props = defineProps({
        milestones: Array,
        criteria: Array,
        new_milestone: Boolean,
        new_criteria: Boolean,
        editing_milestone: Number,
        editing_criterion: Number,
        tid: Number,
    }
)
var new_milestone = ref(props.new_milestone);
var milestone = ref(null);

function reorder_milestones() {
    var order_tree = []
    tree.value.forEach((mst, ix) => {
        var mo = {
            id: mst.id,
            criteria: []
        };
        order_tree.push(mo);
        mst.criteria.forEach(crit => {
            mo.criteria.push(crit.id)
        });
    });

    router.patch(
        route('reorder'),{
            tid: props.tid,
            order: order_tree,
        }
    );
}




function create_milestone() {
    if (!edit_lock.takeMilestone(0)) {
        console.warn("currently editing, creation cancelled");
        return;
    }
    const milestone  = {
            id: 0,
            tid : props.tid,
            color : '#FFFFFF',
            hold_duration: 0,
            needs_aproval: false,
            order : tree.value.length,
            name : "Nieuwe mijlpaal",
            is_any : false,
            do_map : true,
            criteria : [],
            is_new: true,
    };
    tree.value.push(milestone);
}

const original_tree = ref([]);
const tree = ref([]);

function constructTree(milestones, criteria) {
    var arr = []
    milestones.forEach(m => {
        const m2 = {...m};
        m2.criteria = [];
        arr.push(m2);
    });
    criteria.forEach(criterion => {
        milestone = arr.find((mst) => mst.id == criterion.milestone_id );
        milestone.criteria.push({...criterion});
    });

    arr.forEach(milestone => {
        milestone.criteria.sort(function(c1,c2){return c1.id - c2.id});
    })
    return arr;
}

function destroy_milestone(id) {
    const index = tree.value.findIndex((mst) => mst.id == id);
    if (index >= 0) {
        tree.value.splice(index,index);
    }
}


onMounted(() => {
    original_tree.value = constructTree(props.milestones, props.criteria);
    tree.value = constructTree(props.milestones, props.criteria);
    if (new_milestone === true) {
        console.error("grrr")
        create_milestone();
        new_milestone = false;
    } else if (props.editing_milestone) {
        edit_milestone(tree.find((m) => m.id == props.editing_milestone));
    }
})

onUpdated(() => {
    original_tree.value = constructTree(props.milestones, props.criteria);
    tree.value = constructTree(props.milestones, props.criteria);
})
</script>

<template>
    <AppLayout title="Doelen" >
        <template #header>
            <div class="flex justify-between">
                <h2 class="flex-0 font-semibold text-xl text-gray-800 leading-tight">
                    Doelen voor: {{$page.props.auth.user.current_team.name}}
                </h2>
            </div>
        </template>

    <div>
        <draggable
                v-model="tree"
                group="milestones"
                :disabled="elock.isEditing()"
                @start="drag=true"
                @end="drag=false"
                item-key="id"
                >
                <template #item="{element}">
                    <MilestoneDisplay
                        :elock="elock"
                        :milestone="element"
                        />
                </template>
        </draggable>
        <div class="flex flex-row justify-around m-5 ">
           <PrimaryButton :onclick="create_milestone">Maak een mijlpaal aan!</PrimaryButton>
           <PrimaryButton :onclick="reorder_milestones">Orde opslaan</PrimaryButton>
        </div>
    </div>
    <pre>{{props.errors}}</pre>
    <pre>{{elock}}</pre>
    <pre>{{tree}}</pre>
    </AppLayout>
</template>
