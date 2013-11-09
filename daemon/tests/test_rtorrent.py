import unittest
import time
import xmlrpclib
from rtorrent import RTorrent

class TestRTorrentAPI(unittest.TestCase):

    def setUp(self):
        self.rt = RTorrent()
        self.test_file = "tests/tester.torrent"
        self.test_magnet = "magnet:?xt=urn:btih:f2ed240912dc324d6a30de6811a8747f80b9722d&dn=The+Wolverine+2013+DVDRip+x264+AC3-EVO&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A6969&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Fopen.demonii.com%3A1337"

        files = self.rt.get_active_infohashes()
        for f in files:
            self.rt.erase(f)

    def test_add_torrent(self):
        self.rt.add_torrent_magnet(self.test_magnet)

        res = self.rt.get_active_infohashes()
        assert(len(res) == 1)

        self.rt.erase(res[0])
        
        res = self.rt.get_active_infohashes()
        assert(len(res) == 0)

    def test_torrent_size(self):
        self.rt.add_torrent_magnet(self.test_magnet)

        res = self.rt.get_active_infohashes()
        assert(len(res) == 1)

        time.sleep(10)

        assert (1500000000 < self.rt.get_size_bytes(res[0]) < 1600000000)

    def test_torrent_file_add(self):
        f = open(self.test_file)
        out = f.read()
        self.rt.add_torrent_file(out)

        res = self.rt.get_active_infohashes()
        assert(len(res) == 1)
