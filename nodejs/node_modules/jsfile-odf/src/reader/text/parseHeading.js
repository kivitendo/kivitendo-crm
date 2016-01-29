import JsFile from 'JsFile';
import parseParagraph from './parseParagraph';
const {Document} = JsFile;

export default function (params) {
    let baseEl = Document.elementPrototype;
    const {node} = params;

    if (!node) {
        return baseEl;
    }

    const el = parseParagraph(params);
    el.properties.tagName = baseEl.properties.tagName;

    return el;
}