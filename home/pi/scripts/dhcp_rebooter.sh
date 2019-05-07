#!/bin/bash

sleep 180

# The IP for the server you wish to ping (8.8.8.8 is a public Google DNS server)
SERVER=1.1.1.1

# Only send two pings, sending output to /dev/null
ping -c2 ${SERVER} > /dev/null

# If the return code from ping ($?) is not 0 (meaning there was an error)
if [ $? != 0 ]
then
    sudo rm -rf /tmp/connecttowifi.lock
    #echo 'check wpa_supp'
    #if sudo cat /etc/wpa_supplicant/wpa_supplicant.conf | grep "network" 
    #then
      echo 'wifi is configured...checking dhcpcd'
      if sudo cat /etc/dhcpcd.conf | grep "#interface" 
      then
        echo 'restoring dhcpcd'
        sudo cp /home/pi/scripts/dhcpcd.conf.raspap /etc/dhcpcd.conf
        sudo cp /home/pi/scripts/wpa_supplicant.conf /etc/wpa_supplicant/wpa_supplicant.conf
      fi

      # Restart the wireless interface
      #sudo wpa_cli -i wlan0 reconfigure
      #sudo ip addr flush dev wlan0
      #sudo systemctl restart dhcpcd.service
      #sleep 2
      #sudo wpa_cli -i wlan0 reconfigure
      #sudo reboot
      
      if sudo ip addr show wlan0 | grep "10.3.141.1"
      then
        echo 'interface is good..exiting.'
      else
	sudo reboot
        #echo 'resetting wlan0'
        #sudo rfkill block 0
        #sleep 2
        #sudo rfkill unblock 0
        #sleep 10
        #sudo service hostapd stop
        #sleep 2
        #sudo service hostapd start
        #sudo service avahi-daemon stop
        #sleep 2
        #sudo service avahi-daemon start
      fi
    #fi
  #echo 'reload page'
  #xdotool getactivewindow
  #xdotool key ctrl+F5
fi

