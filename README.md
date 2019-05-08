<a href="https://itunes.apple.com/app/royalbox-control-center-home/id1450861330"><img src="https://github.com/omody/Royalbox/raw/master/var/www/html/img/royalbox-logo-color-horiz-light.png" /></a>
<p align="center"><a href="https://itunes.apple.com/app/royalbox-control-center-home/id1450861330"><img src="https://github.com/omody/GitHawk/blob/master/images/app-store-badge.png" width="250" /></a></p>
Royalbox Remote App allows for unlimited streaming if you have a Royalbox.  If you do not then there is an in-app purchase.  The app tries to connect to your Royalbox and if it is able to unlocks the in-app purchase automatically.

# ROYALBOX
Build and sell your own Raspberry Pi based streaming device.  Sell your own SD card loaded to convert any Raspberry Pi into a Chromecast/Roku like streaming device.  Or build your own Raspberry Pi CPU with pre-loaded software to use as a streaming stick.  With Headless setup (No keyboard or mouse required to setup your streaming device) you can simply plug it in your TV and a use the on-screen configuration guide to setup.  You can build your own Royalbox and sell it :) or give it your friends as a gift.  This is the ideal gift for anyone who love British or Australian TV.  BBC, iTV, SBS On Demand, and ABC iView are all supported. 

# PREREQUISITES

 - Raspberry Pi A+, B+, ZeroW with at least 16Gb SD card running Raspbian. 
 - Wifi connection.
 - Television plugged into Raspberry Pi.
 - Computer, tablet or smartphone for controlling server.
 - Use the companion iOS app to Stream to Royalbox from iOS Device (optional)
 
# CREDITS

The project is built on 2 other projects from Github and some additional customization for the purpose of creating the Royalbox.

Streaming Engine - https://github.com/blissland/blissflixx
**NOTE:** The Royalbox is meant to bypass geo-restrictions.  Which is why this repo has 4 channels currently.  You can still use iPhone app to cast any web video to your Royalbox connected TV.  If you want additional channels go the blissflix repo and you can add those channels as well.  Blissflixx also has user plugin channels that may or may not work still but you can use them as well.

Wifi Configuration - https://github.com/billz/raspap-webgui

-------------------------------------------------------------------------------------------------------------------------
# SCREENSHOTS

**TV SETUP and DASHBOARD**
<p align="center">
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/wifi_setup_step1.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/wifi_setup_step2.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_dashboard.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_dashboard_video_loading.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/foxnews.png" width="350" />
</p>

**TABLET/COMPUTER/MOBILE SETUP SCREEN**
<p alight="center">
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/wifi_setup_headless.png" width="500" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_headless_iphone.png" height="350" />
</p>

**CASTING TO TV MOBILE**
<p align="center">
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_channels_iphone.png" height="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_bbc_iphone.png" height="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_cast_iphone.png" height="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_cast2_iphone.png" height="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_popup_iphone.png" height="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_remote_iphone.png" height="350" />
</p>

**CASTING TO TV CHROME**
<p align="center">
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_abciview_mac.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_bbc_mac.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_channels_mac.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_itv_mac.png" width="350" />
<img src="https://github.com/omody/Royalbox/raw/master/home/pi/Pictures/royalbox_sbs_mac.png" width="350" />
</p>

-------------------------------------------------------------------------------------------------------------------------
# PRE-CONFIGURED IMG FILE

**In Progress***
I am working on this

-------------------------------------------------------------------------------------------------------------------------
# HOW TO BUILD - DO IT YOURSELF - In progress

**NOTE**:  This process modifies the filesystem and therefore should be used at own risk.

This repository has the full directory structure and files that were replaced/modified to build this device.  You will need to build this project once and then you can create and image of your Raspberry Pi and/or buy a SD card duplicator to sell your own SD cards or build your CPU's.


Login into your Raspberry Pi

1) Clone the project
```
pi@raspberrypi:~$ cd Downloads
pi@raspberrypi:~$ git clone https://github.com/omody/Royalbox.git
```

2) Move the home directory and replace your current home pi directory
```
pi@raspberrypi:~$ sudo mv ~/Downloads/home/* ~/home/.
```

3) Now configure blissflixx
```
pi@raspberrypi:~$ cd ~/blissflixx
pi@raspberrypi:~$ sudo chmod +x configure.sh
pi@raspberrypi:~$ sudo ./configure.sh
```

This will take about 20 mins to rebuild on a Pi B+.

4) Now lets do the headless setup, accept all the default suggestion
```
pi@raspberrypi:~$ wget -q https://git.io/voEUQ -O /tmp/raspap && bash /tmp/raspap
```

5) Now lets replace the headless configuration software that comes with our own custom one
```
pi@raspberrypi:~$ sudo mv /var/www/html /var/www/html_old
pi@raspberrypi:~$ sudo cp -r ~/Downloads/var/www /var/.
pi@raspberrypi:~$ chmod -R www-data:www-data /var/www
```

6) Now lets configure the interfaces, webservers, cronjob and sudoer's to finish this up
```
pi@raspberrypi:~$ sudo cp -r ~/Downloads/etc /
pi@raspberrypi:~$ sudo cp -r ~/Downloads/boot /
pi@raspberrypi:~$ sudo cp -r ~/Downloads/usr /
pi@raspberrypi:~$ sudo visudo
```
Add the following lines to the end
```
www-data ALL=(ALL) NOPASSWD:/home/pi/scripts/connect_to_wifi_royalbox.sh
www-data ALL=(ALL) NOPASSWD:/home/pi/scripts/wifi_rebooter.sh
www-data ALL=(ALL) NOPASSWD:/home/pi/scripts/dhcp_rebooter.sh
www-data ALL=(ALL) NOPASSWD:/bin/cat /etc/hostname
www-data ALL=(ALL) NOPASSWD: /bin/bash, /sbin/iwlist, /usr/bin/timedatectl, /usr/bin/tvservice, /sbin/fping, /sbin/ifconfig
```
You can make modification using this reference
https://scribles.net/customizing-boot-up-screen-on-raspberry-pi/

I did the following:
Now get rid of the rainbow logo
```
pi@raspberrypi:~$ sudo vi /boot/cmdline.txt
```
Add the end of the file put a space and add:
```
logo.nologo
```
Now lets add the cronjob for startup and make sure the system restarts processes if something crashes
```
pi@raspberrypi:~$ crontab < ~/Downloads/home/pi/setup_cronjobs.txt
```

Now reboot and if everything goes smoothly you can proceed with a headless setup for your Raspberry Pi (Royalbox).
