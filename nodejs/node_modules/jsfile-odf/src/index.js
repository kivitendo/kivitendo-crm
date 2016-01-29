import {Engine, defineEngine} from 'JsFile';
import createDocument from './reader/createDocument';
const {validateFile} = Engine;

const fileTypes = {
    textFiles: {
        extension: ['odt'],
        mime: ['application/vnd.oasis.opendocument.text']
    },
    textTemplateFiles: {
        extension: ['ott'],
        mime: ['application/vnd.oasis.opendocument.text-template']
    },
    graphicsFiles: {
        extension: ['odg'],
        mime: ['application/vnd.oasis.opendocument.graphics']
    },
    graphicsTemplateFiles: {
        extension: ['otg'],
        mime: ['application/vnd.oasis.opendocument.graphics-template']
    },
    presentationFiles: {
        extension: ['odp'],
        mime: ['application/vnd.oasis.opendocument.presentation']
    },
    presentationTemplateFiles: {
        extension: ['otp'],
        mime: ['application/vnd.oasis.opendocument.presentation-template']
    },
    spreadSheetFiles: {
        extension: ['ods'],
        mime: ['application/vnd.oasis.opendocument.spreadsheet']
    },
    spreadSheetTemplateFiles: {
        extension: ['ots'],
        mime: ['application/vnd.oasis.opendocument.spreadsheet-template']
    },
    chartFiles: {
        extension: ['odc'],
        mime: ['application/vnd.oasis.opendocument.chart']
    },
    chartTemplateFiles: {
        extension: ['otc'],
        mime: ['application/vnd.oasis.opendocument.chart-template']
    },
    imageFiles: {
        extension: ['odi'],
        mime: ['application/vnd.oasis.opendocument.image']
    },
    imageTemplateFiles: {
        extension: ['oti'],
        mime: ['application/vnd.oasis.opendocument.image-template']
    },
    formulaFiles: {
        extension: ['odf'],
        mime: ['application/vnd.oasis.opendocument.formula']
    },
    formulaTemplateFiles: {
        extension: ['otf'],
        mime: ['application/vnd.oasis.opendocument.formula-template']
    },
    textMasterFiles: {
        extension: ['odm'],
        mime: ['application/vnd.oasis.opendocument.text-master']
    },
    textWebFiles: {
        extension: ['oth'],
        mime: ['application/vnd.oasis.opendocument.text-web']
    }
};

/**
 * @description Supported files by engine
 * @type {{extension: Array, mime: Array}}
 */
const files = {
    extension: [],
    mime: []
};

for (let k in fileTypes) {
    if (fileTypes.hasOwnProperty(k)) {
        files.extension.push.apply(files.extension, fileTypes[k].extension);
        files.mime.push.apply(files.mime, fileTypes[k].mime);
    }
}

class OdfEngine extends Engine {
    createDocument = createDocument

    parser = 'readArchive'

    files = files

    isTextFile () {
        return Boolean(this.file && validateFile(this.file, fileTypes.textFiles));
    }

    static test (file) {
        return Boolean(file && validateFile(file, files));
    }

    static mimeTypes = files.mime.slice(0)
}

defineEngine(OdfEngine);

export default OdfEngine;