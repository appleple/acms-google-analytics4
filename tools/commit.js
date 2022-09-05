"use strict"

const co = require('co');
const cmd = require('node-cmd');

// package.json
const { version } = require('../package.json');

/**
 * Run system command
 *
 * @param cmdString
 * @returns {Promise}
 */
 const systemCmd = cmdString => {
  return new Promise((resolve) => {
    cmd.get(
      cmdString,
      (data, err, stderr) => {
        console.log(cmdString);
        console.log(data);
        if (err) {
          console.log(err);
        }
        if (stderr) {
          console.log(stderr);
        }
        resolve(data);
      }
    );
  });
}

co(function* () {
  try {
    yield systemCmd('git add -A');
    yield systemCmd(`git commit -m "v${version}"`);
    yield systemCmd(`git tag v${version}`);
    yield systemCmd('git push');
    yield systemCmd('git push --tags');
  } catch (err) {
    console.log(err);
  }
});
