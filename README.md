# ROYALBOX
Turn your Raspberry Pi into a Roku + Chromecast streaming device for iOS Users + Dashboard with weather, time, news feed.  You can embedded any dashboard you like.

Royalbox allows you to stream m3u8, mp4 videos from the web directly to your TV using the iOS app.  It can cast videos embedded in web pages, live stream, etc. to your TV with iOS App and the Raspberry Pi connected TV.


# PREREQUISITES

 - Raspberry Pi A+, B+, ZeroW with at least 16Gb SD card running Raspbian. 
 - Wifi connection.
 - Television plugged into Raspberry Pi.
 - Computer, tablet or smartphone for controlling server.
 
The project is built on 2 other projects from Github and some additional customization for the purpose of creating the Royalbox.

Streaming Engine - https://github.com/blissland/blissflixx
Wifi Configuration - https://github.com/billz/raspap-webgui

Blissflixx hasn't been updated for a while.  Please use the "configure.sh" file that is in the project 
git clone https://github.com/blissland/blissflixx.git
cd blissflixx
COPY THE configure.sh from this repository and replace the out dated one.
sudo ./configure.sh

Raspap-Webgui - Installation can be used as it.  The modification will be later described.
wget -q https://git.io/voEUQ -O /tmp/raspap && bash /tmp/raspap

After installing Rasap-Webgui you need to replace additional file and make additional modification

1)

Use the iOS app to Stream to Royalbox
https://itunes.apple.com/us/app/royalbox-control-center-home/id1450861330?mt=8

