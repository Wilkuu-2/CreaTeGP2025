<script setup>
import {create_milestone, EditLock, constructTree, reorder_milestones, ms_init} from '@/milestones'
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {ref} from 'vue'
import MilestoneList from '@/Components/MilestoneList.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {style_color} from '@/util'
import MilestoneEdit from '@/Components/MilestoneEdit.vue';
import MilestoneShow from '@/Components/MilestoneShow.vue';
import CriterionShow from '@/Components/CriterionShow.vue';
import CriterionEdit from '@/Components/CriterionEdit.vue';
import DragIcon from '@/Components/DragIcon.vue'

const props = defineProps({
    milestones: Object,
    criteria: Object,
    //fills: Object,
    can_edit: Boolean,
    tid: Number,
});

ms_init(route);
const elock = ref(new EditLock(props.can_edit));
const tree = ref(constructTree(props.milestones, props.criteria))
const tid = props.tid || props.user?.current_team || 0;
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
            <div class="flex flex-row justify-around m-5" v-if="props.can_edit">
               <PrimaryButton :disabled="elock.isEditing()" @click="create_milestone(tree, edit_lock,tid)">
                    Maak een mijlpaal aan!
                </PrimaryButton>
               <PrimaryButton :disabled="elock.isEditing()" @click="reorder_milestones(tree)">
                    Orde opslaan
                </PrimaryButton>
            </div>
            <MilestoneList v-model="tree" :elock="elock" :can_edit="props.can_edit">
                    <template #milestones="{milestone, elock, name_id_map}">
                        <template v-if="elock.matchMilestone(milestone.id)">
                            <MilestoneEdit :milestone="milestone" :elock="
                            elock" :name_id_map="name_id_map">
                                <template #corner>
                                    <div class="milestone_drag">
                                        <DragIcon/>
                                    </div>
                                </template>
                            </MilestoneEdit>
                        </template>
                        <template v-else>
                            <MilestoneShow :milestone="milestone" :elock="
                            elock" :name_id_map="name_id_map"/>
                        </template>
                </template>
                <template #criteria="{criterion, elock, name_id_map, mid}">
                <template v-if="elock.matchCriterion(criterion.id)">
                        <CriterionEdit
                            :criterion="criterion"
                            :elock="elock"
                            :name_id_map="name_id_map"
                            :mid="mid"/>
                </template>
                <template v-else>
                    <CriterionShow
                            :criterion="criterion"
                            :elock="elock"
                            :name_id_map="name_id_map"
                            :mid="mid"/>
                </template>
                </template>
            </MilestoneList>
        </div>
        <!-- <pre>{{props.errors}}</pre> -->
        <!-- <pre>{{elock}}</pre> -->
        <!-- <pre>{{tree}}</pre> -->
    </AppLayout>
</template>
