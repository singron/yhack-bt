import unittest
import time
from rtorrent import RTorrent

class TestRTorrentAPI(unittest.TestCase):

    self.test_file = "tester.torrent"

    def setUp(self):
        self.rt = RTorrent()

    def test_add_torrent(self):
        f = open(self.test_file)
        out = f.read()
        self.rt.add_torrent_file(self, out)

        time.sleep(100)

        res = self.rt.get_active_infohashes(self)
        assert(len(res) == 1)
