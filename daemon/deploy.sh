#! /bin/bash

cp rtorrent.rc /home/rtorrent/.rtorrent.rc
cp btdaemon.py /home/rtorrent/
chown rtorrent /home/rtorrent/{.rtorrent.rc,btdaemon.py}
