# DOM to Source

This module wraps around Cheerio and magically calculates the line and column number where DOM elements appear in the HTML source code.

## Usage

```js
var fs = require('fs'),
  domtosource = require('domtosource'),
  doc = fs.readFileSync('file.html', 'utf8'),
  results = domtosource.find(doc, '.green');
```

## Inputs

In the usage example above, you can see that domtosource.find() takes two parameters.

1) The HTML source code to search in

2) The CSS selector to search for

## Return values

domtosource returns an array containing the elements that matched your selector, and their line and column numbers in the HTML source:

```js
[
  {
    el: { '0': [Object], length: 1 },
    html: '<li class="green">Green <span class="green">test</span></li>',
    line: 12,
    column: 5,
    calculationMethod: 'methodA'
  },
  {
  	el: { '0': [Object], length: 1 },
    html: '<span class="green">test</span>',
    line: 12,
    column: 29,
    calculationMethod: 'methodA'
  },
  {
  	el: { '0': [Object], length: 1 },
    html: '<li class="green">Green</li>',
    line: 16,
    column: 5,
    calculationMethod: 'methodB'
 	},
  {
  	el: { '0': [Object], length: 1 },
    html: '<li class="green">Green</li>',
    line: 17,
    column: 5,
    calculationMethod: 'methodB'
  }
]
```

The calculationMethod return value indicates which method was used to calculate the line and column number. This is returned for unit test purposes because some methods are faster than others, but only work in certain situations. It is not something you need to worry about as a user.
