import JsFile from 'JsFile';
import parseListProperties from './parseListProperties';
import parseTableProperties from './parseTableProperties';
import parseTableColumnProperties from './parseTableColumnProperties';
import parseTableCellProperties from './parseTableCellProperties';
import parseTableRowProperties from './parseTableRowProperties';
import parseParagraphProperties from './parseParagraphProperties';
import parseTextProperties from './parseTextProperties';
const {merge} = JsFile.Engine;
const defaultStyleNodeName = 'default-style';
const parsers = {
    'table-properties': {
        name: 'table',
        selector: 'table',
        exec: parseTableProperties
    },
    'table-column-properties': {
        name: 'tableColumn',
        selector: 'td',
        exec: parseTableColumnProperties
    },
    'table-cell-properties': {
        name: 'tableCell',
        selector: 'td',
        exec: parseTableCellProperties
    },
    'table-row-properties': {
        name: 'tableRow',
        selector: 'tr',
        exec: parseTableRowProperties
    },
    'paragraph-properties': {
        name: 'paragraph',
        selector(styleName) {
            let className = '';

            if (styleName) {
                className = `.${styleName}`;
            }

            return `p${className}`;
        },

        exec: parseParagraphProperties
    },
    'text-properties': {
        name: 'text',
        selector(styleName) {
            let className = '';

            if (styleName) {
                className = `.${styleName}`;
            }

            return `span${className}, ${className || 'p'} span`;
        },

        exec: parseTextProperties
    }
};
const forEach = [].forEach;

function readNodes (i, length, nodes, result, resolve, reject) {
    let size = i + 100;

    if (size > length) {
        size = length;
    }

    for (; i < size; i++) {
        let styleName;
        let isNew = true;
        const node = nodes[i];
        const {localName, attributes} = node;

        if (localName === 'style' || localName === defaultStyleNodeName) {
            let dest;
            styleName = attributes['style:name'] && attributes['style:name'].value;

            if (localName === defaultStyleNodeName || !styleName) {
                dest = result.defaults;
            } else {
                isNew = !result.named[styleName];
                dest = result.named[styleName] = result.named[styleName] || {};
            }

            forEach.call(node.childNodes || [], (node) => {
                const {exec, selector, name} = parsers[node.localName] || {};
                if (exec && name) {
                    const data = exec(node);
                    let elSelector;

                    dest[name] = isNew ? data : merge(dest[name], data);

                    if (typeof selector === 'function') {
                        elSelector = selector(styleName);
                    } else {
                        elSelector = styleName ? `.${styleName}` : selector;
                    }

                    result.computed.push({
                        selector: elSelector,
                        properties: dest[name].style
                    });
                }
            });
        }

        //else if (localName === 'list-style') {
        //    styleName = attributes['style:name'] && attributes['style:name'].value;
        //    if (styleName) {
        //        result.named[styleName] = merge(result.named[styleName] || {}, parseListProperties(node));
        //    }
        //}
    }

    if (i === length) {
        resolve(result);
        return;
    }

    setTimeout(() => readNodes(i, length, nodes, result, resolve, reject));
}

export default function (node, result) {
    return new Promise((resolve, reject) => {
        const nodes = node && node.childNodes || [];
        const result = {
            defaults: {},
            named: {},
            computed: []
        };
        readNodes(0, nodes.length, nodes, result, resolve, reject);
    });
}
