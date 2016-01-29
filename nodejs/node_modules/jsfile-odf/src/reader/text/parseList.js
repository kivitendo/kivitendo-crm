import JsFile from 'JsFile';
const {Document} = JsFile;

export default function (params) {
    const result = Document.elementPrototype;
    const {node, documentData, parseDocumentElement} = params;
    result.properties.tagName = 'UL';

    if (!node) {
        return result;
    }

    const {attributes} = node;
    const arrProto = Array.prototype;
    const forEach = arrProto.forEach;
    const attrValue = attributes['xml:id'] && attributes['xml:id'].value;
    if (attrValue) {
        result.properties.id = attrValue;
    }

    result.properties.className = attributes['text:style-name'] && attributes['text:style-name'].value || '';
    forEach.call(node.childNodes || [], (node) => {
        if (node.localName === 'list-item') {
            const el = Document.elementPrototype;
            el.properties.tagName = 'LI';

            forEach.call(node.childNodes || [], (node) => {
                const child = parseDocumentElement({
                    node,
                    documentData
                });

                if (child) {
                    el.children.push(child);
                }
            });

            result.children.push(el);
        }
    });

    return result;
}