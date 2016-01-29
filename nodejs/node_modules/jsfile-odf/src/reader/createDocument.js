import JsFile from 'JsFile';
import createTextDocument from './text/createDocument';
const {Document} = JsFile;
const {errors} = JsFile.Engine;

export default function (data) {
    if (this.isTextFile()) {
        return createTextDocument.apply(this, arguments);
    }

    return Promise.reject(errors.invalidFileType);
};