-- @tag: UserConfig_1
-- @description: Viele Einstellungen durch Benutzer ermöglichen

ALTER TABLE employee     ADD COLUMN helpmode                 BOOL    DEFAULT TRUE;
ALTER TABLE employee     ADD COLUMN listen_theme             TEXT;
ALTER TABLE employee     ADD COLUMN auftrag_button           BOOL    DEFAULT FALSE;
ALTER TABLE employee     ADD COLUMN angebot_button           BOOL    DEFAULT FALSE;
ALTER TABLE employee     ADD COLUMN rechnung_button          BOOL    DEFAULT FALSE;
ALTER TABLE employee     ADD COLUMN zeige_extra              BOOL    DEFAULT TRUE;
ALTER TABLE employee     ADD COLUMN zeige_lxcars             BOOL    DEFAULT FALSE;
ALTER TABLE employee     ADD COLUMN feature_ac               BOOL    DEFAULT FALSE;
ALTER TABLE employee     ADD COLUMN feature_ac_minLength     INTEGER DEFAULT 3;
ALTER TABLE employee     ADD COLUMN feature_ac_delay         INTEGER DEFAULT 0;
ALTER TABLE employee     ADD COLUMN feature_unique_name_plz  BOOL    DEFAULT TRUE;
ALTER TABLE employee     ADD COLUMN dir_group                TEXT    DEFAULT 'users';
ALTER TABLE employee     ADD COLUMN dir_mode                 CHAR(4) DEFAULT '0775';
ALTER TABLE employee     ADD COLUMN sep_cust_vendor          BOOL    DEFAULT TRUE;
ALTER TABLE employee     ADD COLUMN show_err                 BOOL    DEFAULT FALSE;
ALTER TABLE employee     ADD COLUMN kicktel_API              TEXT;
ALTER TABLE employee     ADD COLUMN data_from_tel            BOOL    DEFAULT FALSE;
ALTER TABLE employee     ADD COLUMN google_API               TEXT;