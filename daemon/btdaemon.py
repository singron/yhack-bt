#! /usr/bin/python

import bencode
import rtorrent
from models import Jobs,Torrents
from sqlalchemy import create_engine,and_,func,select,update,bindparam,not_

def get_info_hash_torrent_file(filedata):
	info = bencode.bdecode(filedata)['info']
	infoHash = hashlib.sha1(bencode.bencode(info)).hexdigest()

def get_info_hash_magnet(magnet):
	parts = urlparse(magnet)
	xt = parts.query['xt']
	infoHash = None
	if xt.startswith('urn:btih:'):
		infoHash = xt[len('urn:btih'):]
	elif xt.startswith('urn:sha1'):
		infoHash = xt[len('urn:sha1'):]
	else:
		error("unknown magnet link hash {}".format(magnet))
	return infoHash

def main():
	queue_size = 50
	engine = create_engine('postgresql://yhack:yhack@localhost:5432/yhack')
	rt = rtorrent.RTorrent()
	complete_qry = Jobs.update()\
	               .values(completed=func.now())\
			       .where(and_(Jobs.c.completed == None,\
				               Jobs.c.torrentId == Torrents.c.torrentId,\
				               Torrents.c.infoHash == bindparam('infoHash')))

	update_qry = Jobs.update()\
			     .values(downloaded=bindparam('completed_bytes'),\
				         speed=bindparam('down_rate'),\
				         eta=bindparam('eta'),\
						 size=bindparam('size'))\
			     .where(and_(Torrents.c.infoHash == bindparam('infoHash'),
				             Jobs.c.torrentId == Torrents.c.torrentId))

	active_queue = rt.get_active_infohashes()
	running = True
	while running:
		active_queue = rt.get_active_infohashes()
		if active_queue:
			for infoHash in active_queue:
				completed_bytes = rt.get_completed_bytes()
				size_bytes = rt.get_size_bytes()
				if completed_bytes == size_bytes:
					# Torrent is done
					move_completed_download(rt, infoHash)
					rt.close(infoHash)
					engine.execute(complete_qry, infoHash=infoHash)
				else:
					# update current stats
					down_rate = rt.get_down_rate(infoHash)
					completed_bytes = rt.get_completed_bytes(infoHash)
					size = rt.get_size_bytes(infoHash)
					eta = (size - completed_bytes) / down_rate
					engine.execute(update_qry,\
							infoHash=infoHash,\
							down_rate=down_rate,\
							eta=eta,\
							completed_bytes=completed_bytes,\
							size=size)

		if not active_queue:
			# Add more torrents
			more_needed = queue_size
			queue_qry = select([Torrents.c.infoHash])\
						.where(and_(Jobs.c.completed == None,\
									Jobs.c.torrentId == Torrents.c.torrentId))\
						.order_by(Jobs.c.bid.desc(), Jobs.c.added.asc())\
						.limit(more_needed)
			engine.execute(queue_qry)
					
		elif len(active_queue) < queue_size:
			more_needed = queue_size - len(active_queue)
			queue_qry = select([Torrents.c.infoHash])\
						.where(and_(Jobs.c.completed == None,\
									Jobs.c.torrentId == Torrents.c.torrentId,\
									not_(Torrents.c.infoHash.in_(active_queue))))\
						.order_by(Jobs.c.bid.desc(), Jobs.c.added.asc())\
						.limit(more_needed)
			engine.execute(queue_qry)



if __name__ == '__main__':
	main()
