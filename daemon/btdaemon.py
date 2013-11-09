#! /usr/bin/python

import bencode
import rtorrent
import urlparse
import datetime
from models import Jobs,Torrents
from sqlalchemy import create_engine,and_,func,select,update,bindparam,not_,or_

def error(msg):
	print msg

def get_info_hash_torrent_file(filedata):
	info = bencode.bdecode(filedata)['info']
	infohash = hashlib.sha1(bencode.bencode(info)).hexdigest()
	return infohash.upper()

def get_info_hash_magnet(magnet):
	if not magnet.startswith('magnet:?'):
		return None
	xt = urlparse.parse_qs(magnet[len('magnet:?'):])['xt'][0]
	infohash = None
	if xt.startswith('urn:btih:'):
		infohash = xt[len('urn:btih:'):]
	elif xt.startswith('urn:sha1:'):
		infohash = xt[len('urn:sha1:'):]
	else:
		error("unknown magnet link hash {}".format(magnet))
		return None
	return infohash.upper()

def move_completed_download(rt, infohash):
	pass

def calculate_hashes(engine):
	torrent_qry = select([Torrents])\
			      .where(or_(Torrents.c.infohash == None,
					         Torrents.c.infohash == ""))
	update_qry = Torrents.update()\
			     .values(infohash=bindparam('newhash'))\
				 .where(Torrents.c.torrentid == bindparam('tid'))
	torrents = engine.execute(torrent_qry)
	for t in torrents:
		print "getting hash"
		infohash = None
		if t.torrent != None and t.torrent != "":
			infohash = get_info_hash_torrent_file(t.torrent)
		elif t.magnet_link != None and t.magnet_link != "":
			infohash = get_info_hash_magnet(t.magnet_link)
		engine.execute(update_qry,newhash=infohash,tid=t.torrentid)

def main():
	queue_size = 50
	engine = create_engine('postgresql://yhack:yhack@localhost:5432/yhack')
	rt = rtorrent.RTorrent()
	complete_qry = Jobs.update()\
	               .values(completed=func.now())\
			       .where(and_(Jobs.c.completed == None,\
				               Jobs.c.torrentid == Torrents.c.torrentid,\
				               Torrents.c.infohash == bindparam('infohash')))

	update_qry = Jobs.update()\
			     .values(downloaded=bindparam('completed_bytes'),\
				         speed=bindparam('down_rate'),\
				         eta=bindparam('eta'),\
						 size=bindparam('size'))\
			     .where(and_(Torrents.c.infohash == bindparam('infohash'),
				             Jobs.c.torrentid == Torrents.c.torrentid))

	active_queue = rt.get_active_infohashes()
	running = True
	while running:
		calculate_hashes(engine)
		active_queue = rt.get_active_infohashes()
		if active_queue:
			for infohash in active_queue:
				completed_bytes = rt.get_completed_bytes(infohash)
				size_bytes = rt.get_size_bytes(infohash)
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
					eta = datetime.timedelta(seconds=((size - completed_bytes) /
						down_rate))
					engine.execute(update_qry,\
							infohash=infohash,\
							down_rate=down_rate,\
							eta=eta,\
							completed_bytes=completed_bytes,\
							size=size)

		new_torrents = []
		if not active_queue:
			# Add more torrents
			print "Adding to active queue"
			more_needed = queue_size
			queue_qry = select([Torrents])\
						.where(and_(Jobs.c.completed == None,\
									Jobs.c.torrentid == Torrents.c.torrentid))\
						.order_by(Jobs.c.bid.desc(), Jobs.c.added.asc())\
						.limit(more_needed)
			new_torrents = engine.execute(queue_qry)
					
		elif len(active_queue) < queue_size:
			more_needed = queue_size - len(active_queue)
			queue_qry = select([Torrents])\
						.where(and_(Jobs.c.completed == None,\
									Jobs.c.torrentid == Torrents.c.torrentid,\
									Torrents.c.infohash.notin_(active_queue)))\
						.order_by(Jobs.c.bid.desc(), Jobs.c.added.asc())\
						.limit(more_needed)
			new_torrents = engine.execute(queue_qry)

		for t in new_torrents:
			print "Add torrent " + t.infohash
			if t.torrent:
				rt.add_torrent_file(t.torrent)
			elif t.magnet_link:
				rt.add_torrent_magnet(t.magnet_link)

if __name__ == '__main__':
	main()
