-- @tag: CRMemployee
-- @description: Zusätzliche Attribute für den User in eine eigene Tabelle auslagern

CREATE TABLE crmemployee (
    ceid integer DEFAULT nextval('crmid'::text) NOT NULL,
    uid int,
    key text,
    val text,
    typ char(1) DEFAULT 't'
);

-- @php: *
$felder = array('msrv' => 't','postf' => 't','kennw' => 't','postf2' => 't','abteilung' => 't','position' => 't','interv' => 'i','pre' => 't','vertreter' => 'i','mailsign' => 't','email' => 't','etikett' => 'i','termbegin' => 'i','termend' => 'i','termseq' => 'i','kdview' => 'i','mailuser' => 't','port' => 'i','proto' => 'b','ssl' => 't','preon' => 'b','icalart' => 't','icaldest' => 't','icalext' => 't','pwd' => 't','streetview' => 't','planspace' => 't','theme' => 't','helpmode' => 'b','listen_theme' => 't','auftrag_button' => 'b','angebot_button' => 'b','rechnung_button' => 'b','zeige_extra' => 'b','zeige_lxcars' => 'b','zeige_karte' => 'b','zeige_tools' => 'b','zeige_etikett' => 'b','feature_ac' => 'b','feature_ac_minlength' => 'i','feature_ac_delay' => 'i','feature_unique_name_plz' => 'b','show_err' => 'b','kicktel_api' => 't','data_from_tel' => 'b','tinymce' => 'b');
$employee = $db->getAll('select * from employee');
$r1 = $db->begin();
foreach ($employee as $login) {
    foreach ($felder as $key => $val) {
        if ( array_key_exists($key,$login) ) {
            $sql = 'INSERT INTO crmemployee (uid,key,val,typ) VALUES ('.$login['id'].",'$key','".$login[$key]."','$val')";
        } else {
            $sql = 'INSERT INTO crmemployee (uid,key,val,typ) VALUES ('.$login['id'].",'$key',null,'$val')";
        }
        $rc = $db->query($sql);
        if ( ! $rc ) {
            $db->rollback();
            return -1;
        }
    }
}
foreach ($felder as $key => $val) {
    if (array_key_exists($key,$login)) $rc = $db->query('ALTER TABLE employee DROP COLUMN '.$key);
};
$db->commit();
return true;
-- @exec: *

