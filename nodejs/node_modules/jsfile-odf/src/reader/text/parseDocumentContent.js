import JsFile from 'JsFile';
import parseDocumentElement from './parseDocumentElement';
const {Document} = JsFile;
const {errors: {invalidReadFile}, merge} = JsFile.Engine;

function setPageProperties (page, {style, properties}) {
    merge(page.style, style);
    merge(page.properties, properties);

    // TODO: remove when engine will be able to break the pages by content
    if (page.style.height) {
        page.style.minHeight = page.style.height;
        delete page.style.height;
    }
}

export default function (params) {
    return new Promise((resolve, reject) => {
        const {xml, documentData, fileName} = params;
        if (!xml || !documentData) {
            reject(new Error(invalidReadFile));
        }

        const result = {
            meta: {
                name: fileName,
                wordsCount: (documentData.documentInfo && documentData.documentInfo.wordsCount) || null
            },
            content: [],
            styles: documentData.styles.computed
        };
        const {pageLayout = {}} = documentData.styles;
        const {page: pageProperties = {}} = pageLayout;
        const node = xml.querySelector('body text');

        if (node) {
            let page = Document.elementPrototype;
            setPageProperties(page, pageProperties);
            [].forEach.call(node && node.childNodes || [], (node) => {
                const el = parseDocumentElement({
                    node,
                    documentData
                });

                if (el) {
                    if (el.properties.pageBreak) {
                        result.content.push(page);
                        page = Document.elementPrototype;
                        setPageProperties(page, pageProperties);
                    }

                    page.children.push(el);
                }
            });

            result.content.push(page);
        }

        resolve(result);
    });
}
