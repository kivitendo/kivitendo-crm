import JsFile from 'JsFile';
const {Document} = JsFile;

export default function (params) {
    const result = Document.elementPrototype;
    const {node, documentData, parseDocumentElement} = params;

    [].forEach.call(node.childNodes || [], (node) => {
        const child = parseDocumentElement({
            node,
            documentData
        });

        if (child) {
            result.children.push(child);
        }
    });

    return result;
}