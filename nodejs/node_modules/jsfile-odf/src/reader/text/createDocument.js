import JsFile from 'JsFile';
import parseMetaInformation from './parseMetaInformation';
import parseStyles from './parseStyles';
import parseDocumentContent from './parseDocumentContent';
const {Document} = JsFile;
const {normalizeDataUri} = JsFile.Engine;

/**
 *
 * @param filesEntry {Array}
 * @private
 */
export default function (filesEntry) {
    return new Promise(function (resolve, reject) {
        const domParser = new DOMParser();
        let queue = [];
        let document;
        let documentData = {
            documentInfo: {},
            appInfo: {},
            styles: {},
            media: {}
        };

        filesEntry.forEach(fileEntry => {
            let method;
            const filename = fileEntry.entry.filename;
            let isMediaSource;

            if (!fileEntry.file) {
                return;
            }

            isMediaSource = Boolean(filename && filename.includes('Pictures/'));
            if (isMediaSource) {
                method = 'readAsDataURL';
            }

            queue.push(new Promise(function (resolve, reject) {
                this.readFileEntry({
                    file: fileEntry.file,
                    method
                }).then((result) => {
                    let xml;

                    if (isMediaSource) {
                        documentData.media[filename] = normalizeDataUri(result, filename);
                        resolve();
                    } else {
                        xml = domParser.parseFromString(result, 'application/xml');

                        if (filename.includes('styles.')) {
                            parseStyles(xml).then((styles) => documentData.styles = styles, reject);
                        } else if (filename.includes('meta.')) {
                            let info = parseMetaInformation(xml);
                            documentData.documentInfo = info.documentInfo;
                            documentData.appInfo = info.appInfo;
                        } else if (filename.includes('content.')) {
                            document = xml;
                        }

                        resolve();
                    }
                }, reject);
            }.bind(this)));
        }, this);

        Promise.all(queue).then(function () {
            parseDocumentContent({
                xml: document,
                documentData,
                fileName: this.fileName
            }).then((result) => {
                resolve(new Document(result));
            }, reject);

            documentData = document = null;
        }.bind(this), reject);
    }.bind(this));
}