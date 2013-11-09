#! /bin/bash

cp rtorrent.rc /home/rtorrent/.rtorrent.rc
cp btdaemon.py /home/rtorrent/
cp rtorrent.py /home/rtorrent/
cp storage.py /home/rtorrent/
cp models.py /home/rtorrent/
chown rtorrent /home/rtorrent/{storage.py,.rtorrent.rc,btdaemon.py,models.py,rtorrent.py}
cp rtorrent.conf /etc/init/
if test ! -d /home/rtorrent/env
then
	virtualenv /home/rtorrent/env
fi
source /home/rtorrent/env/bin/activate
pip install -r requirements.txt
chown -R rtorrent /home/rtorrent/env
