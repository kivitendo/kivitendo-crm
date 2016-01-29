import JsFile from 'JsFile';
import getSize from './getSize';
import parseBorderStyle from './parseBorderStyle';
const {formatPropertyName} = JsFile.Engine;

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

        if (prop.includes('border')) {
            const {style, width, color} = parseBorderStyle(value) || {};
            result.style[prop + 'Style'] = style;
            result.style[prop + 'Width'] = width;
            result.style[prop + 'Color'] = color;
        } else if (prop === 'padding') {
            const size = value && getSize(value);
            if (size && size.unit) {
                result.style[prop] = size;
            }
        }
    });

    return result;
};