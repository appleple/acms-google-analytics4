/**
 * 配布バージョン作成プログラム
 */

const { zipPromise } = require('./lib/system.js');

const fs = require('fs-extra');
const co = require('co');

// package.json
const { version } = require('../package.json');


co(function* () {
  try {
    fs.mkdirsSync('GoogleAnalytics4');
    fs.mkdirsSync(`build/v${version}`);
    fs.copySync('./README.md', 'GoogleAnalytics4/README.md');
    fs.copySync('./GET', 'GoogleAnalytics4/GET');
    fs.copySync('./Services', 'GoogleAnalytics4/Services');
    fs.copySync('./template', 'GoogleAnalytics4/template');
    fs.copySync('./vendor', 'GoogleAnalytics4/vendor');
    fs.copySync('./themes', 'GoogleAnalytics4/themes');
    fs.copySync('./ServiceProvider.php', 'GoogleAnalytics4/ServiceProvider.php');
    yield zipPromise('GoogleAnalytics4', `./build/v${version}/GoogleAnalytics4.zip`);
    fs.copySync(`./build/v${version}/GoogleAnalytics4.zip`, './build/GoogleAnalytics4.zip');
  } catch (err) {
    console.log(err);
  } finally {
    fs.removeSync('GoogleAnalytics4');
  }
});
