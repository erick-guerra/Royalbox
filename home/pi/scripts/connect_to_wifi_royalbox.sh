#/bin/bash

if ! (set -o noclobber ; echo > /tmp/connecttowifi.lock) ; then
  #echo 'Running Already - Connection Failed'
  exit
fi

sudo chmod 777 /etc/dhcpcd.conf
sudo cp /home/pi/scripts/dhcpcd.conf.wifi /etc/dhcpcd.conf
