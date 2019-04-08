
CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE User (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name_user VARCHAR(128) NOT NULL,
	email VARCHAR(128) NOT NULL ,
	password CHAR(64) NOT NULL,
	reg_date DATETIME NOT NULL DEFAULT NOW()
);

CREATE TABLE Project (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name_project VARCHAR(128),
	user_id INT NOT NULL
);

CREATE TABLE Task (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name_task CHAR(128) NOT NULL,
	creation_at DATETIME DEFAULT NOW(),
	deadline DATE,
	done_at DATETIME,
	file_task VARCHAR(128),
	project_id INT,
	user_id INT NOT NULL,
	status BOOLEAN DEFAULT FALSE,
	FULLTEXT (name_task)
);

CREATE UNIQUE INDEX email ON User(email);
CREATE INDEX getProject ON Project(name_project,user_id);
CREATE INDEX doneTask ON Task(user_id,project_id);