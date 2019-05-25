import youtube_dl
import sys
from common import ApiError
import json

def supported(url=None):
  if url is None:
    raise ApiError("Play url is undefined")

  ies = youtube_dl.extractor.gen_extractors()
  for ie in ies:
    if ie.suitable(url) and ie.IE_NAME != 'generic':
      return "Supported" 
  return "Not Supportred"


