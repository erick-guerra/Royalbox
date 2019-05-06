#!/bin/bash

#sleep 10

if [ "`ps -ef|grep blissflixx.py|grep -v grep|grep -v vi`" ]
then
exit
fi

if ! (set -o noclobber ; echo > /tmp/startbliss.lock) ; then
  exit
fi

# The IP for the server you wish to ping (8.8.8.8 is a public Google DNS server)
SERVER=1.1.1.1

# Only send two pings, sending output to /dev/null
ping -c2 ${SERVER} > /dev/null

# If the return code from ping ($?) is 0 (meaning there was no error)
#if [ $? == 0 ]
#then
  #sudo service lighttpd stop
  #sleep 2
  /home/pi/blissflixx/blissflixx.py --port 80 --daemon
  #sleep 2
  #chromium-browser --noerrdialogs --incognito --kiosk http://localhost/app/loading.html
#fi

rm -rf /tmp/startbliss.lock
