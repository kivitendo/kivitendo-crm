import JsFile from 'JsFile';
const {Document} = JsFile;

export default function (params) {
    const result = Document.elementPrototype;
    const {node, documentData, parseDocumentElement} = params;
    result.properties.tagName = 'UL';
    result.style.listStyle = 'none';
    result.style.padding = {
        value: 0,
        unit: 'in'
    };

    if (!node) {
        return result;
    }

    const forEach = [].forEach;

    forEach.call(node.childNodes || [], (node) => {
        const {localName} = node;
        if (localName === 'index-body') {
            forEach.call(node.childNodes || [], (node) => {
                const el = parseDocumentElement({
                    node,
                    documentData
                });

                if (el) {
                    el.properties.tagName = 'LI';
                    result.children.push(el);
                }
            });
        }
    });

    return result;
}