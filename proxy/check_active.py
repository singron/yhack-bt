from sqlalchemy.sql import select
from models import Downloads
from datetime import datetime
import re

def parse_uri(uri):
    r = re.compile("/(\d+)(?:/)?$")
    res = re.search(r, uri)
    if res is not None:
        return res.group(1)

def check_active(uri, engine):
    download_id = parse_uri(uri)
    if download_id is None:
        return False

    conn = engine.connect()
    s = select([Downloads]).where(Downloads.c.downloadId == download_id)
    res = conn.execute(s).fetchone()
    if res is None:
        return False

    delta = datetime.utcnow() - res["start_time"] 
    return delta.days < 1
