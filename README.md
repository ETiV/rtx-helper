# Node.js RTX(Tencent) Helper
----

## HOW TO USE

Deploy the files inside `php-rtx-service` folder into `nrh` folder of the RTX `WebRoot` directory.

In case of the encodings of RTX environment is `GB2312`, So I have rewritten some of the PHP APIs, using `UTF-8` encoding.

Edit `nrhIPLimit.json` to fix your situation. Otherwise you will not get the privilege to access those APIs.

Coding:

```
// load the module
var rtx = require('rtx-helper');
// setup RTX API host and path prefix
// the default prefix is 'nrh', maybe you'll have it under a different path
rtx.init('rtx.example.com:8012'[, '{API_PATH_PREFIX}']);

// getUserList will get an array of the full user list.
// with each user item like {id:1001, name:'someone'}
rtx.getUserList(function(err, list){
  console.log(list);
});

// getState will get the online state of 'someone'
rtx.getState('someone', function (err, is_online) {
  console.log(is_online);
});

// sendNotify will send notify information to the `receivers`
// receivers should be a string of the username, or an array of usernames.
// msg should be a string. to send link, use this format: '[TEXT|LINK]'
rtx.sendNotify(['someone1', 'someone2'], '标题 - hello', '中文[百度|http://www.baidu.com]测试', 0, function (err, is_sent) {
  console.log(is_sent);
});
```