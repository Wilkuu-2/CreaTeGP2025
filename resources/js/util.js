export function evaluate_criterion(c, evals) {
    switch (c.operator) {
        case 'link':
            return evals[parseInt(c.constant)].milestone;
        case 'gte':
            return c.fill.double1 >= Number.parseFloat(c.constant);
        case 'gt':
            return c.fill.double1 > Number.parseFloat(c.constant);
        case 'lte':
            return c.fill.double1 <= Number.parseFloat(c.constant);
        case 'lt':
            return c.fill.double1 <= Number.parseFloat(c.constant);
        case 'checkbox':
            const val = typeof c.constant == 'string' ? c.constant : c.constant.toString();
            return c.fill.bool1.toString() == val;
    }
}

export function evaluate_milestone(mst, evals) {
    for (var i = 0; i < mst.criteria.length; i++) {
        const crit = mst.criteria[i];
        const comp = evaluate_criterion(crit, evals);
        evals[mst.id].criteria[crit.id] = comp;

        if (mst.is_any){
            if (comp) {
                return true;
            }
        } else {
            if (!comp) {
                return false;
            }
        }
    }
    return !mst.is_any;
}

export function make_eval_table(tree) {
    const evals = {}
    tree.slice().reverse().forEach(mst => {
        evals[mst.id] = {
                milestone: false,
                criteria: {},
            };

        evals[mst.id].milestone = evaluate_milestone(mst, evals);
    });

    return evals;
}

export function latlng_to_gjson_point(latlng) {
    if (latlng != undefined) {
        return { type: "Point", coordinates: [latlng.lng, latlng.lat] };
    }
    return null
}

export function gjson_point_to_latlng(gjson) {
    var gj = gjson
    if (typeof gjson == 'string') {
        gj = JSON.parse(gjson)
        console.log("got json string")
    }
    if (gj !== null || gj !== undefined) {
        return {lng: gj['coordinates'][0], lat: gj['coordinates'][1] }
    }
    console.error("gjson got a bad value")
    return null
}
