# Pianobar Web Interface #

I created this so my mom could control pianobar running on a raspberry pi
attached to my home stereo system without teaching her to use ssh. This is my
first php program, and I'm sure there are some mistakes even though it is
small. 

## Installing ##
I have this running on a raspberry pi running xbian, which is similar to
raspbian. I modified the apache configuration slightly, but I do not have a
list of necessary configuration options. However, I will list the steps that
should work anyway. First, copy all the files in this directory to your web
directory (eg. /var/www). Make sure index.php is run handled by php. Make sure
pianobar is installed. Change the pianobar configuration to your account name
and either use a plaintext password in the configuration (very unadvisable for a
web server), or use the password_command option, which is the better choice.
After that, create the pianobar-ctl fifo and make sure the web server can write
to it. Then make sure the web server can read and write to the pianobar-out
file. 

Once the site is set up, just point a browser to the address of the server
(actually, you just need to send a GET request) and pandora should start
playing automatically.
