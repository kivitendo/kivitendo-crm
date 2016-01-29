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
        let attrValue = node.attributes['style:width'] && node.attributes['style:width'].value;
        if (attrValue) {
            let size = getSize(attrValue);
            if (size.unit) {
                result.style.width = size;
            }
        }

        attrValue = node.attributes['table:border-model'] && node.attributes['table:border-model'].value;
        if (attrValue) {
            result.style.borderCollapse = (/coll/i).test(attrValue) ? 'collapse' : 'separate';
        }
    }

    return result;
};