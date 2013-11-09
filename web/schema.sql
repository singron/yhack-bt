CREATE TABLE Jobs(
      jobId serial not null,
      torrentId int references Torrents(torrentId),--references Torrent, 
	  added timestamp WITHOUT TIME ZONE,
      bid int,
	  downloaded int,
	  size int,
      speed int,
	  eta timestamp WITHOUT TIME ZONE,
	  completed timestamp WITHOUT TIME ZONE,
	  userId int references Users(userId), --references Users,
      downloadId int references Downloads(downloadId),
	  primary KEY (jobId)
	);

CREATE TABLE Downloads(
      downloadId serial not null,
      start_time timestamp WITHOUT TIME ZONE,
      ip varchar not null,
    );

CREATE TABLE Torrents(
	torrentId serial not null,
    name varchar,
	torrent bytea,
	primary KEY (torrentId)
);

CREATE TABLE Users(
	userId serial not null,
	email varchar,
	hash varchar,
	salt varchar,
	credit int,
	primary KEY(userId)
);
