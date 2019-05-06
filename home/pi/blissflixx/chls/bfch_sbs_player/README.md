# AusTV-WebDL #

Forked from DELX-WEBDL these are a set of Python scripts to grab video from online Free To Air Australian channels.

All channels currently work with the execption of Channel 7. 

Feel free to help out and get this working

## Requirements

* [Livestreamer](http://docs.livestreamer.io/install.html)
* python 2.7 or 3.2+
* pycrypto -- Livestreamer needs this for some videos
* python-lxml
* ffmpeg / libav-tools

## Instructions

### Arch Linux
    pacman -S livestreamer python-crypto python-lxml ffmpeg

### Ubuntu
    apt-get install livestreamer python-crypto python-lxml libav-tools

### Mac OS X

Warning, this is untested!

    brew install python3 ffmpeg
    pip3 install livestreamer pycrypto lxml
