<script setup>
import {create_milestone, EditLock, constructTree, reorder_milestones, destroy_milestone,destroy_criterion, ms_init} from '@/milestones'
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {ref} from 'vue'
import MilestoneList from '@/Components/MilestoneList.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {style_color} from '@/util'
import MilestoneEdit from '@/Components/MilestoneEdit.vue';
import MilestoneShow from '@/Components/MilestoneShow.vue';
import CriterionShow from '@/Components/CriterionShow.vue';
import CriterionEdit from '@/Components/CriterionEdit.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
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
               <PrimaryButton :disabled="elock.isEditing()" @click="create_milestone(tree, elock ,tid)">
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
                            elock" :name_id_map="name_id_map" @remove="destroy_milestone(tree, milestone.id)">
                                <template #corner>
                                    <div class="milestone_drag">
                                        <DragIcon/>
                                    </div>
                                </template>
                            </MilestoneEdit>
                        </template>
                        <template v-else>
                            <MilestoneShow :milestone="milestone" :elock="elock"
                            :name_id_map="name_id_map">
                                <template #corner>
                                    <div class="milestone_drag">
                                        <DragIcon/>
                                    </div>
                                </template>
                            </MilestoneShow>
                        </template>
                </template>
                <template #criteria="{criterion, elock, name_id_map, mid}">
                    <template v-if="elock.matchCriterion(criterion.id)">
                            <CriterionEdit
                                :criterion="criterion"
                                :elock="elock"
                                :name_id_map="name_id_map"
                                :mid="mid"
                                @remove="destroy_criterion(tree,mid,criterion.id)"/>
                    </template>
                    <template v-else>
                        <CriterionShow
                                :criterion="criterion"
                                :elock="elock"
                                :name_id_map="name_id_map"
                                :mid="mid"/>
                    </template>
                </template>
                <template #footer="{elock,milestone,name_id_map}">
                    <div v-if="!elock.isEditing()" class="ml-4">
                            <button @click="milestone.create_criterion(elock)">Maak een criterion</button>
                    </div>
                </template>
            </MilestoneList>
        </div>
        <!-- <pre>{{props.errors}}</pre> -->
        <!-- <pre>{{elock}}</pre> -->
        <!-- <pre>{{tree}}</pre> -->
    </AppLayout>
</template>
