<!DOCTYPE html>
<html ng-app="myApp">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Royalbox</title>

    <!-- Mobile app -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="/img/chrome-touch-icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Royalbox">
    <link rel="apple-touch-icon-precomposed" href="/img/apple-touch-icon-precomposed.png">

    <!-- Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/jquery.webui-popover.css" rel="stylesheet">
    <link href="/css/layout.css" rel="stylesheet">
    <link href="/css/theme.css" rel="stylesheet">
  </head>
  <body>

    <app></app>

    <!-- function.bind() polyfill -->
    <script>
      if (!Function.prototype.bind) {
        Function.prototype.bind = function(oThis) {
          if (typeof this !== 'function') {
          // closest thing possible to the ECMAScript 5
          // internal IsCallable function
            throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
          }

          var aArgs   = Array.prototype.slice.call(arguments, 1),
            fToBind = this,
            fNOP    = function() {},
            fBound  = function() {
              return fToBind.apply(this instanceof fNOP
                ? this
                : oThis,
                aArgs.concat(Array.prototype.slice.call(arguments)));
            };

          fNOP.prototype = this.prototype;
          fBound.prototype = new fNOP();

           return fBound;
        };
      }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.3/fastclick.min.js"></script>
    <script src="js/jquery.webui-popover.js"></script>
    <script src="js/tooltip.js"></script>
    <script src="js/modal.js"></script>
    <script src="js/riot2.0.12.js"></script>
    <script src="js/riotcontrol.js"></script>
    <script src="js/cache.js"></script>
    <script src="js/Utils.js"></script>
    <script src="js/ChannelStore.js"></script>
    <script src="js/PlayerStore.js"></script>
    <script src="js/PlaylistStore.js"></script>
    <script src="js/JQueryStore.js"></script>
    <script src="js/Popovers.js"></script>

    <script src="app.html" type="riot/tag"></script>
    <script src="tags/topnav.html" type="riot/tag"></script>
    <script src="tags/itemlist.html" type="riot/tag"></script>
    <script src="tags/playbar.html" type="riot/tag"></script>
    <script src="tags/playerr.html" type="riot/tag"></script>
    <script src="tags/raw.html" type="riot/tag"></script>
    <script src="tags/backbtn.html" type="riot/tag"></script>
    <script src="tags/feedlist.html" type="riot/tag"></script>

    <script>
      var channelStore = new ChannelStore()
      var playerStore = new PlayerStore()
      var playlistStore = new PlaylistStore()
      var jQueryStore = new JQueryStore()
      RiotControl.addStore(channelStore)
      RiotControl.addStore(playerStore)
      RiotControl.addStore(playlistStore)
      RiotControl.addStore(jQueryStore)
      riot.compile(function() {
        riot.mount('app')
      })
    </script>

     <script>
      if ('addEventListener' in document) {
        document.addEventListener('DOMContentLoaded', function() {
          FastClick.attach(document.body);
        }, false);
      }
    </script>
  </body>
</html>

