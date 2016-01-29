const masks = [
    /^([0-9]*[0-9][0-9]*(?:\.[0-9]*)?|0+\.[0-9]*[1-9][0-9]*|\.[0-9]*[1-9][0-9]*)((cm)|(mm)|(in)|(pt)|(pc)|(px))$/,
    /^-?([0-9]+(?:\.[0-9]*)?|\.[0-9]+)(%)$/
];

/**
 *
 * @param val
 * @returns {{value: number, unit: string}}
 * @private
 */
export default function (val) {
    let result = {
        value: 0,
        unit: ''
    };
    let data;

    masks.some((regExp) => data = regExp.exec(val));

    if (data) {
        const value = Number(data[1]);
        const unit = data[2];

        if (!isNaN(value) && unit) {
            result.unit = String(unit).toLowerCase();
            result.value = value;
        }
    }

    return result;
};