#!/bin/bash

#sudo rm -rf /home/pi/scripts/count.cnt

# The IP for the server you wish to ping (8.8.8.8 is a public Google DNS server)
SERVER=1.1.1.1

# Only send two pings, sending output to /dev/null
ping -c2 ${SERVER} > /dev/null

# If the return code from ping ($?) is not 0 (meaning there was an error)
if [ $? != 0 ]
then
    echo 'check ssid'
    if sudo wpa_cli -i wlan0 status | grep "ssid"
    then
     echo 'connected to wifi'
     if sudo cat /etc/wpa_supplicant/wpa_supplicant.conf | grep "network" 
     then
       echo 'restoring dhcpcd wifi'
       sudo cp /home/pi/scripts/dhcpcd.conf.wifi /etc/dhcpcd.conf
       # Restart the wireless interface
       #sudo ip addr flush dev wlan0
       #sudo systemctl restart dhcpcd.service
       #sudo systemctl stop dhcpcd.service
       #sleep 2
       #sudo systemctl start dhcpcd.service
       #sleep 2
       #sudo wpa_cli -i wlan0 reconfigure
       #sleep 2
       #sudo ifup -a 
       sudo rfkill block 0
       sleep 2
       sudo rfkill unblock 0
       sleep 10
       sudo systemctl stop dhcpcd.service
       sleep 2
       sudo systemctl start dhcpcd.service
       sleep 2
       sudo service hostapd stop
       sleep 2
       sudo service hostapd start
       sudo service avahi-daemon stop
       sleep 2
       sudo service avahi-daemon start
       /home/pi/blissflixx/stop.sh
       #ping -c2 ${SERVER} > /dev/null
       #if [ $? == 0 ]
       #then
       #  /home/pi/blissflixx/stop.sh
       #  sleep 2
       #  /home/pi/blissflixx/blissflixx.py --port 80 --daemon
       #fi
     elif sudo ip addr show wlan0 | grep "10.3.14.1"
     then
       echo 'do nothing already running'
     else
       sudo service hostapd stop
       sleep 2
       sudo service hostapd start
     fi
    elif sudo ip addr show wlan0 | grep "10.3.141.1"
    then
      echo 'do nothing already running'
    else
      sudo service hostapd stop
      sleep 2
      sudo service hostapd start
    fi
else
  echo 'connection ok'
fi

