'use strict';

var spawnSync = require('child_process').spawnSync;


var autoinit = function () {
  // Block event loop while package.json is being constructed.
  spawnSync(process.argv[0], [__dirname + '/init'], {
    stdio: 'inherit'
  });
};


var installing = process.argv.some(function (arg) {
  return arg == 'i' || arg == 'install';
});

var local = process.argv.every(function (arg) {
  return arg != '-g' && arg != '--global';
});

if (installing && local) {
  autoinit();
}
