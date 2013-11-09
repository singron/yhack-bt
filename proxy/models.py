from sqlalchemy import Table, Column, Integer, String, DateTime, ForeignKey, LargeBinary, MetaData

metadata = MetaData()

Jobs = Table('Jobs', metadata,
    Column('jobId', Integer, primary_key=True),
    Column('torrentId', Integer, ForeignKey('Torrents.torrentId')),
    Column('added', DateTime(False)),
    Column('bid', Integer),
    Column('downloaded', Integer),
    Column('size', Integer),
    Column('speed', Integer),
    Column('eta', DateTime(False)),
    Column('completed', DateTime(False)),
    Column('userId', Integer, ForeignKey('Users.userId')),
    Column('downloadId', Integer, ForeignKey('Downloads.downloadId')),
)

Downloads = Table('Downloads', metadata,
    Column('downloadId', Integer, primary_key=True),
    Column('start_time', DateTime(False)),
    Column('ip', String, nullable=False),
)

Torrents = Table('Torrents', metadata,
    Column('torrentId', Integer, primary_key=True),
    Column('name', String),
    Column('torrent', LargeBinary),
    Column('infoHash', String),
)

Users = Table('Users', metadata,
    Column('userId', Integer, primary_key=True),
    Column('email', String),
    Column('hash', String),
    Column('salt', String),
    Column('credit', Integer),
)
