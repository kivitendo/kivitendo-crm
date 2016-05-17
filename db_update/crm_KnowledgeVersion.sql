-- @tag: KnowledgeVersion
-- @description: Sets version for each category
-- @version: 2.2.1

UPDATE knowledge_content KC SET version = 1 WHERE ( KC.version is NULL AND KC.id = ( SELECT id FROM knowledge_content WHERE category = KC.category ORDER BY version, id DESC LIMIT 1));

-- @exec
