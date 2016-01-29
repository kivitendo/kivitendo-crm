import getSize from './getSize';

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

    if (node) {
        const attr = node.attributes['style:column-width'];
        const size = attr && getSize(attr.value);

        if (size && size.unit) {
            result.style.width = size;
        }
    }

    return result;
};