wget http://libtorrent.rakshasa.no/downloads/rtorrent-0.9.3.tar.gz

tar xf rtorrent-0.9.3.tar.gz

cd rtorrent-0.9.3
./configure --with-xmlrpc-c=/usr/bin/xmlrpc-c-config
make
sudo make install
