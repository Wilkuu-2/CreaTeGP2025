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
    var answer = !mst.is_any;

    for (var i = 0; i < mst.criteria.length; i++) {
        const crit = mst.criteria[i];
        const comp = evaluate_criterion(crit, evals);
        evals[mst.id].criteria[crit.id] = comp;

        if (mst.is_any){
            if (comp) {
                answer = true;
            }
        } else {
            if (!comp) {
                answer = false;
            }
        }
    }
    return answer;
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

export function style_color(mst) {
    return  "background-color:" + mst.color + ";";
}

export function id2color(id, id_name_map) {
    const color = id_name_map.find((mst) => id == mst.id)?.color || "#FFFFFF";
    return color == '#FFFFFF' ? '#BABABA' : color;
}

// Source: https://stackoverflow.com/questions/3942878/how-to-decide-font-color-in-white-or-black-depending-on-background-color#answer-41491220
export function colorIsDarkAdvanced(bgColor) {
  let color = (bgColor.charAt(0) === '#') ? bgColor.substring(1, 7) : bgColor;
  let r = parseInt(color.substring(0, 2), 16); // hexToR
  let g = parseInt(color.substring(2, 4), 16); // hexToG
  let b = parseInt(color.substring(4, 6), 16); // hexToB
  let uicolors = [r / 255, g / 255, b / 255];
  let c = uicolors.map((col) => {
    if (col <= 0.03928) {
      return col / 12.92;
    }
    return Math.pow((col + 0.055) / 1.055, 2.4);
  });
  let L = (0.2126 * c[0]) + (0.7152 * c[1]) + (0.0722 * c[2]);
  return L <= 0.179;
}

export function id2textcolor(id, id_name_map) {
    const color = id2color(id, id_name_map);
    return colorIsDarkAdvanced(color) ? '#FFFFFF' : '#000000';
}
