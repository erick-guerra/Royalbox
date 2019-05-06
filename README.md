<a href="https://itunes.apple.com/app/royalbox-control-center-home/id1450861330"><img src="https://github.com/omody/Royalbox/raw/master/var/www/html/img/royalbox-logo-color-horiz-light.png" /></a>
<p align="center"><a href="https://itunes.apple.com/app/royalbox-control-center-home/id1450861330"><img src="https://github.com/omody/GitHawk/blob/master/images/app-store-badge.png" width="250" /></a></p>

# ROYALBOX
Build and sell your own Raspberry Pi based streaming device.  Sell your own SD card loaded to convert any Raspberry Pi into a Chromecast/Roku like streaming device.  Or build your own Raspberry Pi CPU with pre-loaded software to use as a streaming stick.  With Headless setup (No keyboard or mouuse required to setup your streaming device) you can simply plug it in your TV and a use the on-screen configuration guide to setup. 

# PREREQUISITES

 - Raspberry Pi A+, B+, ZeroW with at least 16Gb SD card running Raspbian. 
 - Wifi connection.
 - Television plugged into Raspberry Pi.
 - Computer, tablet or smartphone for controlling server.
 
# CREDITS

The project is built on 2 other projects from Github and some additional customization for the purpose of creating the Royalbox.

Streaming Engine - https://github.com/blissland/blissflixx

Wifi Configuration - https://github.com/billz/raspap-webgui

-------------------------------------------------------------------------------------------------------------------------
# HOW TO BUILD

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



Use the iOS app to Stream to Royalbox

<a href="https://itunes.apple.com/us/app/royalbox-control-center-home/id1450861330?mt=8"><img src="images/githawk-pulse.gif" /></a>
<p align="center"><a href="https://itunes.apple.com/app/royalbox-control-center-home/id1450861330"><img src="https://github.com/omody/GitHawk/blob/master/images/app-store-badge.png" width="250" /></a></p>

