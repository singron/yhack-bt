CREATE TABLE Jobs(
      torrentId int,--references Torrent, 
	  added datetime,
      bid int,
	  downloaded int,
	  size int,
	  eta datetime,
	  completed datetime,
	  userId serial, --references Users,
	  primary KEY (torrentId, userId)
	) ;


CREATE TABLE Torrents(
	torrentId serial,
	torrent bytea,
	magnetLink varchar,
	primary KEY (torrentId)
);

CREATE TABLE Users(
	userId serial,
	email varchar,
	hash varchar,
	salt varchar,
	credit int
	primary KEY(userId)
);