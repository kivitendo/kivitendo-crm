-- @tag: bundeslaender
-- @description: Schreibfehler bereinigen

update bundesland set bundesland = 'Sachsen-Anhalt' where bundesland = 'Sachen-Anhalt';
update bundesland set bundesland = 'Th&uuml;ringen' where bundesland = 'Th&uuml;ingen';

