from boto.s3.connection import S3Connection
from boto.s3.key import Key
import os

torrent_dir = '/home/rtorrent/torrents'

def get_bucket():
    conn = S3Connection('AKIAJ5K7NPVJRNEGRGNQ',
            '0BCbG9DkxZCJQdtlOcmwaFKk5yvSlw8g/bwmPVyw')
    return conn.create_bucket('yhack_files')

def store_file(infoHash):
    os.chdir(torrent_dir)
    os.system("zip -r %s -0 %s" % (infoHash, infoHash))

    k = Key(get_bucket())
    k.key = infoHash
    k.set_contents_from_filename(infoHash + '.zip')

    os.chdir('/home/rtorrent')
    # Generate the URL
    return k.generate_url(86400)

def delete_file(infoHash):
    k = get_bucket().get_key(infoHash)
    k.delete()

def check_file(infoHash):
    return get_bucket().get_key(infoHash) is None

if __name__ == '__main__':
    delete_file("derp")
