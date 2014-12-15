SELECT * FROM wissencategorie WHERE ID = 114;
SELECT * FROM wissencategorie WHERE hauptgruppe = 0;
DROP TABLE knowledge_category;
CREATE TABLE knowledge_category (
	id 	  SERIAL,
	labeltext text,
	maingroup int,
	help    bool,
	oldid	  int
);
DROP TABLE tmp;
CREATE TABLE tmp (
	id 	  SERIAL,
	labeltext text,
	maingroup int,
	help    bool,
	oldid	  int
);
INSERT INTO knowledge_category (labeltext, maingroup, help, oldid) SELECT name, hauptgruppe, kdhelp, id FROM wissencategorie;
INSERT INTO tmp (labeltext, maingroup, help, oldid) SELECT name, hauptgruppe, kdhelp, id FROM wissencategorie;
SELECT * FROM knowledge_category;
SELECT id FROM knowledge_category WHERE oldid = maingroup;
UPDATE knowledge_category SET maingroup = (SELECT id FROM tmp WHERE tmp.oldid = knowledge_category.maingroup) WHERE knowledge_category.maingroup != 0;
SELECT * FROM knowledge_category ORDER BY id;-- WHERE maingroup = 0 ;