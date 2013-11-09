import xmlrpclib
from time import time

class RTorrent:
<<<<<<< HEAD
    def __init__(self):
        self.proxy = xmlrpclib.ServerProxy("http://localhost:500")
        self.delay = 0

    def reset_timer(self):
        self.delay = time() + 5

    def add_torrent_file(self, filedata):
        self.proxy.load_raw_start(xmlrpc.Binary(filedata))
        self.reset_timer()

    def add_torrent_magnet(self, magnet):
        self.proxy.load_start(magnet)
        self.reset_timer()

    def get_completed_bytes(self,infohash):
        while time() < self.delay:
            pass
        return self.proxy.d.get_completed_bytes(infohash)

    def get_down_rate(self,infohash):
        while time() < self.delay:
            pass
        return self.proxy.d.get_down_rate(infohash)

    def get_size_bytes(self,infohash):
        while time() < self.delay:
            pass
        return self.proxy.d.size_bytes(infohash)

    def close(self,infohash):
        while time() < self.delay:
            pass
        self.proxy.d.close(infohash)

    def erase(self,infohash):
        while time() < self.delay:
            pass
        self.proxy.d.erase(infohash)

    def get_active_infohashes(self):
        while time() < self.delay:
            pass
        return self.proxy.download_list()
