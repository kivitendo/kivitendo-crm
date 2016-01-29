module.exports = function () {
    return {
        main: {
            files: [
                {
                    expand: true,
                    flatten: true,
                    src: [
                        'node_modules/jsfile/dist/workers/**/*.js'
                    ],
                    dest: 'dist/workers/'
                }
            ]
        }
    };
};