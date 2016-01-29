import JsFile from 'JsFile';
import parseParagraph from './parseParagraph';

const {Document} = JsFile;
const arrProto = Array.prototype;
const push = arrProto.push;
const map = arrProto.map;
const forEach = arrProto.forEach;

export default function (params) {
    const {node, documentData} = params;
    const thead = Document.elementPrototype;
    const tbody = Document.elementPrototype;
    const result = Document.elementPrototype;
    const attrValue = node.attributes['table:name'] && node.attributes['table:name'].value;
    if (attrValue) {
        result.properties.name = attrValue;
    }

    result.properties.tagName = 'TABLE';
    thead.properties.tagName = 'THEAD';
    tbody.properties.tagName = 'TBODY';
    result.properties.className = node.attributes['table:style-name'] && node.attributes['table:style-name'].value || '';

    forEach.call(node && node.childNodes || [], (node) => {
        const localName = node.localName;

        if (localName === 'table-row') {
            tbody.children.push(parseTableRow({
                node,
                documentData
            }));
        } else if (localName === 'table-header-rows') {
            push.apply(thead.children, map.call(node.querySelectorAll('table-row'), (node) => {
                return parseTableRow({
                    head: true,
                    node,
                    documentData
                });
            }));
        }
    });

    result.children.push(thead, tbody);
    return result;
}

function parseTableRow (params) {
    const result = Document.elementPrototype;
    const {node, documentData, head} = params;

    result.properties.tagName = 'TR';
    push.apply(result.children, map.call(node.querySelectorAll('table-cell'), (node) => {
        const el = Document.elementPrototype;

        el.properties.className = node.attributes['table:style-name'] && node.attributes['table:style-name'].value || '';
        el.properties.tagName = head ? 'TH' : 'TD';
        push.apply(el.children, map.call(node.querySelectorAll('p'), (node) => {
            return parseParagraph({
                node,
                documentData
            });
        }));

        return el;
    }));

    return result;
}
