#!/bin/bash

if [ "`ps -ef|grep omxplayer|grep -v grep|grep -v vi`" ]
then
exit
fi

#sudo pip install --upgrade youtube-dl

#sudo reboot
sleep 3 && sudo reboot &

