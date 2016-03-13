var fs = require('fs'),
	domtosource = require('../'),
	assert = require('assert');

describe('domtosource', function() {

	describe('Test bad inputs', function() {

		it('should throw an error if the html is empty', function() {
			assert.throws(function() {
				var results = domtosource.find('', '.green');
			});
		});

		it('should throw an error if the selector is empty', function() {
			assert.throws(function() {
				var doc = fs.readFileSync(__dirname + '/example-html/page1.html', 'utf8'),
				results = domtosource.find(doc, '');
			});
		});
	});

	describe('Test .green', function() {

		var doc = fs.readFileSync(__dirname + '/example-html/page1.html', 'utf8'),
			results = domtosource.find(doc, '.green');

		it('should return 4 results', function() {
			assert.equal(4, results.length);
		});

		it('should be able to use method A (the fast method) for unique elements', function() {
			assert.equal(results[0].calculationMethod, 'methodA');
			assert.equal(results[1].calculationMethod, 'methodA');
			assert.equal(results[2].calculationMethod, 'methodB');
			assert.equal(results[3].calculationMethod, 'methodB');
		});

		it('should calculate line and column numbers correctly', function() {
			assert.equal(results[0].line, 12);
			assert.equal(results[1].line, 12);
			assert.equal(results[2].line, 16);
			assert.equal(results[3].line, 17);
			assert.equal(results[0].column, 5);
			assert.equal(results[1].column, 29);
			assert.equal(results[2].column, 5);
			assert.equal(results[3].column, 5);
		});
	});

	describe('Test a document with no line breaks', function() {
		var doc = fs.readFileSync(__dirname + '/example-html/page1-oneline.html', 'utf8'),
			results = domtosource.find(doc, '.green');

		it('should return 4 results', function() {
			assert.equal(4, results.length);
		});

		it('should be able to use method A (the fast method) for unique elements', function() {
			assert.equal(results[0].calculationMethod, 'methodA');
			assert.equal(results[1].calculationMethod, 'methodA');
			assert.equal(results[2].calculationMethod, 'methodB');
			assert.equal(results[3].calculationMethod, 'methodB');
		});

		it('should calculate line and column numbers correctly', function() {
			assert.equal(results[0].line, 1);
			assert.equal(results[1].line, 1);
			assert.equal(results[2].line, 1);
			assert.equal(results[3].line, 1);
			assert.equal(results[0].column, 199);
			assert.equal(results[1].column, 223);
			assert.equal(results[2].column, 316);
			assert.equal(results[3].column, 348);
		});
	});

	describe('Test a large document', function() {
		var doc = fs.readFileSync(__dirname + '/example-html/css3-selectors.html', 'utf8'),
			results = domtosource.find(doc, 'p'),
			results2 = domtosource.find(doc, 'p[class]');

		it('should return 333 results', function() {
			assert.equal(results.length, 333);
		});

		it('should return 16 results for paragraphs with a class attribute', function() {
			assert.equal(results2.length, 16);
		});
	});

	// Test method B and capitalised element names
	describe('Test a document with some capitalised element names', function() {
		var doc = fs.readFileSync(__dirname + '/example-html/page1-caps.html', 'utf8'),
			results = domtosource.find(doc, '.green');

		it('should be able to use method A (the fast method) for unique elements', function() {
			assert.equal(results[0].calculationMethod, 'methodA');
			assert.equal(results[1].calculationMethod, 'methodA');
			assert.equal(results[2].calculationMethod, 'methodB');
			assert.equal(results[3].calculationMethod, 'methodB');
		});

		it('should calculate line and column numbers correctly', function() {
			assert.equal(results[0].line, 12);
			assert.equal(results[1].line, 12);
			assert.equal(results[2].line, 16);
			assert.equal(results[3].line, 17);
			assert.equal(results[0].column, 5);
			assert.equal(results[1].column, 29);
			assert.equal(results[2].column, 5);
			assert.equal(results[3].column, 5);
		});

		it('should return HTML for each result', function() {
			assert.equal(results[0].html, '<li class="green">Green <span class="green">test</span></li>');
			assert.equal(results[1].html, '<span class="green">test</span>');
			assert.equal(results[2].html, '<LI class="green">Green</LI>');
			assert.equal(results[3].html, '<LI class="green">Green</LI>');
		});
	});

	// Test method A and capitalised element names
	// Every element should be unique because they have different capitalisation
	describe('Test a document with all unique capitalised element names', function() {
		var doc = fs.readFileSync(__dirname + '/example-html/page1-caps-unique.html', 'utf8'),
			results = domtosource.find(doc, '.green');

		it('should be able to use method A (the fast method) for unique elements', function() {
			assert.equal(results[0].calculationMethod, 'methodA');
			assert.equal(results[1].calculationMethod, 'methodA');
			assert.equal(results[2].calculationMethod, 'methodA');
			assert.equal(results[3].calculationMethod, 'methodA');
		});

		it('should calculate line and column numbers correctly', function() {
			assert.equal(results[0].line, 12);
			assert.equal(results[1].line, 12);
			assert.equal(results[2].line, 16);
			assert.equal(results[3].line, 17);
			assert.equal(results[0].column, 5);
			assert.equal(results[1].column, 29);
			assert.equal(results[2].column, 5);
			assert.equal(results[3].column, 5);
		});

		it('should return HTML for each result', function() {
			assert.equal(results[0].html, '<li class="green">Green <SPAN class="green">test</SPAN></li>');
			assert.equal(results[1].html, '<SPAN class="green">test</SPAN>');
			assert.equal(results[2].html, '<LI class="green">Green</LI>');
			assert.equal(results[3].html, '<li class="green">Green</li>');
		});
	});

	describe('Test a document with nested list items', function() {
		var doc = fs.readFileSync(__dirname + '/example-html/nested-lists.html', 'utf-8'),
			results = domtosource.find(doc, 'li li');

		it('should process descendent selectors correctly', function() {
			assert(results.length, 10);
		});
	});
});
