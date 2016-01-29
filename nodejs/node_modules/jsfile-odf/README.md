# jsFile-odf [![Build Status](https://secure.travis-ci.org/jsFile/jsFile-odf.png?branch=master)](https://travis-ci.org/jsFile/jsFile-odf) [![Coverage Status](https://coveralls.io/repos/jsFile/jsFile-odf/badge.svg?branch=master&service=github)](https://coveralls.io/github/jsFile/jsFile-odf?branch=master)
Engine for jsFile library to work with documents in [ODF](https://www.oasis-open.org/committees/tc_home.php?wg_abbrev=office) format (.odf, etc.)

## Installation
### via NPM

You can install a <code>jsFile-odf</code> package very easily using NPM. After
installing NPM on your machine, simply run:
````
$ npm install jsfile-odf
````

### with Git

You can clone the whole repository with Git:
````
$ git clone git://github.com/jsFile/jsFile-odf.git
````

### from latest version

Also you can download [the latest release](https://github.com/jsFile/jsFile-odf/tree/master/dist) of `ODF` engine and include built files to your project.


##Usage
````js
import JsFile from 'JsFile';
import JsFileOdf from 'jsfile-odf';

const jf = new JsFile(file, options);
````
`file` - a file of [ODF](https://www.oasis-open.org/committees/tc_home.php?wg_abbrev=office) type. You may find information about options and `jsFile` in [documentation](https://github.com/jsFile/jsFile#installation)
