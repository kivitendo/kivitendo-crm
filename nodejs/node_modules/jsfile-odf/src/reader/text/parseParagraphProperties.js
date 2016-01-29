import JsFile from 'JsFile';
import getSize from './getSize';
const {formatPropertyName, normalizeColorValue} = JsFile.Engine;

/**
 *
 * @param node
 * @return {Object}
 * @private
 */
export default function (node) {
    let result = {
        style: {}
    };

    Array.prototype.forEach.call(node && node.attributes || [], attr => {
        const {value = '', name = ''} = attr;
        const prop = name && formatPropertyName(name);
        let size;

        if (prop.includes('padding') || prop.includes('margin')) {
            size = value && getSize(value);

            if (size.unit) {
                result.style[prop] = size;
            }
        } else if (prop === 'backgroundColor') {
            result.style[prop] = normalizeColorValue(value);
        } else if (prop.includes('border')) {
            const borderData = value.split(' ');
            size = borderData[0] && getSize(borderData[0]);

            if (borderData.length === 3 && size.unit) {
                /**
                 * @description minimal visible size for borders in inches
                 * @type {number}
                 */
                const minimalVisibleSize = 0.02;

                /**
                 * Browser can't render too thin borders, as 0.0008in.
                 * So, here is the normalization of border width if it's not a 0.
                 */
                if (size.value && size.value < minimalVisibleSize) {
                    size.value = minimalVisibleSize;
                }

                result.style[`${prop}Width`] = size;
                result.style[`${prop}Style`] = borderData[1];
                result.style[`${prop}Color`] = normalizeColorValue(borderData[2]);
            }
        } else if (prop === 'writingMode') {
            result.style.direction = (/rl/i).test(value) ? 'rtl' : 'ltr';
        } else if (prop === 'textAlign') {
            const align = (/center|left|right/i).exec(value);

            if (align && align[0]) {
                result.style[prop] = align[0];
            }
        }
    });

    return result;
}