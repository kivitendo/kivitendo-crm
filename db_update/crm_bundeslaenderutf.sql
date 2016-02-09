-- @tag: bundeslaenderutf
-- @description: umstellen auf utf8

update bundesland set bundesland = 'Baden-Württemberg' where bundesland = 'Baden-W&uuml;rttemberg';
update bundesland set bundesland = 'Thüringen' where bundesland = 'Th&uuml;ringen';
update bundesland set bundesland = 'Graubünden' where bundesland = 'Graub&uuml;nden';
update bundesland set bundesland = 'Zürich' where bundesland = 'Z&uuml;rich';
update bundesland set bundesland = 'Kärnten' where bundesland = 'K&auml;rnten';
update bundesland set bundesland = 'Niederösterreich' where bundesland = 'Nieder&ouml;sterreich';
update bundesland set bundesland = 'Oberösterreich' where bundesland = 'Ober&ouml;sterreich';

