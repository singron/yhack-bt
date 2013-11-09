#/bin/bash 

svn co http://svn.code.sf.net/p/xmlrpc-c/code/advanced xmlrpc-c
cd xmlrpc-c
./configure
make
cd ..

wget http://libtorrent.rakshasa.no/downloads/rtorrent-0.9.3.tar.gz
wget http://libtorrent.rakshasa.no/downloads/libtorrent-0.13.3.tar.gz

tar xf rtorrent-0.9.3.tar.gz
tar xf libtorrent-0.13.3.tar.gz

cd libtorrent-0.13.3
./configure
make
cd ..

cd rtorrent-0.9.3
./configure --with-xmlrpc-c=/usr/local/bin/xmlrpc-c-config
make
cd ..


