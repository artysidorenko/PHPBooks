-- Doctrine Migration File Generated on 2019-05-03 16:08:01

-- Version 20190503143534
ALTER TABLE books CHANGE title title VARCHAR(255) NOT NULL, CHANGE price price INT NOT NULL, CHANGE description description VARCHAR(255) NOT NULL;
INSERT INTO migration_versions (version, executed_at) VALUES ('20190503143534', CURRENT_TIMESTAMP);
