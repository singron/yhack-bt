CREATE TABLE Jobs(
      torrentId int,--references Torrent, 
	  added timestamp,
      bid int,
	  downloaded int,
	  size int,
	  eta timestamp,
	  completed timestamp,
	  userId int references Users(userId), --references Users,
	  primary KEY (torrentId)
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
	credit int,
	primary KEY(userId)
);
