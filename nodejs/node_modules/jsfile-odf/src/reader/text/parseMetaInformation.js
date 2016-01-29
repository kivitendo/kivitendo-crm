import JsFile from 'JsFile';
const {formatPropertyName} = JsFile.Engine;

export default function (xml) {
    const result = {
        documentInfo: {},
        appInfo: {}
    };
    const node = xml.querySelector('meta');

    [].forEach.call(node && node.childNodes || [], ({textContent, localName, attributes}) => {
        switch (localName) {
            case 'initial-creator':
            case 'creator':
                if (textContent) {
                    result.documentInfo.creator = textContent;
                }

                break;
            case 'creation-date':
                if (textContent) {
                    result.documentInfo.created = new Date(textContent);
                }

                break;
            case 'date':
                if (textContent) {
                    result.documentInfo.modified = new Date(textContent);
                }

                break;
            case 'generator':
                if (textContent) {
                    result.appInfo.application = textContent;
                }

                break;
            case 'document-statistic':
                Array.prototype.forEach.call(attributes || [], ({value, name}) => {
                    result.documentInfo[formatPropertyName(name)] = !isNaN(value) ? Number(value) : value;
                });

                break;
        }
    });

    return result;
}