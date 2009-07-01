-- @tag: sonderflag
-- @description: Sonderflags vom conf.php

CREATE TABLE sonderflag (
    svalue int,
    skey text,
    sorder int
);

INSERT INTO sonderflag (svalue,skey,sorder) VALUES (1,'News',1);
INSERT INTO sonderflag (svalue,skey,sorder) VALUES (2,'WV',2);
INSERT INTO sonderflag (svalue,skey,sorder) VALUES (4,'Test',3);
