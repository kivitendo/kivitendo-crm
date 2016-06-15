
//var getUrl = window.location;
//var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + '/';
var kivi_global = jQuery.parseJSON( kivi.myconfig.global_conf );
var baseUrl = kivi_global.baseurl;
var SERVER_URL = baseUrl + '/crm/ajax/postitall.php';

var getJsonRequest = function(params, callback) {
    iduser = otherid ? otherid : kivi.myconfig.id; //otherid is for share postits
    $.ajax({
        url: SERVER_URL + "?iduser=" + iduser, //+ "&format=json",
        data: params,
        //cache: true,
        error: function(data) {
            console.log('An error has occurred', data);
        },
        success: function(data) {
            //console.log(data.message);
            if(data.status == "success") {
                if(callback != null) callback(data.message);
            } else {
                alert('An error has occurred' + data);
                if(callback != null) callback(null);
            }
        },
        type: 'POST'
    });

};

// External Storage Manager via AJAX
var externalManager = {
    test: function(callback) {
        getJsonRequest("option=test", function(retVal) {
            if(retVal !== null) {
                callback(true);
            } else {
                callback(false);
            }
        });
    },
    add: function(obj, callback) {
        //alert( "add ");
        console.log( obj );
        var varname = 'PostIt_' + parseInt(obj.id, 10);
        //alert( varname );
        var testPrefs = encodeURIComponent(JSON.stringify(obj));
        //alert( testPrefs );
        var jsonfile = {};
        jsonfile[varname] = testPrefs;
        console.log('add', varname, testPrefs);
        getJsonRequest("option=add&key=" + varname + "&content=" + testPrefs, callback);
    },
    get: function(id, callback) {
        var varvalue;
        var varname = 'PostIt_' + parseInt(id, 10);
        getJsonRequest("option=get&key=" + varname, function(retVal) {
            try {
                varvalue = JSON.parse(retVal);
            } catch (e) {
                varvalue = "";
            }
            if(callback != null) callback(varvalue);
        });
    },
    remove: function(varname, callback) {
        //console.log('Remove',varname);
        varname = 'PostIt_' + parseInt(varname, 10);
        getJsonRequest("option=remove&key=" + varname, callback);
    },
    removeDom: function(options, callback) {
        var len = -1;
        var iteration = 0;
        var finded = false;
        if(options === undefined || typeof options === "function") {
            callback = options;
            options = {
                domain : window.location.origin,
                page : window.location.pathname
            };
        }
        var domain = options.domain;
        var page = options.page;
        var t = this;
        //console.log('hola?', domain, page, $.fn.postitall.globals.filter);
        t.getlength(function(len) {
            if(!len) {
                callback();
                return;
            }
            for (var i = 1; i <= len; i++) {
                t.key(i, function(key) {
                    t.getByKey(key, function(o) {
                        if(o != null) {
                            if($.fn.postitall.globals.filter == "domain")
                                finded = (getUrl(o.domain) === getUrl(domain));
                            else if($.fn.postitall.globals.filter == "page")
                                finded = (getUrl(o.domain) === getUrl(domain) && (o.page === page || page === undefined));
                            else
                                finded = true;
                            //console.log('finded', finded, o.domain, domain);
                            if (finded) {
                                t.remove(o.id);
                            }
                        }
                        if(iteration == (len - 1) && callback != null) {
                            callback();
                            callback = null;
                        }
                        iteration++;
                    });
                });
            }
        });
    },
    clear: function(callback) {
      var len = -1;
      var iteration = 0;
      var finded = false;
      var t = this;
      t.getlength(function(len) {
          if(!len) {
              callback();
              return;
          }
          for (var i = 1; i <= len; i++) {
            t.key(i, function(key) {
              t.getByKey(key, function(o) {
                if(o != null) {
                    t.remove(o.id);
                }
                if(iteration == (len - 1) && callback != null) {
                    callback();
                    callback = null;
                }
                iteration++;
              });
            });
          }
      });
    },
    getlength: function(callback) {
        var total = 0;
        //console.log('chromeManager.getlength');
        getJsonRequest("option=getlength", function(total) {
            callback(total);
        });
    },
    key: function (i, callback) {
        i--;
        getJsonRequest("option=key&key="+i, function(retVal) {
            //console.log('chromeManager.key ' + i, retVal);
            if(retVal)
                callback(retVal);
            else
                callback("");
        });
    },
    view: function () {
        //console.log('view chrome');
        getJsonRequest("option=getByKey&key="+key, function(retVal) {
            //console.log(retVal);
        });
    },
    getByKey: function (key, callback) {
          if (key != null && key.slice(0,7) === "PostIt_") {
              key = key.slice(7,key.length);
              getJsonRequest("option=getByKey&key="+key, callback);
          } else {
              if(callback != null) callback(null);
          }
    },
    getAll: function (callback) {
        getJsonRequest("option=getAll", callback);
    }
};
$.PostItAll.load();
