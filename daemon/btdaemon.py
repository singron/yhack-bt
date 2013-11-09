#! /usr/bin/python

import bencode
import rtorrent
import urlparse
import datetime
import hashlib
from binascii import unhexlify
from storage import *
from models import Jobs,Torrents,Downloads
from sqlalchemy import create_engine,and_,func,select,update,bindparam,not_,or_,exists

def error(msg):
    print msg

def get_info_hash_torrent_file(filedata):
    info = bencode.bdecode(unhexlify(filedata))['info']
    infohash = hashlib.sha1(bencode.bencode(info)).hexdigest()
    return infohash.upper()

def get_info_hash_magnet(magnet):
    if not magnet.startswith('magnet:?'):
        return None
    params = urlparse.parse_qs(magnet[len('magnet:?'):])
    xt = params['xt'][0]
    infohash = None
    if xt.startswith('urn:btih:'):
        infohash = xt[len('urn:btih:'):]
    elif xt.startswith('urn:sha1:'):
        infohash = xt[len('urn:sha1:'):]
    else:
        error("unknown magnet link hash {}".format(magnet))
        return None
    return infohash.upper()

def get_name_magnet(magnet):
    if not magnet.startswith('magnet:?'):
        return None
    params = urlparse.parse_qs(magnet[len('magnet:?'):])
    if 'dn' in params.keys():
        name = params['dn'][0]
    else:
        name = None
    return name

def calculate_hashes(engine):
    torrent_qry = select([Torrents])\
                  .where(or_(Torrents.c.infohash == None,
                             Torrents.c.infohash == ""))
    update_qry = Torrents.update()\
                 .values(infohash=bindparam('newhash'))\
                 .where(Torrents.c.torrentid == bindparam('tid'))

    update_name_qry = Torrents.update()\
                 .values(infohash=bindparam('newhash'),name=bindparam('name'))\
                 .where(Torrents.c.torrentid == bindparam('tid'))
    torrents = engine.execute(torrent_qry)
    for t in torrents:
        infohash = None
        name = None
        if t.torrent != None and t.torrent != "":
            infohash = get_info_hash_torrent_file(t.torrent)
        elif t.magnet_link != None and t.magnet_link != "":
            infohash = get_info_hash_magnet(t.magnet_link)
            name = get_name_magnet(t.magnet_link)
        if name:
            engine.execute(update_name_qry,newhash=infohash,tid=t.torrentid,name=name)
        else:
            engine.execute(update_qry,newhash=infohash,tid=t.torrentid)

def main():
    queue_size = 4 
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
    
    complete1_qry = Downloads.update()\
                   .values(link=bindparam('s3link'))\
                   .where(and_(Downloads.c.downloadid == Jobs.c.downloadid,\
                               Jobs.c.torrentid == Torrents.c.torrentid,\
                               Torrents.c.infohash == bindparam('infohash')))

    complete2_qry = Jobs.update()\
                   .values(completed=func.now())\
                   .where(and_(Jobs.c.torrentid == Torrents.c.torrentid,\
                               Torrents.c.infohash == bindparam('infohash')))
    
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
                    print "Completed " + infohash
                    s3link = store_file(infohash)

                    ins = Downloads.insert().values(link=s3link)
                    engine.execute(ins)

                    s = select([Downloads]).where(Downloads.c.link == s3link)
                    res = engine.execute(ins).fetchone()

                    s = Jobs.update().values(Jobs.c.downloadid = res.downloadid)\
                            .where(and_(Jobs.c.torrendid == Torrents.c.torrentid,\
                                        Torrents.c.infohash == infohash))

                    rt.close(infohash)
                    rt.erase(infohash)
                    engine.execute(complete1_qry, infohash=infohash,
                            s3link=s3link)
                    engine.execute(complete2_qry, infohash=infohash)
                else:
                    # update current stats
                    down_rate = rt.get_down_rate(infohash)
                    completed_bytes = rt.get_completed_bytes(infohash)
                    size = rt.get_size_bytes(infohash)
                    if down_rate != 0:
                        eta = datetime.timedelta(seconds=((size - completed_bytes) /
                            down_rate))
                    else:
                        eta = None
                    engine.execute(update_qry,\
                            infohash=infohash,\
                            down_rate=down_rate,\
                            eta=eta,\
                            completed_bytes=completed_bytes,\
                            size=size)

        active_queue = rt.get_active_infohashes()
        new_torrents = []
        if not active_queue:
            # Add more torrents
            more_needed = queue_size
            queue_qry = select([Torrents])\
                        .where(and_(Jobs.c.completed == None,\
                                    Torrents.c.infohash != None,\
                                    Jobs.c.torrentid == Torrents.c.torrentid))\
                        .order_by(Jobs.c.bid.desc(), Jobs.c.added.asc())\
                        .limit(more_needed)
            new_torrents = engine.execute(queue_qry)
                    
        elif len(active_queue) < queue_size:
            more_needed = queue_size - len(active_queue)
            queue_qry = select([Torrents])\
                        .where(and_(Jobs.c.completed == None,\
                                    Jobs.c.torrentid == Torrents.c.torrentid,\
                                    Torrents.c.infohash != None,\
                                    Torrents.c.infohash.notin_(active_queue)))\
                        .order_by(Jobs.c.bid.desc(), Jobs.c.added.asc())\
                        .limit(more_needed)
            new_torrents = engine.execute(queue_qry)

        for t in new_torrents:
            print "Add torrent " + t.infohash
            if t.torrent:
                print "torrent"
                rt.add_torrent_file(unhexlify(t.torrent), t.infohash)
            elif t.magnet_link:
                print "magnet"
                rt.add_torrent_magnet(t.magnet_link, t.infohash)

        active_queue = rt.get_active_infohashes()
        if active_queue:
            # remove stale torrents
            check_qry = select([Torrents])\
                        .where(and_(Torrents.c.infohash.in_(active_queue),
                            not_(exists(select([Jobs]).where(and_(
                                Torrents.c.torrentid == Jobs.c.torrentid,
                                Jobs.c.completed == None
                               ))))))

            stale_torrents = engine.execute(check_qry)
            stale_hashes = [t.infohash for t in stale_torrents]
            active_torrents_tmp = []
            for t in active_queue:
                if t in stale_hashes:
                    print "Remove stale torrent " + t
                    rt.close(t)
                    rt.erase(t)
                else:
                    active_torrents_tmp.append(t)
            active_queue = active_torrents_tmp
            
if __name__ == '__main__':
    main()
