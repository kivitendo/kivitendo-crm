import JsFile from 'JsFile';
import getSize from './getSize';
const {normalizeColorValue} = JsFile.Engine;

/**
 *
 * @param data
 * @return {Object}
 * @private
 */
export default function (data) {
    const result = {
        width: {
            value: 0,
            unit: 'pt'
        },
        color: 'none',
        style: 'none'
    };

    if (data && data !== 'none') {
        const [sizeData, style, color] = data.split(' ');

        if (sizeData && style && color) {
            let size = getSize(sizeData);

            if (size.unit) {
                result.width = size;
            }

            result.style = style;
            result.color = normalizeColorValue(color);
        }
    }

    return result;
};