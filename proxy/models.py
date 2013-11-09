from sqlalchemy import Table, Column, Integer, String, DateTime, ForeignKey, LargeBinary, MetaData, Boolean, Interval

metadata = MetaData()

Jobs = Table('jobs', metadata,
    Column('jobid', Integer, primary_key=True),
    Column('torrentid', Integer, ForeignKey('torrents.torrentid')),
    Column('added', DateTime(False)),
    Column('bid', Integer),
    Column('downloaded', Integer),
    Column('size', Integer),
    Column('speed', Integer),
    Column('eta', Interval),
    Column('completed', DateTime(False)),
    Column('userid', Integer, ForeignKey('users.userid')),
    Column('downloadid', Integer, ForeignKey('downloads.downloadid')),
)

Downloads = Table('downloads', metadata,
    Column('downloadid', Integer, primary_key=True),
    Column('start_time', DateTime(False)),
    Column('ip', String),
    Column('link', String, nullable=False),
)

Torrents = Table('torrents', metadata,
    Column('torrentid', Integer, primary_key=True),
    Column('name', String),
    Column('torrent', LargeBinary),
    Column('magnet_link', String),
    Column('infohash', String, unique=True)
)

Users = Table('users', metadata,
    Column('userid', Integer, primary_key=True),
    Column('email', String),
    Column('hash', String),
    Column('salt', String),
    Column('credit', Integer),
)
