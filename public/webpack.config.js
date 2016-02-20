var webpack = require('webpack');
var path = require('path');
var CommonsChunkPlugin = require("webpack/lib/optimize/CommonsChunkPlugin");
var glob = require('glob');



function getEntry() {
    var entry = {};
    glob.sync(__dirname + '/js/app/*.main.js').forEach(function(name) {
        var n = name.match(/([^/]+?)\.main\.js/)[1];

        entry[n] = './js/app/' + n + '.main.js';
    });
    return entry;
}

module.exports = {
    refreshEntry: function() {
        this.entry = getEntry();
    },
    //context: __dirname + '/',
    entry: getEntry(),
    // 输出

    plugins: [
        //定义全局变量
        new webpack.DefinePlugin({
            VERSION: JSON.stringify("5fa3b9"),
            BROWSER_SUPPORTS_HTML5: true,
            TWO: "1+1",
            "typeof window": JSON.stringify("object")
        }),
        //提取公用代码
        new CommonsChunkPlugin('common.js'),
        //全局模块
        new webpack.ProvidePlugin({
            $: "../libs/jquery-1.11.3.js",
            jQuery: "../libs/jquery-1.11.3.js",
            "window.jQuery": "../libs/jquery-1.11.3.js"
        }),
       // new webpack.IgnorePlugin(/icc/g),
        //生产hash
        function() {
            this.plugin("done", function(stats) {
                require("fs").writeFileSync(
                    path.join(__dirname, "/dist/js", "stats.json"),
                    JSON.stringify(stats.toJson())
                );
            });
        },
        //压缩js
       // new webpack.optimize.UglifyJsPlugin()
    ],
    
    resolve: {
        alias: {
            jquery: '../libs/jquery-1.11.3.js',
            //iccTags:'http://catholicstatic.umaman.com/js/icc-tags.js'
        },
    },
    externals: {
       // iccTags: true
    },
  
    module: { 
        loaders: [
            {test: /\.html$/i, loader: 'raw-loader'},
            {test: /\.(jpe?g|png|gif|svg)$/i, loader: "file-loader" },
            {test: /\.es6$/, loader: 'babel-loader'}
        ]
    },
    //devtool: 'source-map',
    output: {
        path: './dist/js',
        filename: '[name].js',
        sourceMapFilename: '[file].map',
        publicPath: "./dist/js/"
    }
};
