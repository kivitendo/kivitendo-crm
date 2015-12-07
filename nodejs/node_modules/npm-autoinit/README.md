[![npm](https://nodei.co/npm/npm-autoinit.png)](https://npmjs.com/package/npm-autoinit)

# npm-autoinit

[![Build Status][travis-badge]][travis] [![Dependency Status][david-badge]][david]

If you miss `package.json` in the directory `npm` is working in, it will complain.

```
$ npm install thingy
…
npm WARN ENOENT ENOENT: no such file or directory, open '/tmp/tmpdir/R3semq/package.json'
npm WARN EPACKAGEJSON /tmp/tmpdir/R3semq No description
npm WARN EPACKAGEJSON /tmp/tmpdir/R3semq No repository field.
npm WARN EPACKAGEJSON /tmp/tmpdir/R3semq No README data
npm WARN EPACKAGEJSON /tmp/tmpdir/R3semq No license field.
```

Or even:

```
$ npm ls
…
npm ERR! error in /tmp/tmpdir/R3semq: ENOENT: no such file or directory, open '/tmp/tmpdir/R3semq/package.json'
```

I often [use temporary directories][tmpdir] to play/experiment with packages and hustling with `package.json` every time I want to check out some package is not an option for me.

This module will make npm run `npm init --yes` automatically for you if it sees fit.

See https://github.com/npm/npm/issues/9161.

[tmpdir]: https://github.com/eush77/fish-tmpdir

[travis]: https://travis-ci.org/eush77/npm-autoinit
[travis-badge]: https://travis-ci.org/eush77/npm-autoinit.svg
[david]: https://david-dm.org/eush77/npm-autoinit
[david-badge]: https://david-dm.org/eush77/npm-autoinit.png

## How

This package takes advantage of `onload-script` npm config option.

> A node module to require() when npm loads.  Useful for programmatic usage.

`onload-script` executes before any work on npm command is done, so if we create `package.json` file here (and [block] while we do it) the problem is solved.

[block]: https://github.com/eush77/npm-autoinit/blob/ba74d4434fba8b1ffa093669de24bb7defb88f6c/autoinit.js#L6

## Install

```
$ npm install -g npm-autoinit
```

After that, add `npm-autoinit/autoinit` as npm onload script:

```
$ npm config set onload-script npm-autoinit/autoinit
```

## API

#### `autoinit(dir, cb(err))`

Check if `package.json` is present, and if it's not run `npm init -y`.

## License

MIT
