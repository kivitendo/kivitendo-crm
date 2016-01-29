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
        style: {},
        properties: {}
    };

    Array.prototype.forEach.call(node && node.attributes || [], attr => {
        const {value = '', name = ''} = attr;
        const prop = name && formatPropertyName(name);

        if (prop.includes('padding') || prop.includes('margin') || prop === 'fontSize') {
            const size = value && getSize(value);

            if (size && size.unit) {
                result.style[prop] = size;
            }
        } else if (prop === 'color' || prop === 'backgroundColor') {
            result.style[prop] = normalizeColorValue(value);
        } else if (prop === 'fontStyle') {
            result.style[prop] = (/italic/ig).test(value) ? 'italic' : 'normal';
        } else if (prop === 'fontName') {
            result.style.fontFamily = value;
        } else if (prop === 'fontWeight') {
            result.style[prop] = (/bold/ig).test(value) ? 'bold' : 'normal';
        } else if (prop === 'textUnderlineStyle') {
            result.style.textDecoration = (/none/ig).test(value) ? 'none' : 'underline';
        } else if (prop === 'language') {
            result.properties.lang = value;
        }
    });

    return result;
}