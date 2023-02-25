CREATE TABLE /*_*/dcinv_block(
-- Primary key
dcinvblock_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
-- discord server id of the server being blocked
dcinvblock_guild varchar(255) NOT NULL,
-- user.user_id of the user who blocked the guild
dcinvblock_user int unsigned NOT NULL,
-- user.user_text of the user who blocked the guild
dcinvblock_user_text varchar(255),
-- Timestamp of when the guild was blocked
dcinvblock_timestamp varbinary(14) NOT NULL default NULL '',
-- block reason
dcinvblock_reason varchar(255) NOT NULL
)/*$wgDBTableOptions*/;

CREATE UNIQUE INDEX /*i*/dcinvblock_guild ON /*_*/dcinv_block (dcinvblock_guild);
CREATE INDEX /*i*/dcinvblock_user ON /*_*/dcinv_block (dcinvblock_user);
CREATE INDEX /*i*/dcinvblock_user_text ON /*_*/dcinv_block (dcinvblock_user_text);