module.exports = function () {
    return {
        options: {
            compress: true,
            report: false
        },
        engine: {
            'src': 'dist/jsfile-odf.js',
            'dest': 'dist/jsfile-odf.min.js'
        }
    };
};