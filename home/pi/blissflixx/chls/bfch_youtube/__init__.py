import chanutils.reddit
from chanutils import get_json
from playitem import PlayItem, PlayItemList

_SEARCH_URL = "https://www.googleapis.com/youtube/v3/search"

_FEEDLIST = [
  {'title':'Trending', 'url':'http://www.reddit.com/domain/youtube.com/top/.json'},
  {'title':'Popular', 'url':'https://www.googleapis.com/youtube/v3/videos?maxResults=50&key=AIzaSyAzkfoVmKXf3520e5WLBMnOMXXbyjIMJLk&part=snippet&chart=mostPopular'},
]

def name():
  return 'Youtube'

def image():
  return "icon.png"

def description():
  return "Youtube Channel (<a target='_blank' href='https://www.youtube.com/'>https://www.youtube.com/</a>)."

def feedlist():
  return _FEEDLIST

def feed(idx):
  url = _FEEDLIST[idx]['url']
  if url.endswith('.json'):
    return chanutils.reddit.get_feed(_FEEDLIST[idx])
  else:
    data = get_json(url)
    return _extract(data)

def search(q):
  query = {'part':'snippet', 'q':q, 'maxResults': 50,
	    'key':'AIzaSyAzkfoVmKXf3520e5WLBMnOMXXbyjIMJLk'}
  data = get_json(_SEARCH_URL, params=query)
  return _extract(data)

def _extract(data):
  results = PlayItemList()
  rtree = data['items']
  for r in rtree:
    title= r['snippet']['title']
    subtitle= r['snippet']['publishedAt'][:10]
    synopsis= r['snippet']['description']
    if len(synopsis) > 200:
      synopsis = synopsis[:200] + "..."
    img = r['snippet']['thumbnails']['default']['url']
    if isinstance(r['id'], basestring):
      vid = r['id']
    elif 'videoId' in r['id']:
      vid = r['id']['videoId']
    else:
      continue
    url = 'https://www.youtube.com/watch?v=' + vid
    results.add(PlayItem(title, img, url, subtitle, synopsis))
  return results
