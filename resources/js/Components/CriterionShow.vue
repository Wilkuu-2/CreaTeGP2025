<script setup>
import {id2textcolor,id2color} from "@/util"
import {milestoneTag} from "@/milestones"

const props = defineProps({
    elock: Object,
    criterion: Object,
    name_id_map: Object,
    mid: Number,
});

const criterion = props.criterion;
const name_id_map = props.name_id_map;
const elock = props.elock;
</script>

<template>
    <div class="flex flex-row justify-between">
        <div :style="{display: !criterion.noname() ? 'inline' : 'none'}" >
             {{criterion.name}}
         </div>
        <div class="">
            <span>{{criterion.display_operator(name_id_map)}}:&nbsp;</span>
                <div v-if="criterion.constant_type == 'milestone'"
                    class="inline rounded-md px-1 py-1pt"
                    :style="{'background-color': id2color(Number.parseInt(criterion.constant), name_id_map)}">
                    <a
                        :href="'#' + milestoneTag(criterion.constant)"
                        :style="{'color': id2textcolor(Number.parseInt(criterion.constant), name_id_map)}">
                        {{criterion.display_value(name_id_map)}}
                    </a>
                </div>
            <span v-else>{{criterion.display_value(name_id_map)}}</span>
        </div>
        <div :style="{visibility: elock.isEditing() ? 'hidden' : 'visible'}">
            <button @click="criterion.edit_criterion(elock)" >Edit</button><br>
        </div>
    </div>
</template>
