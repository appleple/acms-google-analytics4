/**
 * 配布バージョン作成プログラム
 */

 const fs = require('fs-extra');
 const co = require('co');
 const archiver = require('archiver');

 // package.json
 const pkg = require('../package.json');

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

//  co(function* () {
//    try {
//      fs.mkdirsSync(`Event`);
//      fs.mkdirsSync(`build`);
//      fs.copySync(`./README.md`, `Event/README.md`);
//      fs.copySync(`./images`, `Event/images`);
//      fs.copySync(`./GET`, `Event/GET`);
//      fs.copySync(`./template`, `Event/template`);
//      fs.copySync(`./ServiceProvider.php`, `Event/ServiceProvider.php`);
//      yield zipPromise(`Event`, `./build/Event${pkg.version}.zip`);
//      fs.removeSync(`Event`);
//    } catch (err) {
//      console.log(err);
//    }
//  });
