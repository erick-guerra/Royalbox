from chanutils import get_doc, select_all, select_one, get_attr, get_text, get_text_content
from playitem import PlayItem, PlayItemList, MoreEpisodesAction

_SEARCH_URL = 'http://www.bbc.co.uk/iplayer/search'

_FEEDLIST = [
  {'title':'Most Popular','url':'https://www.bbc.co.uk/iplayer/most-popular'},
  {'title':'BBC One','url':'https://www.bbc.co.uk/bbcone'},
  {'title':'BBC Two','url':'https://www.bbc.co.uk/bbctwo'},
  {'title':'BBC Three','url':'https://www.bbc.co.uk/tv/bbcthree'},
  {'title':'BBC Four','url':'https://www.bbc.co.uk/bbcfour'},  
  {'title':'Arts','url':'http://www.bbc.co.uk/iplayer/categories/arts/all?sort=dateavailable'},
  {'title':'CBBC','url':'http://www.bbc.co.uk/iplayer/categories/cbbc/all?sort=dateavailable'},
  {'title':'CBeebies','url':'http://www.bbc.co.uk/iplayer/categories/cbeebies/all?sort=dateavailable'},
  {'title':'Comedy','url':'http://www.bbc.co.uk/iplayer/categories/comedy/all?sort=dateavailable'},
  {'title':'Documentaries','url':'http://www.bbc.co.uk/iplayer/categories/documentaries/all?sort=dateavailable'},
  {'title':'Drama & Soaps','url':'http://www.bbc.co.uk/iplayer/categories/drama-and-soaps/all?sort=dateavailable'},
  {'title':'Entertainment','url':'http://www.bbc.co.uk/iplayer/categories/entertainment/all?sort=dateavailable'},
  {'title':'Films','url':'http://www.bbc.co.uk/iplayer/categories/films/all?sort=dateavailable'},
  {'title':'Food','url':'http://www.bbc.co.uk/iplayer/categories/food/all?sort=dateavailable'},
  {'title':'History','url':'http://www.bbc.co.uk/iplayer/categories/history/all?sort=dateavailable'},
  {'title':'Lifestyle','url':'http://www.bbc.co.uk/iplayer/categories/lifestyle/all?sort=dateavailable'},
  {'title':'Music','url':'http://www.bbc.co.uk/iplayer/categories/music/all?sort=dateavailable'},
  {'title':'News','url':'http://www.bbc.co.uk/iplayer/categories/news/all?sort=dateavailable'},
  {'title':'Science & Nature','url':'http://www.bbc.co.uk/iplayer/categories/science-and-nature/all?sort=dateavailable'},
  {'title':'Sport','url':'http://www.bbc.co.uk/iplayer/categories/sport/all?sort=dateavailable'},
]

def name():
  return 'BBC iPlayer'

def image():
  return 'icon.png'

def description():
  return "BBC iPlayer Channel (<a target='_blank' href='http://www.bbc.co.uk/iplayer'>http://www.bbc.co.uk/iplayer</a>). Geo-restricted to UK."

def feedlist():
  return _FEEDLIST

def feed(idx):
  doc = get_doc(_FEEDLIST[idx]['url'])
  if idx == 0:
    return _extract_grid(idx, doc)
    #return _extract_popular(doc)
  else:
    return _extract_grid(idx, doc)

def search(q):
  doc = get_doc(_SEARCH_URL, params = { 'q':q })
  return _extract_grid(1, doc)

def showmore(link):
  print "show more => " + link
  doc = get_doc(link)
  return _extract_grid(1, doc)

def _extract_popular(doc):
  rtree = select_all(doc, 'li.most-popular__item')
  results = PlayItemList()
    
  for l in rtree:
    a = select_one(l, 'a')
    url = get_attr(a, 'href')
    if url is None:
      continue
    if url.startswith('/iplayer'):
      url = "http://www.bbc.co.uk" + url
    idiv = select_one(l, 'div.rs-image')
    idiv = select_one(idiv, 'source')
    img = get_attr(idiv, 'srcset')
    img = img.split()[0]

    idiv = select_one(l, 'div.content-item__info__text')
    title = get_text(select_one(idiv, 'div.content-item__title'))
    pdiv = select_one(idiv, 'div.content-item__info__primary')
    subtitle = get_text(select_one(pdiv, 'div.content-item__description'))
    pdiv = select_one(idiv, 'div.content-item__info__secondary')
    synopsis= get_text(select_one(pdiv, 'div.content-item__description'))
    item = PlayItem(title, img, url, subtitle, synopsis)
    results.add(item)
  return results

