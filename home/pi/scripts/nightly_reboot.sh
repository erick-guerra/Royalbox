#!/bin/bash

if [ "`ps -ef|grep omxplayer|grep -v grep|grep -v vi`" ]
then
exit
fi

cd /home/pi/blissflixx
/usr/bin/git fetch --all
/usr/bin/git reset --hard origin/master
/usr/bin/git pull
cd /home/pi/blissflixx/lib/youtube-dl
/usr/bin/make lazy-extractors
/usr/bin/make youtube-dl
chmod +x /home/pi/blissflixx/*.sh
chmod +x /home/pi/blissflixx/bin/*
chmod +x /home/pi/blissflixx/blissflixx.py
chmod +x /home/pi/blissflixx/*

#sudo pip install --upgrade youtube-dl

#sudo reboot
sleep 3 && sudo reboot &

