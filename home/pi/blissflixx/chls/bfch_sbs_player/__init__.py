from chanutils import get_xml, get_doc, select_all, select_one, get_json
from chanutils import get_attr, get_text, get_text_content
from playitem import PlayItem, PlayItemList, MoreEpisodesAction
import requests, lxml.html, re
import json
import urllib

_SEARCH_URL = 'https://www.sbs.com.au/api/video_search/v2/?m=1&q='

BASE = "https://www.sbs.com.au"
FULL_VIDEO_LIST = BASE + "/api/video_search/v2/?m=1&filters={section}{Programs}"
VIDEO_URL = BASE + "/ondemand/video/single/%s"

NS = {
    "smil": "http://www.w3.org/2005/SMIL21/Language",
} 

_FEEDLIST = [
  {'title':'SBS 1-50', 'url':'https://www.sbs.com.au/api/video_search/v2/?m=1&filters={section}{Programs}&range=1-50'},
  {'title':'SBS 51-100', 'url':'https://www.sbs.com.au/api/video_search/v2/?m=1&filters={section}{Programs}&range=51-100'},
  {'title':'SBS 101-150', 'url':'https://www.sbs.com.au/api/video_search/v2/?m=1&filters={section}{Programs}&range=101-150'},
  {'title':'SBS 151-200', 'url':'https://www.sbs.com.au/api/video_search/v2/?m=1&filters={section}{Programs}&range=151-200'},
  {'title':'SBS 201-250', 'url':'https://www.sbs.com.au/api/video_search/v2/?m=1&filters={section}{Programs}&range=201-250'},
  {'title':'SBS Latest TV', 'url':'https://www.sbs.com.au/api/video_feed/f/Bgtm9B/sbs-section-sbstv'},
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
  return 'SBS On Demand'

def image():
  return 'icon.png'

def description():
   return "SBS On Demand (<a target='_blank' href='https://www.sbs.com.au/'>https://www.sbs.com.au</a>). Geo-restricted to AUS."

def feedlist():
  return _FEEDLIST


def download(video_link):
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

def search(q):
  url = _SEARCH_URL + q + "&range=1-50"
  count = 0
  results = PlayItemList()
  shows = get_json(url)
  for ep_info in shows["entries"]:
    video_key = ep_info["id"]
    title = ep_info["title"]
    description = ep_info["description"]
    thumbnail = ep_info["plmedia$defaultThumbnailUrl"]
    video_url = VIDEO_URL % video_key.split("/")[-1]
    item = PlayItem(title, thumbnail, video_url, description)
    results.add(item)

  return results

def feed(idx):
  url = _FEEDLIST[idx]['url']
  count = 0 
  results = PlayItemList()
  shows = get_json(url)
  for ep_info in shows["entries"]:
    video_key = ep_info["id"]
    title = ep_info["title"]
    description = ep_info["description"] 
    thumbnail = ep_info["plmedia$defaultThumbnailUrl"]
    video_url = VIDEO_URL % video_key.split("/")[-1] 
    item = PlayItem(title, thumbnail, video_url, description) 
    results.add(item)

  return results
