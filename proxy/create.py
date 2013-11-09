from models import metadata, Jobs, Torrents, Users, Downloads
from sqlalchemy import create_engine
from sqlalchemy.schema import CreateTable

engine = create_engine('sqlite:///proxy.db', echo=True)

def dump(sql, *multiparams, **params):
        print sql.compile(dialect=engine.dialect)
postgres = create_engine('postgresql://', strategy='mock', executor=dump)
print CreateTable(Jobs).compile(postgres)
print CreateTable(Downloads).compile(postgres)
print CreateTable(Torrents).compile(postgres)
print CreateTable(Users).compile(postgres)

metadata.create_all(engine)
