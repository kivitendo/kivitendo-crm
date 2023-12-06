-- @tag: AppDeliveryTime
-- @description: Upgrade script for calendar function in crm_app
-- @version: 2.3.4

ALTER TABLE IF EXISTS oe ADD COLUMN delivery_time text;
