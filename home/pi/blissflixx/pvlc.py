#!/usr/bin/python
from os import path
import sys, os
LIB_PATH = path.join(path.abspath(path.dirname(__file__)), "lib")
CHAN_PATH = path.join(path.abspath(path.dirname(__file__)), "chls")
YTDL_PATH = path.join(path.abspath(path.dirname(__file__)), "lib/youtube-dl")
print LIB_PATH
sys.path.append(LIB_PATH)
sys.path.append(CHAN_PATH)
sys.path.append(YTDL_PATH)

import subprocess
import youtube_dl


player_run = False
p = None


def hook(status):
    global player_run, p

    if player_run == False:
        player_run = True
        p = subprocess.Popen(['omxplayer', status['filename']])


opts = {
    'format': 'best[height<720]',
    'nopart': True,
    'progress_hooks': [hook],
    'verbose': False,
    'hls-use-mpegts':True,
}

with youtube_dl.YoutubeDL(opts) as ydl:
    #ydl.download(['https://www.nbc.com/the-blacklist/video/anna-mcmahon/3950441'])
    ydl.download(['https://www.youtube.com/watch?v=mU4hV50rkVE'])

p.wait()
