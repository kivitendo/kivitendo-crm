(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("JsFile"));
	else if(typeof define === 'function' && define.amd)
		define(["JsFile"], factory);
	else if(typeof exports === 'object')
		exports["JsFileOdf"] = factory(require("JsFile"));
	else
		root["JsFileOdf"] = factory(root["JsFile"]);
})(this, function(__WEBPACK_EXTERNAL_MODULE_1__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	var _get = function get(_x, _x2, _x3) { var _again = true; _function: while (_again) { var object = _x, property = _x2, receiver = _x3; desc = parent = getter = undefined; _again = false; if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x = parent; _x2 = property; _x3 = receiver; _again = true; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var _JsFile = __webpack_require__(1);

	var _readerCreateDocument = __webpack_require__(2);

	var _readerCreateDocument2 = _interopRequireDefault(_readerCreateDocument);

	var validateFile = _JsFile.Engine.validateFile;

	var fileTypes = {
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
	var files = {
	    extension: [],
	    mime: []
	};

	for (var k in fileTypes) {
	    if (fileTypes.hasOwnProperty(k)) {
	        files.extension.push.apply(files.extension, fileTypes[k].extension);
	        files.mime.push.apply(files.mime, fileTypes[k].mime);
	    }
	}

	var OdfEngine = (function (_Engine) {
	    _inherits(OdfEngine, _Engine);

	    function OdfEngine() {
	        _classCallCheck(this, OdfEngine);

	        _get(Object.getPrototypeOf(OdfEngine.prototype), 'constructor', this).apply(this, arguments);

	        this.createDocument = _readerCreateDocument2['default'];
	        this.parser = 'readArchive';
	        this.files = files;
	    }

	    _createClass(OdfEngine, [{
	        key: 'isTextFile',
	        value: function isTextFile() {
	            return Boolean(this.file && validateFile(this.file, fileTypes.textFiles));
	        }
	    }], [{
	        key: 'test',
	        value: function test(file) {
	            return Boolean(file && validateFile(file, files));
	        }
	    }, {
	        key: 'mimeTypes',
	        value: files.mime.slice(0),
	        enumerable: true
	    }]);

	    return OdfEngine;
	})(_JsFile.Engine);

	(0, _JsFile.defineEngine)(OdfEngine);

	exports['default'] = OdfEngine;
	module.exports = exports['default'];

/***/ },
/* 1 */
/***/ function(module, exports) {

	module.exports = __WEBPACK_EXTERNAL_MODULE_1__;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _textCreateDocument = __webpack_require__(3);

	var _textCreateDocument2 = _interopRequireDefault(_textCreateDocument);

	var Document = _JsFile2['default'].Document;
	var errors = _JsFile2['default'].Engine.errors;

	exports['default'] = function (data) {
	    if (this.isTextFile()) {
	        return _textCreateDocument2['default'].apply(this, arguments);
	    }

	    return Promise.reject(errors.invalidFileType);
	};

	;
	module.exports = exports['default'];

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _parseMetaInformation = __webpack_require__(4);

	var _parseMetaInformation2 = _interopRequireDefault(_parseMetaInformation);

	var _parseStyles = __webpack_require__(5);

	var _parseStyles2 = _interopRequireDefault(_parseStyles);

	var _parseDocumentContent = __webpack_require__(17);

	var _parseDocumentContent2 = _interopRequireDefault(_parseDocumentContent);

	var Document = _JsFile2['default'].Document;
	var normalizeDataUri = _JsFile2['default'].Engine.normalizeDataUri;

	/**
	 *
	 * @param filesEntry {Array}
	 * @private
	 */

	exports['default'] = function (filesEntry) {
	    return new Promise((function (resolve, reject) {
	        var _this = this;

	        var domParser = new DOMParser();
	        var queue = [];
	        var document = undefined;
	        var documentData = {
	            documentInfo: {},
	            appInfo: {},
	            styles: {},
	            media: {}
	        };

	        filesEntry.forEach(function (fileEntry) {
	            var method = undefined;
	            var filename = fileEntry.entry.filename;
	            var isMediaSource = undefined;

	            if (!fileEntry.file) {
	                return;
	            }

	            isMediaSource = Boolean(filename && filename.includes('Pictures/'));
	            if (isMediaSource) {
	                method = 'readAsDataURL';
	            }

	            queue.push(new Promise((function (resolve, reject) {
	                this.readFileEntry({
	                    file: fileEntry.file,
	                    method: method
	                }).then(function (result) {
	                    var xml = undefined;

	                    if (isMediaSource) {
	                        documentData.media[filename] = normalizeDataUri(result, filename);
	                        resolve();
	                    } else {
	                        xml = domParser.parseFromString(result, 'application/xml');

	                        if (filename.includes('styles.')) {
	                            (0, _parseStyles2['default'])(xml).then(function (styles) {
	                                return documentData.styles = styles;
	                            }, reject);
	                        } else if (filename.includes('meta.')) {
	                            var info = (0, _parseMetaInformation2['default'])(xml);
	                            documentData.documentInfo = info.documentInfo;
	                            documentData.appInfo = info.appInfo;
	                        } else if (filename.includes('content.')) {
	                            document = xml;
	                        }

	                        resolve();
	                    }
	                }, reject);
	            }).bind(_this)));
	        }, this);

	        Promise.all(queue).then((function () {
	            (0, _parseDocumentContent2['default'])({
	                xml: document,
	                documentData: documentData,
	                fileName: this.fileName
	            }).then(function (result) {
	                resolve(new Document(result));
	            }, reject);

	            documentData = document = null;
	        }).bind(this), reject);
	    }).bind(this));
	};

	module.exports = exports['default'];

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var formatPropertyName = _JsFile2['default'].Engine.formatPropertyName;

	exports['default'] = function (xml) {
	    var result = {
	        documentInfo: {},
	        appInfo: {}
	    };
	    var node = xml.querySelector('meta');

	    [].forEach.call(node && node.childNodes || [], function (_ref) {
	        var textContent = _ref.textContent;
	        var localName = _ref.localName;
	        var attributes = _ref.attributes;

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
	                Array.prototype.forEach.call(attributes || [], function (_ref2) {
	                    var value = _ref2.value;
	                    var name = _ref2.name;

	                    result.documentInfo[formatPropertyName(name)] = !isNaN(value) ? Number(value) : value;
	                });

	                break;
	        }
	    });

	    return result;
	};

	module.exports = exports['default'];

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _parsePageLayoutStyles = __webpack_require__(6);

	var _parsePageLayoutStyles2 = _interopRequireDefault(_parsePageLayoutStyles);

	var _parseStylesNode = __webpack_require__(8);

	var _parseStylesNode2 = _interopRequireDefault(_parseStylesNode);

	/**
	 *
	 * @param xml
	 * @returns {*}
	 * @private
	 */

	exports['default'] = function (xml) {
	    return new Promise(function (resolve, reject) {
	        var pageLayoutName = undefined;
	        var layouts = {};
	        var firstPageLayout = '';
	        var node = xml.querySelector('master-styles master-page');
	        if (node) {
	            var attrValue = node.attributes['style:page-layout-name'] && node.attributes['style:page-layout-name'].value;
	            if (attrValue) {
	                pageLayoutName = attrValue;
	            }
	        }

	        [].forEach.call(xml.querySelectorAll('automatic-styles page-layout'), function (node) {
	            var attrValue = node.attributes['style:name'] && node.attributes['style:name'].value;
	            if (attrValue) {
	                layouts[attrValue] = (0, _parsePageLayoutStyles2['default'])(node);

	                if (!firstPageLayout) {
	                    firstPageLayout = attrValue;
	                }
	            }
	        });

	        var pageLayout = layouts[pageLayoutName] || layouts[firstPageLayout];
	        (0, _parseStylesNode2['default'])(xml.querySelector('styles')).then(function (result) {
	            result.pageLayout = pageLayout;
	            resolve(result);
	        }, reject);
	    });
	};

	module.exports = exports['default'];

/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	var _JsFile$Engine = _JsFile2['default'].Engine;
	var formatPropertyName = _JsFile$Engine.formatPropertyName;
	var normalizeColorValue = _JsFile$Engine.normalizeColorValue;

	/**
	 *
	 * @param node
	 * @return {Object}
	 * @private
	 */

	exports['default'] = function (node) {
	    var result = {
	        page: {
	            style: {},
	            properties: {}
	        },
	        footnote: {
	            style: {},
	            properties: {}
	        },
	        footer: {
	            style: {},
	            properties: {}
	        },
	        header: {
	            style: {},
	            properties: {}
	        }
	    };

	    node = node && node.querySelector('page-layout-properties');
	    if (node) {
	        Array.prototype.forEach.call(node.attributes || [], function (attr) {
	            var size = undefined;
	            var _attr$value = attr.value;
	            var value = _attr$value === undefined ? '' : _attr$value;
	            var _attr$name = attr.name;
	            var name = _attr$name === undefined ? '' : _attr$name;

	            var prop = name && formatPropertyName(name);

	            if (prop.includes('padding') || prop.includes('margin')) {
	                size = value && (0, _getSize2['default'])(value);

	                if (size && size.unit) {
	                    result.page.style[prop] = size;
	                }
	            } else {
	                switch (prop) {
	                    case 'writingMode':
	                        result.page.style.direction = value.indexOf('rl') >= 0 ? 'rtl' : 'ltr';
	                        break;
	                    case 'printOrientation':
	                        result.page.properties.isLandscapeOrientation = value === 'landscape';
	                        break;
	                    case 'numFormat':
	                        if (value) {
	                            result.page.properties.numberingFormat = value;
	                        }

	                        break;
	                    case 'footnoteMaxHeight':
	                        size = value && (0, _getSize2['default'])(value);
	                        if (size && size.unit) {
	                            result.footnote.style.maxHeight = size;
	                        }

	                        break;
	                    case 'pageHeight':
	                        size = value && (0, _getSize2['default'])(value);
	                        if (size && size.unit) {
	                            result.page.style.height = size;
	                        }

	                        break;
	                    case 'pageWidth':
	                        size = value && (0, _getSize2['default'])(value);
	                        if (size && size.unit) {
	                            result.page.style.width = size;
	                        }

	                        break;
	                    default:
	                        size = value && (0, _getSize2['default'])(value);
	                        if (size && size.unit) {
	                            result.page.properties[prop] = size;
	                        } else {
	                            if (/color$/i.test(prop) && value) {
	                                value = value.toUpperCase();
	                            }

	                            result.page.properties[prop] = value;
	                        }
	                }
	            }
	        });

	        [].forEach.call(node.childNodes || [], function (node) {
	            if (node.localName === 'footnote-sep') {
	                var attrValue = node.attributes['style:width'] && node.attributes['style:width'].value;
	                var size = attrValue && (0, _getSize2['default'])(attrValue);
	                if (size && size.unit) {
	                    result.footnote.style.width = size;
	                }

	                attrValue = node.attributes['style:distance-before-sep'] && node.attributes['style:distance-before-sep'].value;
	                size = attrValue && (0, _getSize2['default'])(attrValue);
	                if (size && size.unit) {
	                    result.footnote.style.marginTop = size;
	                }

	                attrValue = node.attributes['style:distance-after-sep'] && node.attributes['style:distance-after-sep'].value;
	                size = attrValue && (0, _getSize2['default'])(attrValue);
	                if (size && size.unit) {
	                    result.footnote.style.marginBottom = size;
	                }

	                attrValue = node.attributes['style:adjustment'] && node.attributes['style:adjustment'].value;
	                if (attrValue) {
	                    result.footnote.style.float = 'none';

	                    if (attrValue === 'left') {
	                        result.footnote.style.float = 'left';
	                    } else if (attrValue === 'right') {
	                        result.footnote.style.float = 'right';
	                    }
	                }

	                attrValue = node.attributes['style:color'] && node.attributes['style:color'].value;
	                if (attrValue) {
	                    result.footnote.style.color = normalizeColorValue(attrValue);
	                }
	            }
	        });
	    }

	    return result;
	};

	;
	module.exports = exports['default'];

/***/ },
/* 7 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});
	var masks = [/^([0-9]*[0-9][0-9]*(?:\.[0-9]*)?|0+\.[0-9]*[1-9][0-9]*|\.[0-9]*[1-9][0-9]*)((cm)|(mm)|(in)|(pt)|(pc)|(px))$/, /^-?([0-9]+(?:\.[0-9]*)?|\.[0-9]+)(%)$/];

	/**
	 *
	 * @param val
	 * @returns {{value: number, unit: string}}
	 * @private
	 */

	exports['default'] = function (val) {
	    var result = {
	        value: 0,
	        unit: ''
	    };
	    var data = undefined;

	    masks.some(function (regExp) {
	        return data = regExp.exec(val);
	    });

	    if (data) {
	        var value = Number(data[1]);
	        var unit = data[2];

	        if (!isNaN(value) && unit) {
	            result.unit = String(unit).toLowerCase();
	            result.value = value;
	        }
	    }

	    return result;
	};

	;
	module.exports = exports['default'];

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _parseListProperties = __webpack_require__(9);

	var _parseListProperties2 = _interopRequireDefault(_parseListProperties);

	var _parseTableProperties = __webpack_require__(10);

	var _parseTableProperties2 = _interopRequireDefault(_parseTableProperties);

	var _parseTableColumnProperties = __webpack_require__(11);

	var _parseTableColumnProperties2 = _interopRequireDefault(_parseTableColumnProperties);

	var _parseTableCellProperties = __webpack_require__(12);

	var _parseTableCellProperties2 = _interopRequireDefault(_parseTableCellProperties);

	var _parseTableRowProperties = __webpack_require__(14);

	var _parseTableRowProperties2 = _interopRequireDefault(_parseTableRowProperties);

	var _parseParagraphProperties = __webpack_require__(15);

	var _parseParagraphProperties2 = _interopRequireDefault(_parseParagraphProperties);

	var _parseTextProperties = __webpack_require__(16);

	var _parseTextProperties2 = _interopRequireDefault(_parseTextProperties);

	var merge = _JsFile2['default'].Engine.merge;

	var defaultStyleNodeName = 'default-style';
	var parsers = {
	    'table-properties': {
	        name: 'table',
	        selector: 'table',
	        exec: _parseTableProperties2['default']
	    },
	    'table-column-properties': {
	        name: 'tableColumn',
	        selector: 'td',
	        exec: _parseTableColumnProperties2['default']
	    },
	    'table-cell-properties': {
	        name: 'tableCell',
	        selector: 'td',
	        exec: _parseTableCellProperties2['default']
	    },
	    'table-row-properties': {
	        name: 'tableRow',
	        selector: 'tr',
	        exec: _parseTableRowProperties2['default']
	    },
	    'paragraph-properties': {
	        name: 'paragraph',
	        selector: function selector(styleName) {
	            var className = '';

	            if (styleName) {
	                className = '.' + styleName;
	            }

	            return 'p' + className;
	        },

	        exec: _parseParagraphProperties2['default']
	    },
	    'text-properties': {
	        name: 'text',
	        selector: function selector(styleName) {
	            var className = '';

	            if (styleName) {
	                className = '.' + styleName;
	            }

	            return 'span' + className + ', ' + (className || 'p') + ' span';
	        },

	        exec: _parseTextProperties2['default']
	    }
	};
	var forEach = [].forEach;

	function readNodes(i, length, nodes, result, resolve, reject) {
	    var size = i + 100;

	    if (size > length) {
	        size = length;
	    }

	    var _loop = function () {
	        var styleName = undefined;
	        var isNew = true;
	        var node = nodes[i];
	        var localName = node.localName;
	        var attributes = node.attributes;

	        if (localName === 'style' || localName === defaultStyleNodeName) {
	            (function () {
	                var dest = undefined;
	                styleName = attributes['style:name'] && attributes['style:name'].value;

	                if (localName === defaultStyleNodeName || !styleName) {
	                    dest = result.defaults;
	                } else {
	                    isNew = !result.named[styleName];
	                    dest = result.named[styleName] = result.named[styleName] || {};
	                }

	                forEach.call(node.childNodes || [], function (node) {
	                    var _ref = parsers[node.localName] || {};

	                    var exec = _ref.exec;
	                    var selector = _ref.selector;
	                    var name = _ref.name;

	                    if (exec && name) {
	                        var data = exec(node);
	                        var elSelector = undefined;

	                        dest[name] = isNew ? data : merge(dest[name], data);

	                        if (typeof selector === 'function') {
	                            elSelector = selector(styleName);
	                        } else {
	                            elSelector = styleName ? '.' + styleName : selector;
	                        }

	                        result.computed.push({
	                            selector: elSelector,
	                            properties: dest[name].style
	                        });
	                    }
	                });
	            })();
	        }

	        //else if (localName === 'list-style') {
	        //    styleName = attributes['style:name'] && attributes['style:name'].value;
	        //    if (styleName) {
	        //        result.named[styleName] = merge(result.named[styleName] || {}, parseListProperties(node));
	        //    }
	        //}
	    };

	    for (; i < size; i++) {
	        _loop();
	    }

	    if (i === length) {
	        resolve(result);
	        return;
	    }

	    setTimeout(function () {
	        return readNodes(i, length, nodes, result, resolve, reject);
	    });
	}

	exports['default'] = function (node, result) {
	    return new Promise(function (resolve, reject) {
	        var nodes = node && node.childNodes || [];
	        var result = {
	            defaults: {},
	            named: {},
	            computed: []
	        };
	        readNodes(0, nodes.length, nodes, result, resolve, reject);
	    });
	};

	module.exports = exports['default'];

/***/ },
/* 9 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});
	var listStyleTypes = {
	    1: 'decimal',
	    i: 'lower-roman',
	    I: 'upper-roman',
	    a: 'lower-alpha',
	    A: 'upper-alpha'
	};

	exports['default'] = function (xml) {
	    var result = {
	        style: {}
	    };
	    var nodes = xml && xml.childNodes || [];
	    var i = nodes.length;

	    while (i--) {
	        if (nodes[i].localName === 'list-level-style-number') {
	            var attr = nodes[i].attributes['style:num-format'];

	            result.style.listStyleType = attr && listStyleTypes[attr.value] || 'auto';
	        }
	    }

	    return result;
	};

	;
	module.exports = exports['default'];

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	/**
	 *
	 * @param node
	 * @return {Object}
	 * @private
	 */

	exports['default'] = function (node) {
	    var result = {
	        style: {}
	    };

	    if (node) {
	        var attrValue = node.attributes['style:width'] && node.attributes['style:width'].value;
	        if (attrValue) {
	            var size = (0, _getSize2['default'])(attrValue);
	            if (size.unit) {
	                result.style.width = size;
	            }
	        }

	        attrValue = node.attributes['table:border-model'] && node.attributes['table:border-model'].value;
	        if (attrValue) {
	            result.style.borderCollapse = /coll/i.test(attrValue) ? 'collapse' : 'separate';
	        }
	    }

	    return result;
	};

	;
	module.exports = exports['default'];

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	/**
	 *
	 * @param node
	 * @return {Object}
	 * @private
	 */

	exports['default'] = function (node) {
	    var result = {
	        style: {}
	    };

	    if (node) {
	        var attr = node.attributes['style:column-width'];
	        var size = attr && (0, _getSize2['default'])(attr.value);

	        if (size && size.unit) {
	            result.style.width = size;
	        }
	    }

	    return result;
	};

	;
	module.exports = exports['default'];

/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	var _parseBorderStyle = __webpack_require__(13);

	var _parseBorderStyle2 = _interopRequireDefault(_parseBorderStyle);

	var formatPropertyName = _JsFile2['default'].Engine.formatPropertyName;

	/**
	 *
	 * @param node
	 * @return {Object}
	 * @private
	 */

	exports['default'] = function (node) {
	    var result = {
	        style: {}
	    };

	    Array.prototype.forEach.call(node && node.attributes || [], function (attr) {
	        var _attr$value = attr.value;
	        var value = _attr$value === undefined ? '' : _attr$value;
	        var _attr$name = attr.name;
	        var name = _attr$name === undefined ? '' : _attr$name;

	        var prop = name && formatPropertyName(name);

	        if (prop.includes('border')) {
	            var _ref = (0, _parseBorderStyle2['default'])(value) || {};

	            var style = _ref.style;
	            var width = _ref.width;
	            var color = _ref.color;

	            result.style[prop + 'Style'] = style;
	            result.style[prop + 'Width'] = width;
	            result.style[prop + 'Color'] = color;
	        } else if (prop === 'padding') {
	            var size = value && (0, _getSize2['default'])(value);
	            if (size && size.unit) {
	                result.style[prop] = size;
	            }
	        }
	    });

	    return result;
	};

	;
	module.exports = exports['default'];

/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	var _slicedToArray = (function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i['return']) _i['return'](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError('Invalid attempt to destructure non-iterable instance'); } }; })();

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	var normalizeColorValue = _JsFile2['default'].Engine.normalizeColorValue;

	/**
	 *
	 * @param data
	 * @return {Object}
	 * @private
	 */

	exports['default'] = function (data) {
	    var result = {
	        width: {
	            value: 0,
	            unit: 'pt'
	        },
	        color: 'none',
	        style: 'none'
	    };

	    if (data && data !== 'none') {
	        var _data$split = data.split(' ');

	        var _data$split2 = _slicedToArray(_data$split, 3);

	        var sizeData = _data$split2[0];
	        var style = _data$split2[1];
	        var color = _data$split2[2];

	        if (sizeData && style && color) {
	            var size = (0, _getSize2['default'])(sizeData);

	            if (size.unit) {
	                result.width = size;
	            }

	            result.style = style;
	            result.color = normalizeColorValue(color);
	        }
	    }

	    return result;
	};

	;
	module.exports = exports['default'];

