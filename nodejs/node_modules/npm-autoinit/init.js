'use strict';

var autoinit = require('./');


autoinit(process.cwd(), function (err) {
  if (err) throw err;
});
