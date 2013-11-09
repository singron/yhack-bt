from sqlalchemy import create_engine
from sqlalchemy.sql import select
from twisted.internet import reactor
from twisted.web import server
from twisted.web.proxy import ReverseProxyResource
from rproxy import MyReverseProxy
from urllib import quote


engine = create_engine('sqlite:///proxy.db', echo=True)

site = server.Site(MyReverseProxy('yhack.phaaze.com', 80, '', engine))
reactor.listenTCP(8080, site)
reactor.run()
