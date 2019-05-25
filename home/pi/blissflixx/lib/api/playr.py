import sys
from player import Player
from chanutils import get_xml, get_doc, select_all, select_one, get_json
from chanutils import get_attr, get_text, get_text_content
from common import ApiError
import re, chanutils.torrent
import extractor, cherrypy, urlparse, settings
from os.path import splitext, basename
from StringIO import StringIO
import json

API_URL = "http://iview.abc.net.au/api"
AUTH_URL = "http://iview.abc.net.au/auth"

BASE = "https://www.sbs.com.au"
FULL_VIDEO_LIST = BASE + "/api/video_search/v2/?m=1&filters={section}{Programs}"
VIDEO_URL = BASE + "/ondemand/video/single/%s"

NS = {
    "smil": "http://www.w3.org/2005/SMIL21/Language",
}

try:
    raw_input
except NameError:
    raw_input = input


def download_sbs(video_link):
    doc = get_doc(video_link)
    player_params = get_player_params(doc)
    release_url = player_params["releaseUrls"]["html"]

    doc = get_xml(release_url)
    video = doc.xpath("//smil:video", namespaces=NS)[0]
    video_url = video.attrib["src"]
    if not video_url:
        raise Exception("Unsupported video : %s" % (video_link))
    return video_url

def get_player_params(doc):
    for script in doc.xpath("//script"):
        if not script.text:
            continue
        for line in script.text.split("\n"):
            s = "var playerParams = {"
            if s in line:
                p1 = line.find(s) + len(s) - 1
                p2 = line.find("};", p1) + 1
                if p1 >= 0 and p2 > 0:
                    return json.loads(line[p1:p2])
    raise Exception("Unable to find player params ")

def find_hls_url(playlist,video_key):
    print playlist
    for video in playlist:
        if video["type"] in ["program","livestream"]:
            for quality in ["hls-plus", "hls-high", "hls-low"]:
                if quality in video:
                    return video[quality].replace("http:", "https:")
    raise Exception("Missing program stream for " + video_key)

def get_auth_details():
    auth_doc = get_xml(AUTH_URL)
    #auth_doc = lxml.etree.parse(doc, lxml.etree.XMLParser(encoding="utf-8", recover=True))
    #auth_doc = grab_xml(AUTH_URL, 0)
    NS = {
        "auth": "http://www.abc.net.au/iView/Services/iViewHandshaker",
    }
    token = auth_doc.xpath("//auth:tokenhd/text()", namespaces=NS)[0]
    token_url = auth_doc.xpath("//auth:server/text()", namespaces=NS)[0]
    obj = urlparse.urlparse(token_url)
    token_hostname = obj.netloc
    return token, token_hostname

def add_auth_token_to_url(video_url, token, token_hostname):
    parsed_url = urlparse.urlparse(video_url)
    hacked_url = parsed_url._replace(netloc=token_hostname, query="hdnea=" + token)
    video_url = urlparse.urlunparse(hacked_url)
    return video_url

def download(video_url):
    info = get_json(video_url)
    if "playlist" not in info:
        return False
    print info
    new_video_url = find_hls_url(info["playlist"],video_url)
    token, token_hostname= get_auth_details()
    new_video_url = add_auth_token_to_url(new_video_url, token, token_hostname)
    return new_video_url

def _save_subs_prefs(subs):
  if 'lang' in subs:
    settings.save('subtitles', {'lang':subs['lang']})

def playOnRB(url=None, title=None, subs=None):
  if url is None:
    raise ApiError("Play url is undefined")
  if subs is not None:
    _save_subs_prefs(subs)
  obj = urlparse.urlparse(url)
  #print "url=>" + url
  Player.playYtdl(url, title, subs)

def play(url=None, title=None, subs=None):
  if url is None:
    raise ApiError("Play url is undefined")
  if subs is not None:
    _save_subs_prefs(subs)
  obj = urlparse.urlparse(url)
  #extension = urlparse.urlparse(url)
  filename, file_ext = splitext(basename(obj.path))
  if obj.scheme == "file":
    Player.playLocalFile(obj.path, title)
  elif obj.netloc == "www.twitch.tv":
    Player.playLivestream(url, title)
  elif obj.netloc == "www.gplexdb.net":
    #print "url=>" + url
    #print obj.netloc
    #print file_ext
    if file_ext in ['.m3u8']:
      head, sep, tail = url.partition('/http')
      tail = 'http' + tail
      #newurl = tail.split('&rocket', 1)[0]
      #newurl = tail.split('?rocket', 1)[0]
      newurl = "http://www.royalbox.tv/loadiplayer6.php/" + tail
      #print "newurl=>" + newurl
      #print "tail=>" + tail
      Player.playNoProxy(url, title, subs)
      #Player.playLive(tail, title, subs)
    else:
      Player.playYtdl(url, title, subs)
  elif chanutils.torrent.is_torrent_url(url):
    Player.playTorrent(url, chanutils.torrent.torrent_idx(url), title, subs)
  elif file_ext in ['.m3u8']:
    print "live url=>" + url
    Player.playLive(url, title, subs)
  else:
    #print "youtube url=>" + url
    Player.playYtdl(url, title, subs)

def playOnDevice(url=None, title=None, subs=None):
  if url is None:
    raise ApiError("Play url is undefined")
  if subs is not None:
    _save_subs_prefs(subs)
  obj = urlparse.urlparse(url)
  #extension = urlparse.urlparse(url)
  filename, file_ext = splitext(basename(obj.path))
  if obj.scheme == "file":
    Player.playLocalFile(obj.path, title)
  elif obj.netloc == "www.twitch.tv":
    Player.playLivestream(url, title)
  elif obj.netloc == "www.sbs.com.au":
     newurl = download_sbs(url)
     print "playOnDevice newurl=>" + newurl
     return Player.playURL2(newurl, title, subs)
  elif obj.netloc == "www.gplexdb.net":
    #print "url=>" + url
    #print obj.netloc
    #print file_ext
    if file_ext in ['.m3u8']:
      head, sep, tail = url.partition('/http')
      tail = 'http' + tail
      newurl = "http://www.royalbox.tv/loadiplayer6.php/" + tail
      return Player.playNoProxy2(url, title, subs)
    else:
      return Player.playYtdl2(url, title, subs)
  elif chanutils.torrent.is_torrent_url(url):
    Player.playTorrent(url, chanutils.torrent.torrent_idx(url), title, subs)
  elif file_ext in ['.m3u8']:
    #print "url=>" + url
    return Player.playLive2(url, title, subs)
  else:
    #print "url=>" + url
    return Player.playYtdl2(url, title, subs)

def control(action=None):
  if action is None:
    raise ApiError("Action is undefined")
  Player.control(action)

def status():
  return Player.status()
