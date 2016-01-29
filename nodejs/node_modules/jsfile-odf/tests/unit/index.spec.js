import chai from 'chai';
import chaiJsonSchema from 'chai-json-schema';
import schemas from 'jsfile-schemas';
import JsFile from 'JsFile';
import OdfEngine from './../../src/index';

chai.use(chaiJsonSchema);
const assert = chai.assert;

describe('jsFile-odf', () => {
    let files;
    const documentSchema = schemas.document;

    before(() => {
        files = window.files;
    });

    it('should exist', () => {
        assert.isFunction(OdfEngine);
    });

    it('should read the file', function () {
        this.timeout(50000);
        const queue = [];
        let name;
        for (name in files) {
            if (files.hasOwnProperty(name)) {
                (function (file, name) {
                    const jf = new JsFile(file, {
                        workerPath: '/base/dist/workers/'
                    });
                    const promise = jf.read().then(done, done);
                    queue.push(promise);

                    function done (result) {
                        assert.instanceOf(result, JsFile.Document, name);
                        const json = result.json();
                        const html = result.html();
                        const text = html.textContent || '';
                        assert.jsonSchema(json, documentSchema, name);
                        assert.notEqual(text.length, 0, `File ${name} shouldn't be empty`);
                        assert.notEqual(result.name.length, 0, `Engine should parse a name of file ${name}`);

                        if (/MetaData/.test(name)) {
                            assert.isTrue(/Metadata Examples, 22 Aug 2007/.test(text), 'should parse h1');
                        }
                    }
                }(files[name], name));
            }
        }

        return Promise.all(queue);
    });
});
