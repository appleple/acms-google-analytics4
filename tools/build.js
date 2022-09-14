/**
 * 配布バージョン作成プログラム
 */

const fs = require('fs-extra');
const co = require('co');
const archiver = require('archiver');

// package.json
const { version } = require('../package.json');

const zipPromise = (src, dist) => {
  return new Promise((resolve, reject) => {
    const archive = archiver.create('zip', {});
    const output = fs.createWriteStream(dist);

    // listen for all archive data to be written
    output.on('close', () => {
      console.log(archive.pointer() + ' total bytes');
      console.log('Archiver has been finalized and the output file descriptor has closed.');
      resolve();
    });

    // good practice to catch this error explicitly
    archive.on('error', (err) => {
      reject(err);
    });

    archive.pipe(output);
    archive.directory(src).finalize();
  });
}

co(function* () {
  try {
    fs.mkdirsSync('GoogleAnalytics4');
    fs.mkdirsSync(`build/v${version}`);
    fs.copySync('./README.md', 'GoogleAnalytics4/README.md');
    fs.copySync('./GET', 'GoogleAnalytics4/GET');
    fs.copySync('./Services', 'GoogleAnalytics4/Services');
    fs.copySync('./template', 'GoogleAnalytics4/template');
    fs.copySync('./vendor', 'GoogleAnalytics4/vendor');
    fs.copySync('./ServiceProvider.php', 'GoogleAnalytics4/ServiceProvider.php');
    yield zipPromise('GoogleAnalytics4', `./build/v${version}/GoogleAnalytics4.zip`);
    fs.copySync(`./build/v${version}/GoogleAnalytics4.zip`, './build/GoogleAnalytics4.zip');
  } catch (err) {
    console.log(err);
  } finally {
    fs.removeSync('GoogleAnalytics4');
  }
});
