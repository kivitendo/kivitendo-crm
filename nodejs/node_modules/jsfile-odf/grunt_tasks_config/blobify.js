module.exports = function () {
    return {
        main: {
            options: {
                target: 'global'
            },
            src: ['tests/files/**/07-08-22-MetaData-Examples.*'],
            dest: 'tests/filesCache.js'
        }
    };
};