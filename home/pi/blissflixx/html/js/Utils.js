var Utils = {
  rpc: function(module, fn, data, cb) {
    var url = '/api/' +  module 
    if(module === 'channels' || module === 'playr') {
	url = 'http://www.royalbox.tv:6969/api/' + module
    }
    if(fn === 'play' || fn === 'status' || fn === 'control') {
      url = '/api/' +  module 
    }
    payload = { fn: fn }
    if (data) payload['data'] = JSON.stringify(data)
    $.getJSON(url, payload, function(data) {
      cb(null, data)
    }).fail(function(xhr, textStatus) {
      var msg = '' 
      if (xhr.status === 0) {
        return cb(Utils.createError('offline','offline'))
      }
      else if (xhr.responseText.lastIndexOf('{', 0) === 0) {
        obj = JSON.parse(xhr.responseText)
        msg = obj.error
      } else {
        msg = xhr.responseText
      }
      cb(Utils.createError(textStatus + ": " + xhr.status, msg))
    })
  },
  goRoute: function() {
    var route = arguments[0]
    if(route === 'stream') {
      route = 'api/play'
    }
    for (i=1; i<arguments.length; i++) {
      route += '/' + encodeURIComponent(arguments[i])
    }
    riot.route(route)
  },
  createError: function(type, msg) {
    return '/' + encodeURIComponent(type) + '/' + encodeURIComponent(msg)
  },
  showError: function(err) {
    riot.route('error' + err)
  },
  decodeURI: function(val) {
    val = decodeURIComponent(val)
    if (val === 'undefined') val = ''
    return val
  },
}