def _extract_grid(idx, doc):
  rtree = select_all(doc, 'li.grid__item')
  results = PlayItemList()
  
  count = 0
  for l in rtree:
    a = select_one(l, 'a')
    url = get_attr(a, 'href')
    if url is None:
      continue
    if url.startswith('/iplayer'):
      url = "http://www.bbc.co.uk" + url
    idiv = select_one(l, 'div.rs-image')
    idiv = select_one(idiv, 'source')
    img = get_attr(idiv, 'srcset').split()[0]

    sdiv = select_one(l, 'div.content-item__info__text')
    avail = select_one(sdiv, 'div.content-item__labels')
    if get_text_content(avail) == "Not available":
      continue

    title = get_text_content(select_one(sdiv, 'div.content-item__title'))
    subtitle = get_text_content(select_one(sdiv, 'div.content-item__description'))
    if title.endswith("..."):
      title = title[:-3]
    if subtitle.endswith("..."):
      subtitle = subtitle[:-3]

    item = PlayItem(title, img, url, subtitle)
    a = select_one(l, 'a.js-view-all-episodes')
    if a is not None:
      link = "http://bbc.co.uk" + a.get('href')
      item.add_action(MoreEpisodesAction(link, title))

    if count == 0:
      seriestree = select_all(doc, 'li.scrollable-nav__item')
      for st in seriestree:
        a = select_one(st, 'a')
	url = get_attr(a, 'href')
	if url is None:
	  continue
	if url.startswith('/iplayer'):
	  url = "http://www.bbc.co.uk" + url
	sdiv = select_one(st, 'span.button__text')
	title = get_text_content(sdiv)
	if title is None:
	  continue 
	item.add_action(MoreEpisodesAction(url, title))
    count = count + 1
    results.add(item)

  if idx is not None:
    if idx == 1:
      item1 = PlayItem("BBC One", "https://static.bbci.co.uk/tviplayer/img/navigation/bbcone.svg", "https://a.files.bbci.co.uk/media/live/manifesto/audio_video/simulcast/hls/uk/hls_mobile_wifi_rw/aks/bbc_one_london.m3u8", "BBC One Live", "Watch Live TV")
      results.add(item1)
    elif idx == 2:
      item2 = PlayItem("BBC Two", "https://static.bbci.co.uk/tviplayer/img/navigation/bbctwo.svg", "https://a.files.bbci.co.uk/media/live/manifesto/audio_video/simulcast/hls/uk/hls_mobile_wifi_rw/llnws/bbc_two_england.m3u8", "BBC Two Live", "Watch Live TV")
      results.add(item2)
    elif idx == 4:
      #item3 = PlayItem("BBC Three", "https://static.bbci.co.uk/tviplayer/img/navigation/bbcfour.svg", "https://a.files.bbci.co.uk/media/live/manifesto/audio_video/simulcast/hls/uk/hls_mobile_wifi_rw/llnws/bbc_four.m3u8", "BBC Four Live", "Watch Live TV")
      #results.add(item3)
      item4 = PlayItem("BBC Four", "https://static.bbci.co.uk/tviplayer/img/navigation/bbcfour.svg", "https://a.files.bbci.co.uk/media/live/manifesto/audio_video/simulcast/hls/uk/hls_mobile_wifi_rw/llnws/bbc_four.m3u8", "BBC Four Live", "Watch Live TV")
      results.add(item4)
    elif idx == 17:
      item5 = PlayItem("BBC News", "https://static.bbci.co.uk/tviplayer/img/navigation/bbcnews.svg", "https://a.files.bbci.co.uk/media/live/manifesto/audio_video/simulcast/hls/uk/hls_mobile_wifi_rw/aks/bbc_news24.m3u8", "BBC News Live", "Watch Live TV")
      results.add(item5)

  return results

def _extract(doc):
  rtree = select_all(doc, 'li.list-item')
  results = PlayItemList()
  for l in rtree:
    a = select_one(l, 'a')
    url = get_attr(a, 'href')
    if url is None:
      continue
    if url.startswith('/iplayer'):
      url = "http://www.bbc.co.uk" + url

    pdiv = select_one(l, 'div.primary')
    idiv = select_one(pdiv, 'div.r-image')
    if idiv is None:
      idiv = select_one(pdiv, 'div.rs-image')
      idiv = select_one(idiv, 'source')
      img = get_attr(idiv, 'srcset')
    else:
      img = get_attr(idiv, 'data-ip-src')

    sdiv = select_one(l, 'div.secondary')
    title = get_text(select_one(sdiv, 'div.title'))
    subtitle = get_text(select_one(sdiv, 'div.subtitle'))
    synopsis = get_text(select_one(sdiv, 'p.synopsis'))
    item = PlayItem(title, img, url, subtitle, synopsis)
    a = select_one(l, 'a.view-more-container')
    if a is not None:
      link = "http://bbc.co.uk" + a.get('href')
      item.add_action(MoreEpisodesAction(link, title))
    results.add(item)
  return results
