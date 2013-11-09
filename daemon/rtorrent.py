import xmlrpclib
from time import time

class RTorrent:
    def __init__(self):
        self.proxy = xmlrpclib.ServerProxy("http://localhost:500")
        self.delays = {}

    def reset_timer(self, infohash):
        self.delays[infohash] = time() + 5

    def add_torrent_file(self, filedata, infohash):
        self.proxy.load_raw_start(xmlrpclib.Binary(filedata))
        self.reset_timer(infohash)

    def add_torrent_magnet(self, magnet, infohash):
        self.proxy.load_start(magnet)
        self.reset_timer(infohash)

    def get_completed_bytes(self,infohash):
        while time() < self.delays[infohash]:
            pass
        return self.proxy.d.get_completed_bytes(infohash)

    def get_down_rate(self,infohash):
        while time() < self.delays[infohash]:
            pass
        return self.proxy.d.get_down_rate(infohash)

    def get_size_bytes(self,infohash):
        while time() < self.delays[infohash]:
            pass
        return self.proxy.d.size_bytes(infohash)

    def close(self,infohash):
        while time() < self.delays[infohash]:
            pass
        self.proxy.d.close(infohash)

    def erase(self,infohash):
        while time() < self.delays[infohash]:
            pass
        self.proxy.d.erase(infohash)

    def get_active_infohashes(self):
        for key, value in self.delays.iteritems():
            while time() < value:
                pass
        return self.proxy.download_list()