/***/ },
/* 14 */
/***/ function(module, exports) {

	/**
	 *
	 * @param node
	 * @return {Object}
	 * @private
	 */
	"use strict";

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	exports["default"] = function (node) {
	    var result = {
	        style: {}
	    };

	    return result;
	};

	;
	module.exports = exports["default"];

/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	var _JsFile$Engine = _JsFile2['default'].Engine;
	var formatPropertyName = _JsFile$Engine.formatPropertyName;
	var normalizeColorValue = _JsFile$Engine.normalizeColorValue;

	/**
	 *
	 * @param node
	 * @return {Object}
	 * @private
	 */

	exports['default'] = function (node) {
	    var result = {
	        style: {}
	    };

	    Array.prototype.forEach.call(node && node.attributes || [], function (attr) {
	        var _attr$value = attr.value;
	        var value = _attr$value === undefined ? '' : _attr$value;
	        var _attr$name = attr.name;
	        var name = _attr$name === undefined ? '' : _attr$name;

	        var prop = name && formatPropertyName(name);
	        var size = undefined;

	        if (prop.includes('padding') || prop.includes('margin')) {
	            size = value && (0, _getSize2['default'])(value);

	            if (size.unit) {
	                result.style[prop] = size;
	            }
	        } else if (prop === 'backgroundColor') {
	            result.style[prop] = normalizeColorValue(value);
	        } else if (prop.includes('border')) {
	            var borderData = value.split(' ');
	            size = borderData[0] && (0, _getSize2['default'])(borderData[0]);

	            if (borderData.length === 3 && size.unit) {
	                /**
	                 * @description minimal visible size for borders in inches
	                 * @type {number}
	                 */
	                var minimalVisibleSize = 0.02;

	                /**
	                 * Browser can't render too thin borders, as 0.0008in.
	                 * So, here is the normalization of border width if it's not a 0.
	                 */
	                if (size.value && size.value < minimalVisibleSize) {
	                    size.value = minimalVisibleSize;
	                }

	                result.style[prop + 'Width'] = size;
	                result.style[prop + 'Style'] = borderData[1];
	                result.style[prop + 'Color'] = normalizeColorValue(borderData[2]);
	            }
	        } else if (prop === 'writingMode') {
	            result.style.direction = /rl/i.test(value) ? 'rtl' : 'ltr';
	        } else if (prop === 'textAlign') {
	            var align = /center|left|right/i.exec(value);

	            if (align && align[0]) {
	                result.style[prop] = align[0];
	            }
	        }
	    });

	    return result;
	};

	module.exports = exports['default'];

/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	var _JsFile$Engine = _JsFile2['default'].Engine;
	var formatPropertyName = _JsFile$Engine.formatPropertyName;
	var normalizeColorValue = _JsFile$Engine.normalizeColorValue;

	/**
	 *
	 * @param node
	 * @return {Object}
	 * @private
	 */

	exports['default'] = function (node) {
	    var result = {
	        style: {},
	        properties: {}
	    };

	    Array.prototype.forEach.call(node && node.attributes || [], function (attr) {
	        var _attr$value = attr.value;
	        var value = _attr$value === undefined ? '' : _attr$value;
	        var _attr$name = attr.name;
	        var name = _attr$name === undefined ? '' : _attr$name;

	        var prop = name && formatPropertyName(name);

	        if (prop.includes('padding') || prop.includes('margin') || prop === 'fontSize') {
	            var size = value && (0, _getSize2['default'])(value);

	            if (size && size.unit) {
	                result.style[prop] = size;
	            }
	        } else if (prop === 'color' || prop === 'backgroundColor') {
	            result.style[prop] = normalizeColorValue(value);
	        } else if (prop === 'fontStyle') {
	            result.style[prop] = /italic/ig.test(value) ? 'italic' : 'normal';
	        } else if (prop === 'fontName') {
	            result.style.fontFamily = value;
	        } else if (prop === 'fontWeight') {
	            result.style[prop] = /bold/ig.test(value) ? 'bold' : 'normal';
	        } else if (prop === 'textUnderlineStyle') {
	            result.style.textDecoration = /none/ig.test(value) ? 'none' : 'underline';
	        } else if (prop === 'language') {
	            result.properties.lang = value;
	        }
	    });

	    return result;
	};

	module.exports = exports['default'];

/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _parseDocumentElement = __webpack_require__(18);

	var _parseDocumentElement2 = _interopRequireDefault(_parseDocumentElement);

	var Document = _JsFile2['default'].Document;
	var _JsFile$Engine = _JsFile2['default'].Engine;
	var invalidReadFile = _JsFile$Engine.errors.invalidReadFile;
	var merge = _JsFile$Engine.merge;

	function setPageProperties(page, _ref) {
	    var style = _ref.style;
	    var properties = _ref.properties;

	    merge(page.style, style);
	    merge(page.properties, properties);

	    // TODO: remove when engine will be able to break the pages by content
	    if (page.style.height) {
	        page.style.minHeight = page.style.height;
	        delete page.style.height;
	    }
	}

	exports['default'] = function (params) {
	    return new Promise(function (resolve, reject) {
	        var xml = params.xml;
	        var documentData = params.documentData;
	        var fileName = params.fileName;

	        if (!xml || !documentData) {
	            reject(new Error(invalidReadFile));
	        }

	        var result = {
	            meta: {
	                name: fileName,
	                wordsCount: documentData.documentInfo && documentData.documentInfo.wordsCount || null
	            },
	            content: [],
	            styles: documentData.styles.computed
	        };
	        var _documentData$styles$pageLayout = documentData.styles.pageLayout;
	        var pageLayout = _documentData$styles$pageLayout === undefined ? {} : _documentData$styles$pageLayout;
	        var _pageLayout$page = pageLayout.page;
	        var pageProperties = _pageLayout$page === undefined ? {} : _pageLayout$page;

	        var node = xml.querySelector('body text');

	        if (node) {
	            (function () {
	                var page = Document.elementPrototype;
	                setPageProperties(page, pageProperties);
	                [].forEach.call(node && node.childNodes || [], function (node) {
	                    var el = (0, _parseDocumentElement2['default'])({
	                        node: node,
	                        documentData: documentData
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
	            })();
	        }

	        resolve(result);
	    });
	};

	module.exports = exports['default'];

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _parseParagraph = __webpack_require__(19);

	var _parseParagraph2 = _interopRequireDefault(_parseParagraph);

	var _parseList = __webpack_require__(20);

	var _parseList2 = _interopRequireDefault(_parseList);

	var _parseTable = __webpack_require__(21);

	var _parseTable2 = _interopRequireDefault(_parseTable);

	var _parseTableOfContent = __webpack_require__(22);

	var _parseTableOfContent2 = _interopRequireDefault(_parseTableOfContent);

	var _parseHeading = __webpack_require__(23);

	var _parseHeading2 = _interopRequireDefault(_parseHeading);

	var _parseSection = __webpack_require__(24);

	var _parseSection2 = _interopRequireDefault(_parseSection);

	var parsers = {
	    p: _parseParagraph2['default'],
	    h: _parseHeading2['default'],
	    list: _parseList2['default'],
	    table: _parseTable2['default'],
	    section: _parseSection2['default'],
	    'table-of-content': _parseTableOfContent2['default']
	};

	function parseDocumentElement(params) {
	    var parser = params.node && parsers[params.node.localName];
	    if (!parser) {
	        return null;
	    }

	    params.parseDocumentElement = parseDocumentElement;
	    return parser(params);
	}

	exports['default'] = parseDocumentElement;
	module.exports = exports['default'];

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _getSize = __webpack_require__(7);

	var _getSize2 = _interopRequireDefault(_getSize);

	var Document = _JsFile2['default'].Document;
	var tabAsSpaces = _JsFile2['default'].Engine.tabAsSpaces;

	exports['default'] = function (params) {
	    var result = Document.elementPrototype;
	    var node = params.node;
	    var documentData = params.documentData;

	    result.properties.tagName = 'P';

	    if (!node) {
	        return result;
	    }

	    result.properties.className = node.attributes['text:style-name'] && node.attributes['text:style-name'].value || '';
	    [].forEach.call(node && node.childNodes || [], function (node) {
	        var attrValue = undefined;
	        var _node$textContent = node.textContent;
	        var textContent = _node$textContent === undefined ? '' : _node$textContent;
	        var localName = node.localName;
	        var attributes = node.attributes;

	        var el = Document.elementPrototype;
	        el.properties.tagName = 'SPAN';

	        switch (localName) {
	            case 'line-break':
	                el.properties.tagName = 'BR';
	                result.children.push(el);
	                break;
	            case 'tab':
	                el.properties.textContent = tabAsSpaces;
	                result.children.push(el);
	                break;
	            case 'soft-page-break':
	                result.properties.pageBreak = true;
	                break;
	            case 'bookmark':
	                var linkName = attributes['text:name'] && attributes['text:name'].value;

	                if (linkName) {
	                    el.properties.tagName = 'A';
	                    el.properties.name = linkName;
	                    result.children.push(el);
	                }

	                break;
	            case 'a':
	            case 'span':
	                if (localName === 'span') {
	                    attrValue = attributes['text:style-name'] && attributes['text:style-name'].value || '';
	                    el.properties.className = attrValue;
	                } else {
	                    el.properties.tagName = 'A';
	                    attrValue = attributes['xlink:href'] && attributes['xlink:href'].value;
	                    if (attrValue) {
	                        el.properties.href = attrValue;
	                        if (attrValue[0] !== '#') {
	                            el.properties.target = '_blank';
	                        }
	                    }
	                }

	                [].forEach.call(node && node.childNodes || [], function (child) {
	                    var localName = child.localName;

	                    if (localName === 'tab') {
	                        el.properties.textContent += tabAsSpaces;
	                    } else {
	                        el.properties.textContent += child.textContent || '';
	                    }
	                });

	                result.children.push(el);
	                break;
	            case 'frame':
	                var img = node.querySelector('image');
	                if (img) {
	                    attrValue = img.attributes['xlink:href'] && img.attributes['xlink:href'].value;
	                    if (attrValue && documentData && documentData.media) {
	                        el.properties.tagName = 'IMG';
	                        el.properties.src = documentData.media[attrValue];

	                        var size = undefined;

	                        attrValue = attributes['svg:x'] && attributes['svg:x'].value;
	                        if (attrValue) {
	                            size = (0, _getSize2['default'])(attrValue);

	                            if (size.unit) {
	                                el.style.left = size;
	                                el.style.position = 'absolute';
	                                el.style.position = 'absolute';
	                            }
	                        }

	                        attrValue = attributes['svg:y'] && attributes['svg:y'].value;
	                        if (attrValue) {
	                            size = (0, _getSize2['default'])(attrValue);

	                            if (size.unit) {
	                                el.style.top = size;
	                            }
	                        }

	                        attrValue = attributes['svg:width'] && attributes['svg:width'].value;
	                        if (attrValue) {
	                            size = (0, _getSize2['default'])(attrValue);

	                            if (size.unit) {
	                                el.style.width = size;
	                            }
	                        }

	                        attrValue = attributes['svg:height'] && attributes['svg:height'].value;
	                        if (attrValue) {
	                            size = (0, _getSize2['default'])(attrValue);

	                            if (size.unit) {
	                                el.style.height = size;
	                            }
	                        }

	                        attrValue = attributes['draw:z-index'] && attributes['draw:z-index'].value;
	                        if (!isNaN(attrValue)) {
	                            el.style.zIndex = Number(attrValue);
	                        }

	                        attrValue = attributes['draw:style-name'] && attributes['draw:style-name'].value;
	                        if (attrValue) {
	                            el.properties.styleName = attrValue;
	                        }

	                        result.children.push(el);
	                    }
	                }

	                break;
	            case 'note':
	                break;
	            default:
	                el.properties.textContent = textContent;
	                result.children.push(el);
	        }
	    });

	    return result;
	};

	module.exports = exports['default'];

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var Document = _JsFile2['default'].Document;

	exports['default'] = function (params) {
	    var result = Document.elementPrototype;
	    var node = params.node;
	    var documentData = params.documentData;
	    var parseDocumentElement = params.parseDocumentElement;

	    result.properties.tagName = 'UL';

	    if (!node) {
	        return result;
	    }

	    var attributes = node.attributes;

	    var arrProto = Array.prototype;
	    var forEach = arrProto.forEach;
	    var attrValue = attributes['xml:id'] && attributes['xml:id'].value;
	    if (attrValue) {
	        result.properties.id = attrValue;
	    }

	    result.properties.className = attributes['text:style-name'] && attributes['text:style-name'].value || '';
	    forEach.call(node.childNodes || [], function (node) {
	        if (node.localName === 'list-item') {
	            (function () {
	                var el = Document.elementPrototype;
	                el.properties.tagName = 'LI';

	                forEach.call(node.childNodes || [], function (node) {
	                    var child = parseDocumentElement({
	                        node: node,
	                        documentData: documentData
	                    });

	                    if (child) {
	                        el.children.push(child);
	                    }
	                });

	                result.children.push(el);
	            })();
	        }
	    });

	    return result;
	};

	module.exports = exports['default'];

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _parseParagraph = __webpack_require__(19);

	var _parseParagraph2 = _interopRequireDefault(_parseParagraph);

	var Document = _JsFile2['default'].Document;

	var arrProto = Array.prototype;
	var push = arrProto.push;
	var map = arrProto.map;
	var forEach = arrProto.forEach;

	exports['default'] = function (params) {
	    var node = params.node;
	    var documentData = params.documentData;

	    var thead = Document.elementPrototype;
	    var tbody = Document.elementPrototype;
	    var result = Document.elementPrototype;
	    var attrValue = node.attributes['table:name'] && node.attributes['table:name'].value;
	    if (attrValue) {
	        result.properties.name = attrValue;
	    }

	    result.properties.tagName = 'TABLE';
	    thead.properties.tagName = 'THEAD';
	    tbody.properties.tagName = 'TBODY';
	    result.properties.className = node.attributes['table:style-name'] && node.attributes['table:style-name'].value || '';

	    forEach.call(node && node.childNodes || [], function (node) {
	        var localName = node.localName;

	        if (localName === 'table-row') {
	            tbody.children.push(parseTableRow({
	                node: node,
	                documentData: documentData
	            }));
	        } else if (localName === 'table-header-rows') {
	            push.apply(thead.children, map.call(node.querySelectorAll('table-row'), function (node) {
	                return parseTableRow({
	                    head: true,
	                    node: node,
	                    documentData: documentData
	                });
	            }));
	        }
	    });

	    result.children.push(thead, tbody);
	    return result;
	};

	function parseTableRow(params) {
	    var result = Document.elementPrototype;
	    var node = params.node;
	    var documentData = params.documentData;
	    var head = params.head;

	    result.properties.tagName = 'TR';
	    push.apply(result.children, map.call(node.querySelectorAll('table-cell'), function (node) {
	        var el = Document.elementPrototype;

	        el.properties.className = node.attributes['table:style-name'] && node.attributes['table:style-name'].value || '';
	        el.properties.tagName = head ? 'TH' : 'TD';
	        push.apply(el.children, map.call(node.querySelectorAll('p'), function (node) {
	            return (0, _parseParagraph2['default'])({
	                node: node,
	                documentData: documentData
	            });
	        }));

	        return el;
	    }));

	    return result;
	}
	module.exports = exports['default'];

/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var Document = _JsFile2['default'].Document;

	exports['default'] = function (params) {
	    var result = Document.elementPrototype;
	    var node = params.node;
	    var documentData = params.documentData;
	    var parseDocumentElement = params.parseDocumentElement;

	    result.properties.tagName = 'UL';
	    result.style.listStyle = 'none';
	    result.style.padding = {
	        value: 0,
	        unit: 'in'
	    };

	    if (!node) {
	        return result;
	    }

	    var forEach = [].forEach;

	    forEach.call(node.childNodes || [], function (node) {
	        var localName = node.localName;

	        if (localName === 'index-body') {
	            forEach.call(node.childNodes || [], function (node) {
	                var el = parseDocumentElement({
	                    node: node,
	                    documentData: documentData
	                });

	                if (el) {
	                    el.properties.tagName = 'LI';
	                    result.children.push(el);
	                }
	            });
	        }
	    });

	    return result;
	};

	module.exports = exports['default'];

/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var _parseParagraph = __webpack_require__(19);

	var _parseParagraph2 = _interopRequireDefault(_parseParagraph);

	var Document = _JsFile2['default'].Document;

	exports['default'] = function (params) {
	    var baseEl = Document.elementPrototype;
	    var node = params.node;

	    if (!node) {
	        return baseEl;
	    }

	    var el = (0, _parseParagraph2['default'])(params);
	    el.properties.tagName = baseEl.properties.tagName;

	    return el;
	};

	module.exports = exports['default'];

/***/ },
/* 24 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	    value: true
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _JsFile = __webpack_require__(1);

	var _JsFile2 = _interopRequireDefault(_JsFile);

	var Document = _JsFile2['default'].Document;

	exports['default'] = function (params) {
	    var result = Document.elementPrototype;
	    var node = params.node;
	    var documentData = params.documentData;
	    var parseDocumentElement = params.parseDocumentElement;

	    [].forEach.call(node.childNodes || [], function (node) {
	        var child = parseDocumentElement({
	            node: node,
	            documentData: documentData
	        });

	        if (child) {
	            result.children.push(child);
	        }
	    });

	    return result;
	};

	module.exports = exports['default'];

/***/ }
/******/ ])
});
;