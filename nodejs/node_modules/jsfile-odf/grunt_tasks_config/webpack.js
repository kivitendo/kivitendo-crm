module.exports = function (grunt) {
    return {
        app: {
            // webpack options
            entry: "./src/index.js",
            output: {
                path: "dist/",
                filename: "jsfile-odf.js",
                library: ["JsFileOdf"],
                libraryTarget: "umd"
            },

            module: {
                loaders: [
                    {
                        test: /\.js$/,
                        loader: 'babel',
                        query: {
                            stage: 0
                        }
                    }
                ]
            },

            externals: [
                {
                    "JsFile": {
                        root: "JsFile",
                        commonjs2: "JsFile",
                        commonjs: "JsFile",
                        amd: "JsFile"
                    }
                }
            ],

            stats: {
                // Configure the console output
                colors: true,
                modules: true,
                reasons: true
            },

            progress: false
        }
    };
};