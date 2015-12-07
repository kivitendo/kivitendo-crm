'use strict';

var init = require('init-package-json'),
    findRoot = require('find-root');

var fs = require('fs'),
    path = require('path');


module.exports = function (dir, cb) {
  // Make sure no package.json is there yet.
  try {
    findRoot(dir);
    return process.nextTick(cb);
  }
  catch (e) {}

  // Write package.json file.
  var consoleLog = console.log;
  console.log = Function.prototype;
  init(dir, String(), { yes: true }, function (err) {
    console.log = consoleLog;
    if (err) return cb(err);
    var pkgFile = path.join(dir, 'package.json');
    var pkg = fixup(require(pkgFile));
    fs.writeFile(pkgFile, JSON.stringify(pkg, null, 2), cb);
  });
};


// Fix package.json to not produce warnings.
function fixup (pkg) {
  pkg.description = ' ';
  pkg.repository = {};
  return pkg;
}
