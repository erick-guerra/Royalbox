function _Popover(e, html, events) {
  var self = this

  this.hide = function() {
    if (self.shown) self.popover.toggle()
  }

  target = $(e.target)
  target.webuiPopover({content:html, animation:'pop'})
  self.popover = target.data('plugin_webuiPopover')
  target.on('shown.webui.popover', function() {
    self.shown = true
    events()
  })
  RiotControl.on('jq_hide', function() {
    if (self.shown) {
      self.shown = false
      self.popover.destroy()
      RiotControl.off('jq_hide')
    }
  })
  self.popover.toggle(e)
}


var Popovers = new function() {
  var self = this

  this._getPlaylistOptions = function(playlists) {
    var html = ''
    for (i=0; i<playlists.length; i++) {
      var pl = playlists[i]
      if (!pl.url) { /* Not remote playlists */
        html += '<option value="' + pl.plid + '">' + pl.title + '</option>'
      }
    }
    return html
  }

  this._newPlaylistHtml = function() {
    html = '<div>New Playlist:</div>'
    return html + '<form id="new-playlist"><input id="new-playlist-name" class="form-control" type="text" required=""></form>'
  }

  this._newRemotePlaylistHtml = function() {
    html = '<div>Remote Playlist URL:</div>'
    return html + '<form id="new-remote-playlist"><input id="new-remote-playlist-url" class="form-control" type="text" required=""></form>'
  }

  this._addToPlaylistHtml = function() {
    var html = ''
    var playlists = playlistStore.getCachedPlaylists()
    if (playlists.length > 0) {
      html += '<select id="existing-playlist" class="form-control">'
      html += '<option value="">Choose existing playlist...</option>'
      html += self._getPlaylistOptions(playlists)
      html += '</select>'
    }
    return html + self._newPlaylistHtml()
  }

  this._areYouSureHtml = function() {
    var html = '<div>Are You Sure?</div>'
    html += '<div>'
    html += '<button id="yes-button" type="button" class="btn btn-warning btn-spacer-right">Yes</button>'
    html += '<button id="no-button" type="button" class="btn btn-default">No</button>'
    html += '</div'
    return html
  }

  this._subsTypeHtml = function() {
    var html = '<div>Is this a Movie or TV Show?</div>'
    html += '<div>'
    html += '<button id="movie-button" type="button" class="btn btn-warning btn-spacer-right">Movie</button>'
    html += '<button id="tv-button" type="button" class="btn btn-default">TV</button>'
    html += '</div'
    return html
  }

  this.addToPlaylist = function(e, cb) {
    popover = new _Popover(e, self._addToPlaylistHtml, function() {
      $("#existing-playlist").change(function(e) {
        var value = e.target.value
        if (value) {
          popover.hide()
          cb(false, value)
        }
      })
      $("#new-playlist").submit(function(e) {
        e.preventDefault()
        var value = $("#new-playlist-name").val()
        if (value) {
          popover.hide()
          cb(true, value)
        }
      })
    })
  }

  this.newPlaylist = function(e, cb) {
    popover = new _Popover(e, self._newPlaylistHtml, function() {
      $("#new-playlist").submit(function(e) {
        e.preventDefault()
        var value = $("#new-playlist-name").val()
        if (value) {
          popover.hide()
          cb(value)
        }
      })
    })
  }

  this.newRemotePlaylist = function(e, cb) {
    popover = new _Popover(e, self._newRemotePlaylistHtml, function() {
      $("#new-remote-playlist").submit(function(e) {
        e.preventDefault()
        var value = $("#new-remote-playlist-url").val()
        if (value) {
          popover.hide()
          cb(value)
        }
      })
    })
  }

  this.subsType = function(e, cb) {
    popover = new _Popover(e, self._subsTypeHtml, function() {
      $("#movie-button").click(function(e) {
        popover.hide()
        cb("movie")
      })
      $("#tv-button").click(function(e) {
        popover.hide()
        cb("tv")
      })
    })
  }

  this.areYouSure = function(e, cb) {
    popover = new _Popover(e, self._areYouSureHtml, function() {
      $("#yes-button").click(function(e) {
        popover.hide()
        cb(true)
      })
      $("#no-button").click(function(e) {
        popover.hide()
        cb(false)
      })
    })
  }
} ()
