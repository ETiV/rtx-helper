/**
 * Created by ETiV on 2014/5/15.
 */

var util = require('util');
var request = require('request');

var API_CONFIG = {
  SEND_NOTIFY: {
    URI: 'nrhSendNotify.php',
    METHOD: 'POST',
    ARGS_KEYS: ['receivers', 'title', 'msg', 'delaytime']
  },
  GET_STATE: {
    URI: 'nrhGetState.php',
    METHOD: 'GET',
    ARGS_KEYS: ['username']
  },
  GET_USER_LIST: {
    URI: 'nrhUserList.php',
    METHOD: 'GET',
    ARGS_KEYS: []
  }
};

var HTTP_HOST = '';
var API_PREFIX = '/nrh';
var make_url = function (http_host, uri) {
  if (HTTP_HOST == '') {
    throw new Error('HTTP_HOST MUST BE SET TO NO EMPTY STRING. SET THROUGH .init(http_host)');
  }
  return 'http://' + http_host + API_PREFIX + '/' + uri;
};

var base = function (api_conf, params_obj, cb) {

  if (typeof cb == 'undefined') {
    if (typeof params_obj == 'function') {
      cb = params_obj;
    }
    if (typeof params_obj == 'undefined') {
      params_obj = {};
    }
  }

  if (typeof cb != 'function') {
    cb = new Function();
  }

  if (api_conf.hasOwnProperty('URI') && api_conf.hasOwnProperty('METHOD')) {
    var url = make_url(HTTP_HOST, api_conf['URI']);
    var form = {};
    form['encoding'] = 'UTF-8';
    for (var i = 0; i < api_conf['ARGS_KEYS'].length; i++) {
      if (params_obj.hasOwnProperty(api_conf['ARGS_KEYS'][ i ])) {
        form[ api_conf['ARGS_KEYS'][ i ] ] = params_obj[ api_conf['ARGS_KEYS'][ i ] ];
      }
    }

    var payload = {};
    payload['timeout'] = 1000;
    payload['method'] = api_conf['METHOD'];
    payload[ api_conf['METHOD'] == 'GET' ? 'qs' : 'form' ] = form;

    request(url, payload, function (req, resp, body) {
      if (req instanceof Error) {
        cb(req);
      } else {
        if (body.indexOf('Uncaught exception') == -1) {
          cb(null, resp.statusCode, String(body).trim());
        } else {
          var now = Date.now();
          console.error('======== LOG: ' + (now) + ' ========');
          console.error(body);
          console.error('======== LOG: ' + (now) + ' ========\n');


          cb(new Error('Remote RTX Server Error. See LOG: ' + now));
        }
      }
    });
  } else {
    return false;
  }
};

module.exports = {
  SEND_NOTIFY_TO_ALL: 'NRH_SEND_NOTIFY_TO_ALL',
  init: function (http_host, api_prefix) {
    HTTP_HOST = http_host;
    if (!!api_prefix)
      API_PREFIX = api_prefix;
  },
  getUserList: function (cb) {
    base(API_CONFIG.GET_USER_LIST, function (err, statusCode, body) {
      if (err) {
        cb(err);
      } else {
        if (statusCode == 200) {
          var json = {};
          try {
            json = JSON.parse(body);
          } catch (e) {
            cb(new Error('Response Body is NOT JSON.'));
          }
          cb(null, json['list'] || []);
        } else {
          cb(new Error('Response Status is NOT 200.'));
        }
      }
    });
  },
  getState: function (username, cb) {
    base(API_CONFIG.GET_STATE, {username: username || '-'}, function (err, statusCode, body) {
      if (err) {
        cb(err);
      } else {
        if (statusCode == 200) {
          var json = {};
          try {
            json = JSON.parse(body);
          } catch (e) {
            cb(new Error('Response Body is NOT JSON.'));
          }
          cb(null, json['state'] == '1');
        } else {
          cb(new Error('Response Status is NOT 200.'));
        }
      }
    });
  },
  sendNotify: function (receivers, title, msg, delaytime_msec, cb) {
    var payload = {};

    if (util.isArray(receivers)) {
      receivers = receivers.join(',');
    }

    if (!!receivers) {
      payload['receivers'] = receivers;
    }

    if (!!title) {
      payload['title'] = title;
    }

    if (!!msg) {
      payload['msg'] = msg;
    }

    if (!!delaytime_msec) {
      payload['delaytime'] = delaytime_msec;
    } else {
      payload['delaytime'] = 0;
    }

    base(API_CONFIG.SEND_NOTIFY, payload, function (err, statusCode, body) {
      if (err) {
        cb(err);
      } else {
        if (statusCode == 200) {
          var json = {};
          try {
            json = JSON.parse(body);
          } catch (e) {
            cb(new Error('Response Body is NOT JSON.'));
          }
          cb(null, json['status'] == 200);
        } else {
          cb(new Error('Response Status is NOT 200.'));
        }
      }
    });
  }
};
