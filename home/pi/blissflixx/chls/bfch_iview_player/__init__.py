from chanutils import get_xml, get_doc, select_all, select_one, get_json
from chanutils import get_attr, get_text, get_text_content
from playitem import PlayItem, PlayItemList, MoreEpisodesAction
import requests, lxml.html, re

_SEARCH_URL = 'https://iview.abc.net.au/api/search/?keyword='

API_URL = "http://iview.abc.net.au/api"
AUTH_URL = "http://iview.abc.net.au/auth"

_FEEDLIST = [
  {'title':'ABC 1', 'url':'http://iview.abc.net.au/api/channel/abc1'},
  {'title':'ABC 2', 'url':'http://iview.abc.net.au/api/channel/abc2'},
  {'title':'ABC 3', 'url':'http://iview.abc.net.au/api/channel/abc3'},
  {'title':'ABC 4 Kids', 'url':'http://iview.abc.net.au/api/channel/abc4kids'},
  {'title':'iView Exclusives', 'url':'http://iview.abc.net.au/api/channel/iview'},
]

try:
    raw_input
except NameError:
    raw_input = input


def format_episode_title(series, ep):
    if ep:
        return series + " " + ep
    else:
        return series

def name():
  return 'ABC iView Player'

def image():
  return 'icon.png'

def description():
   return "ABC iView Channel (<a target='_blank' href='https://iview.abc.net.au/'>https://iview.abc.net.au</a>). Geo-restricted to AUS."

def feedlist():
  return _FEEDLIST

def find_hls_url(playlist,video_key):
    print playlist
    for video in playlist:
        if video["type"] in ["program","livestream"]:
            for quality in ["hls-plus", "hls-high", "hls-low"]:
                if quality in video:
                    return video[quality].replace("http:", "https:")
    raise Exception("Missing program stream for " + video_key)

def get_auth_details():
    #doc = get_doc(AUTH_URL)
    #doc = urlopen(AUTH_URL).read()
    #doc = get_xml(AUTH_URL)
    #auth_doc = lxml.etree.parse(doc.text, lxml.etree.XMLParser(encoding="utf-8", recover=True))
    auth_doc = get_xml(AUTH_URL)
    #auth_doc = grab_xml(AUTH_URL, 0)

    NS = {
        "auth": "http://www.abc.net.au/iView/Services/iViewHandshaker",
    }
    token = auth_doc.xpath("//auth:tokenhd/text()", namespaces=NS)[0]
    token_url = auth_doc.xpath("//auth:server/text()", namespaces=NS)[0]
    token_hostname = urllib.parse.urlparse(token_url).netloc
    return token, token_hostname

def add_auth_token_to_url(video_url, token, token_hostname):
    parsed_url = urllib.parse.urlparse(video_url)
    hacked_url = parsed_url._replace(netloc=token_hostname, query="hdnea=" + token)
    video_url = urllib.parse.urlunparse(hacked_url)
    return video_url

def download(video_key):
    info = get_json(API_URL + "/programs/" + video_key)
    if "playlist" not in info:
        return False
    print info
    video_url = find_hls_url(info["playlist"],video_key)
    token, token_hostname= get_auth_details()
    video_url = add_auth_token_to_url(video_url, token, token_hostname)
    return video_url

def search(q):
  url = _SEARCH_URL + q
  count = 0
  results = PlayItemList()
  shows = get_json(url)
  for ep_info in shows:
    video_key = ep_info["episodeHouseNumber"]
    series_title = ep_info["seriesTitle"]
    title = ep_info.get("title", None)
    episode_title = format_episode_title(series_title, title)
    thumbnail = ep_info["thumbnail"]
    video_url = API_URL + "/programs/" + video_key
    item = PlayItem(series_title, thumbnail, video_url, title, episode_title)
    results.add(item)

  return results

def feed(idx):
  url = _FEEDLIST[idx]['url']
  count = 0 
  results = PlayItemList()
  shows = get_json(url)
  for index_list in shows["index"]:
    for ep_info in index_list["episodes"]:
      video_key = ep_info["episodeHouseNumber"]
      series_title = ep_info["seriesTitle"]
      title = ep_info.get("title", None)
      #episode_title = format_episode_title(series_title, title)
      episode_title = ep_info["description"] 
      thumbnail = ep_info["thumbnail"]
      video_url = API_URL + "/programs/" + video_key 
      item = PlayItem(series_title, thumbnail, video_url, title, episode_title) 
      if ep_info["livestream"] == "0":
        results.add(item)

  return results
