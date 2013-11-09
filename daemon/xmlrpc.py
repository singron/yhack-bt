import xmlrpclib

class RTorrent:
	def __init__(self):
		self.proxy = xmlrpclib.ServerProxy("http://localhost:500/")

	def add_torrent_file(self, filedata):
		self.proxy.load_raw_start(filedata)

	def add_torrent_magnet(self, magnet):
		self.proxy.load_start(magnet)
		return infohash

	def get_completed_bytes(self,infohash):
		return self.proxy.d.get_completed_bytes(infohash)

	def get_down_rate(self,infohash):
		return self.proxy.d.get_down_rate(infohash)

	def get_size_bytes(self,infohash):
		return self.proxy.d.size_bytes(infohash)

	def close(self,infohash):
		self.proxy.d.close(infohash)

	def get_active_infohashes(self):
		self.proxy.download_list()
