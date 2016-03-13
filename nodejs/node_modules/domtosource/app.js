/*globals require, process, console, exports */

(function () {

	'use strict';

	var cheerio = require('cheerio'),
		nodeIndexToCharIndex,
		charIndexToLocation,
		processElement,
		find;

	// Take a node type and the index of this node (e.g. this is the 5th <li> element) and return the character index of the node
	nodeIndexToCharIndex = function(html, nodeType, n) {
		var i,
			index = 0,
			search = '<' + nodeType;
		for (i = 0; i <= n; i += 1) {
			index = html.indexOf(search, index + 1);
		}
		return index;
	};

	// Take a string index as input and return the line and column number
	charIndexToLocation = function(html, index, method) {
		var substr = html.substr(0, index),
			lastLineBreak = substr.lastIndexOf('\n') || '',
			lineNumber = (substr.match(/\n/g)||[]).length + 1,
			columnNumber = index - lastLineBreak;
		return [lineNumber, columnNumber, method];
	};

	// Calculate the location of an individual element
	processElement = function(html, matchHtml, $cheerioLowerCaseLazy, i) {
		var matchHtmlLen = matchHtml.length,
			charIndex;

		// Method A
		// This method is very fast but only works if the element is unique

		// Find where matchHtml appears in the document
		charIndex = html.indexOf(matchHtml);
		if (charIndex > -1) {
			// If this was the only occurrence, we have the location
			if (html.indexOf(matchHtml, charIndex + matchHtmlLen) === -1) {
				return charIndexToLocation(html, charIndex, 'methodA');
			}
		}

		// Method B
		// This method is slower but will work in all cases

		// Get the elements of this type (case insensitive)
		return (function() {
			var $lazy = $cheerioLowerCaseLazy(),
				$cheerioLowerCase = $lazy.$cheerio,
				$matchLowerCase = $lazy.$matches.eq(i),
				htmlLowerCase = $lazy.html,
				nodeTypeLowerCase = $matchLowerCase['0'].name,
				$similarElements = $cheerioLowerCase(nodeTypeLowerCase),
				n, len;

			for (n = 0, len = $similarElements.length; n < len; n += 1) {
				if ($matchLowerCase['0'] == $similarElements.eq(n)['0']) {
					// This is the nth element of type nodeType in the document
					charIndex = nodeIndexToCharIndex(htmlLowerCase, nodeTypeLowerCase, n);
					return charIndexToLocation(html, charIndex, 'methodB');
				}
			}
		}());

		throw new Error('Unable to calculate the line number for an element of type ' + nodeType);
	};

	// The exported module
	find = function(html, selector) {

		if (!html || !selector) {
			throw new Error('The html and selector parameters are required');
		}

		var $cheerio = cheerio.load(html, {lowerCaseTags: false}),
			$matches = $cheerio(selector),
			$cheerioLowerCaseLazy = (function() {
				// $cheerioLowerCaseLazy is Cheerio with the lowerCaseTags parameter set to true
				// This variable is lazily initialised because it has a negative impact on speed
				// and it is only required by method B
				// https://github.com/fb55/htmlparser2/wiki/Parser-options
				var _$cheerio,
					_$matches,
					_html;

				return function() {
					if (typeof _$cheerio === 'undefined') {
						_$cheerio = cheerio.load(html, {lowerCaseTags: true});
						_$matches = _$cheerio(selector);
						_html = html.toLowerCase();
					}
					return {
						$cheerio: _$cheerio,
						$matches: _$matches,
						html: _html
					};
				};
			}()),
			results = [],
			i, len, $match, matchHtml, location;

		for (i = 0, len = $matches.length; i < len; i += 1) {
			$match = $matches.eq(i);
			matchHtml = $cheerio.html($match);
			results[i] = {
				el: $match,
				html: $cheerio.html($matches.eq(i))
			};
			location = processElement(html, matchHtml, $cheerioLowerCaseLazy, i);
			results[i].line = location[0];
			results[i].column = location[1];
			results[i].calculationMethod = location[2];
		}

		return results;
	};

	// Export for Node JS
	if (typeof exports !== 'undefined') {
		exports.find = find;
	}

}());
