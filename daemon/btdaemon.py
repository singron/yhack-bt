#! /usr/bin/python

import bencode
import rtorrent
from models import Jobs
from sqlalchemy import create_engine,and_,func,select,update,bindparam

def get_info_hash_torrent_file(filedata):
	info = bencode.bdecode(filedata)['info']
	infohash = hashlib.sha1(bencode.bencode(info)).hexdigest()

def get_info_hash_magnet(magnet):
	parts = urlparse(magnet)
	xt = parts.query['xt']
	infohash = None
	if xt.startswith('urn:btih:'):
		infohash = xt[len('urn:btih'):]
	elif xt.startswith('urn:sha1'):
		infohash = xt[len('urn:sha1'):]
	else:
		error("unknown magnet link hash {}".format(magnet))
	return infohash

def main():
	queue_size = 50
	engine = create_engine('postgresql://yhack:yhack@localhost/yhack')
	rt = rtorrent.RTorrent()
	queue_qry = select([Jobs.c.infohash])\
			    .where(_and(Jobs.completed == None,\
				            not_(Jobs.infohash.in_(bindparam('hashes')))))\
	            .order_by(Jobs.bid.desc(), Jobs.added.asc())\
				.limit(bind_param('n'))

	complete_qry = Jobs.update()\
	               .values(completed=func.now())\
			       .where(and_(Jobs.completed == None,\
				               Jobs.infohash == bindparam('infohash')))

	update_qry = Jobs.update()\
			     .values(downloaded=bindparam('completed_bytes'),\
				         speed=bindparam('down_rate'),\
				         eta=bindparam('eta'),\
						 size=bindparam('size'))\
			     .where(infohash=bindparam('infohash'))

	active_queue = rt.get_active_infohashes()
	running = True
	while running:
		for infohash in active_queue:
			completed_bytes = rt.get_completed_bytes()
			size_bytes = rt.get_size_bytes()
			if completed_bytes == size_bytes:
				# Torrent is done
				move_completed_download(rt, infohash)
				rt.close(infohash)
				engine.execute(complete_qry, infohash=infohash)
			else:
				# update current stats
				down_rate = rt.get_down_rate(infohash)
				completed_bytes = rt.get_completed_bytes(infohash)
				size = rt.get_size_bytes(infohash)
				eta = (size - completed_bytes) / down_rate
				engine.execute(update_qry,\
						infohash=infohash,\
						down_rate=down_rate,\
						eta=eta,\
						completed_bytes=completed_bytes,\
						size=size)

		active_queue = rt.get_active_infohashes()
		if len(active_queue) < queue_size:
			# Add more torrents
			more_needed = queue_size - len(active_queue)
			engine.execute(queue_qry, n=more_needed,\
					hashes=active_queue).fetch_all()



if __name__ == '__main__':
	main()
