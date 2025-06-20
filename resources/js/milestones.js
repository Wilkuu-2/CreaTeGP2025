var route = undefined;
import {router} from '@inertiajs/vue3'

export function ms_init(ziggy_route) {
    route = ziggy_route;
}


export const edit_lock_proto = {
    isEditing: function () { return !this.can_edit ||  this.milestone > -1 || this.criterion > -1;  },
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

export function EditLock(can_edit) {
    this.milestone = -1;
    this.criterion = -1;
    this.can_edit = can_edit || false;
}

Object.assign(EditLock.prototype, edit_lock_proto);

export function milestoneTag(id) {
    return `mst${id}`
}
export function criterionTag(id) {
    return `crit${id}`
}

export const milestone_proto = {
    getTag() {
        return milestoneTag(this.id);
    },
    remove_milestone(edit_lock) {
        if (!edit_lock.isEditing()) {
            console.warn("no milestone to delete")
            return
        }
        if (edit_lock.criteria > -1) {
            console.warn("currently editing a criteria, deletion cancelled")
            return
        }
        const id = this.id;
        // Todo: Pop out a confirmation
        console.log("url")
        if (confirm("Ben je zeker dat je de mijlpaal wilt verwijderen?")) {
            edit_lock.release();
            emit('destroy',id);
            if (id != undefined && id > 0) {
                router.delete(
                    route('milestones.destroy', id))
            } else {
                router.get(route('milestones.index'))
            }
        }
    },

    edit_milestone(edit_lock) {
        if (!edit_lock.takeMilestone(this.id)) {
            console.warn("currently editing, editing cancelled")
            return
        }
        // const estone = tree.value.find((mst) => mst.id == id )
        // milestone.value = estone;
    },

    restore_milestone(edit_lock) {
        edit_lock.release()
        router.get(route('milestones.index'))
    },
     save_milestone(edit_lock) {
        if (!edit_lock.isEditing()) {
            console.warn("no milestone to save")
            return
        }
        if (edit_lock.criteria > -1) {
            console.warn("currently editing a criteria, saving cancelled")
            return
        }
        var req = {
            tid: this.tid,
            hold_duration: this.hold_duration,
            needs_approval: this.needs_aproval,
            color: this.color,
            name : this.name ,
            is_any : this.is_any ,
            do_map : this.do_map ,
        };
        edit_lock.release()
        // For now just push the results to the DB
        if (this.id != null && this.id > 1) {
            router.put(route("milestones.update", this.id), req)
        } else {
            console.log(req)
            router.post(route("milestones.store"), req)
        }
    },
    create_criterion(edit_lock) {
        if (!edit_lock.takeCriterion(0, this.id)) {
            console.warn("currently editing, creation cancelled");
            return;
        }
       const criterion = {
                id: 0,
                milestone_id: this.id,
                order : this.criteria.length,
                name : "Meer dan 10 gloobs",
                operator: 'gte',
                constant: "10",
                type: "int",
                constant_type: "data",
                unit: "ton"
        };
        this.criteria.push(criterion);
    },


    destroy_criterion(id) {
        const index = this.criteria.findIndex((cr) => cr.id == id);
        if (index >= 0) {
            this.criteria.splice(index,index);
        }
    },
}

export const operator_table = {
    'gte': "Tenminste",
    'gt': "Meer dan",
    'lte': "Hoogstens",
    'lt': "Minder dan",
    'link': "Behaal",
    'check': "Als",
}

const criterion_proto = {
    cur_crit: {},
    prev_crit: {},
    getTag() {
        return criterionTag(this.id);
    },

    edit_criterion(edit_lock) {
        if (!edit_lock.takeCriterion(this.id)) {
            console.warn("currently editing, editing cancelled")
            return
        }
        // const estone = tree.value.find((mst) => mst.id == id )
        // milestone.value = estone;
    },

    remove_criterion(edit_lock) {
        if (!edit_lock.isEditing()) {
            console.warn("no milestone to delete")
            return
        }
        if (edit_lock.criteria <= -1) {
            console.warn("currently editing a milestone, deletion cancelled")
            return
        }
        const id = this.id;
        if (confirm("Ben je zeker dat je de criterion wilt verwijderen?")) {
            edit_lock.release()
            emit('destroy', id)
            if (id != undefined && id > 0) {
                router.delete(
                    route('criteria.destroy', id))
            }
            else {
                router.get(route('criteria.index'))
            }
        }
    },

    restore_criterion(edit_lock) {
        edit_lock.release()
        router.get(route('milestones.index'))
    },

    on_operator_change(event) {
        switch (this.operator) {
            case 'link':
                this.type = 'none'
                this.constant_type = 'milestone'
                this.constant = '-1'
            break;
            case 'check':
                this.type = 'bool'
                this.constant_type = 'data'
                this.constant = 'true'
            break;
            case 'gt':
            case 'gte':
            case 'lt':
            case 'lte':
                this.type = 'double'
                this.constant_type = 'data'
                this.constant = '0'
            break;
        }
        if (this.type != cur_crit.type && this.id && this.id > 0 && !confirm("Dit verandering kan tot dataverlies lijden, ben je zeker?")){
            this.operator = cur_crit.operator;
            this.type = cur_crit.type;
            this.constant_type = cur_crit.constant_type;
            this.constant = cur_crit.constant;
            return
        }
        this.prev_crit = cur_crit
        this.cur_crit = structuredClone({...criterion})
        Object.setPrototypeOf(cur_crit, criterion_proto);
    },


    save_criterion(edit_lock) {
        if (!edit_lock.isEditing()) {
            console.warn("no criterion to save")
            return
        }
        if (edit_lock.criteria <= -1) {
            console.warn("currently editing a milestone, saving cancelled")
            return
        }
        var req = {
            milestone_id: this.milestone_id,
            operator: this.operator,
            type: this.type,
            constant: this.constant,
            constant_type: this.constant_type,
            name : this.name,
            unit: this.unit,
        };
        edit_lock.release()
        // For now just push the results to the DB
        if (this.id != null && this.id > 1) {
            console.log(req)
            router.put(route("criteria.update", this.id), req)
        } else {
            console.log(req)
            router.post(route("criteria.store"), req)
        }
        // router.visit(route("milestones.index"))
    },

    display_value(id_name_map) {
        switch (this.constant_type) {
            case 'milestone':
                for (const m of id_name_map) {
                    if (this.constant == m.id) {
                        return m.name;
                    }
                }
                return "";
            case 'data':
                switch (this.type) {
                    case 'int':
                    case 'double':
                        return this.constant +" " + this.unit
                    case 'bool':
                        return this.constant ? 'Is waar' : 'Is Onwaar';
                }
            break;
        }
    },

    display_operator() { return operator_table[this.operator];},
    noname() { return this.operator == 'link'},
}

export function constructTree(milestones, criteria, fills) {
    var arr = []
    milestones.forEach(m => {
        const m2 = {...m};
        m2.criteria = [];
        Object.setPrototypeOf(m2,milestone_proto)
        arr.push(m2);
    });
    criteria.forEach(criterion => {
        var crit = {...criterion};
        if (fills !== undefined) {
            const fill = fills.find((fl) => fl.criterion_id == criterion.id);
            crit.fill = {...fill};
        }
        const milestone = arr.find((mst) => mst.id == criterion.milestone_id);
        Object.setPrototypeOf(crit, criterion_proto);
        crit.cur_crit = {...crit};
        Object.setPrototypeOf(crit.cur_crit, criterion_proto);
        milestone.criteria.push(crit);
    });

    arr.forEach(milestone => {
        milestone.criteria.sort(function(c1,c2){return c1.id - c2.id});
    })
    return arr;
}
export function create_milestone(tree,edit_lock,tid) {
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
            order : tree.length,
            name : "Nieuwe mijlpaal",
            is_any : false,
            do_map : true,
            criteria : [],
            is_new: true,
    };
    Object.setPrototypeOf(milestone, milestone_prototype);
    tree.push(milestone);
}

export function destroy_milestone(tree,id) {
    const index = tree.findIndex((mst) => mst.id == id);
    if (index >= 0) {
        tree.splice(index,index);
    }
}

export function makeMilestoneOptions(id_name_map, current_id) {
    const arr = [];
    const current = id_name_map.find((m) => m.id == current_id);

    id_name_map.forEach((m) => {
        if (m.order > (current.order || 0)){
            arr.push({ label: m.name, value: m.id});
        }
    })
    return arr;
}


export function reorder_milestones(tree) {
    var order_tree = []
    tree.forEach((mst, ix) => {
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
