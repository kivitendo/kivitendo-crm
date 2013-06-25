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
echo "CRMemployee wird ausgeführt... </ br>";
$felder = array('msrv' => 't','postf' => 't','kennw' => 't','postf2' => 't','abteilung' => 't','position' => 't','interv' => 'i','pre' => 't','vertreter' => 'i','mailsign' => 't','email' => 't','etikett' => 'i','termbegin' => 'i','termend' => 'i','termseq' => 'i','kdview' => 'i','mailuser' => 't','port' => 'i','proto' => 'b','ssl' => 't','preon' => 'b','icalart' => 't','icaldest' => 't','icalext' => 't','streetview' => 't','planspace' => 't','theme' => 't','helpmode' => 'b','listen_theme' => 't','auftrag_button' => 'b','angebot_button' => 'b','rechnung_button' => 'b','zeige_extra' => 'b','zeige_lxcars' => 'b','zeige_karte' => 'b','zeige_tools' => 'b','zeige_etikett' => 'b','feature_ac' => 'b','feature_ac_minlength' => 'i','feature_ac_delay' => 'i','feature_unique_name_plz' => 'b','show_err' => 'b','data_from_tel' => 'b','tinymce' => 'b');
$defaults = array(  'msrv'                      => "NULL",
                    'postf'                     => "NULL",
                    'kennw'                     => "NULL",
                    'postf2'                    => "NULL",
                    'abteilung'                 => "NULL",
                    'position'                  => "NULL",
                    'interv'                    => "60",
                    'pre'                       => "%",
                    'vertreter'                 => "NULL",
                    'mailsign'                  => "--Your Signature",
                    'email'                     => "change@me.tld",
                    'etikett'                   => "1",
                    'termbegin'                 => "8",
                    'termend'                   => "20",
                    'termseq'                   => "30",
                    'kdview'                    => "3",
                    'mailuser'                  => "your_mail_login",
                    'port'                      => "143",
                    'proto'                     => "t",
                    'ssl'                       => "f",
                    'preon'                     => "t",
                    'icalart'                   => "file",
                    'icaldest'                  => "",
                    'icalext'                   => "",
                    'streetview'                => "http://maps.google.de/maps?f=d&hl=de&saddr=Ensingerstrasse+19,89073+Ulm&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%",
                    'planspace'                 => "+",
                    'theme'                     => "base",
                    'helpmode'                  => "t",
                    'listen_theme'              => "blue",
                    'auftrag_button'            => "t",
                    'angebot_button'            => "t",
                    'rechnung_button'           => "t",
                    'liefer_button'             => "t",
                    'zeige_extra'               => "t",
                    'zeige_lxcars'              => "f",
                    'zeige_karte'               => "t",
                    'zeige_tools'               => "t",
                    'zeige_etikett'             => "t",
                    'feature_ac'                => "t",
                    'feature_ac_minlength'      => "2",
                    'feature_ac_delay'          => "100",
                    'feature_unique_name_plz'   => "t",
                    'show_err'                  => "f",
                    'php_error'                 => "f",
                    'data_from_tel'             => "f",
                    'tinymce'                   => "t");

$employee = $db->getAll('select * from employee');
$r1 = $db->begin();
foreach ($employee as $login) {
    foreach ($felder as $key => $val) {
        if ( array_key_exists($key,$login) ) {
            echo "login[key]: ".$login[$key]."</ br>";//ToDoDELETE AFTER DEBUG
            $sql = 'INSERT INTO crmemployee (uid,key,val,typ) VALUES ('.$login['id'].",'$key','".$login[$key]."','$val')";
        } else {
            echo "defaults[key]: ".$defaults[$key]."</ br>";//ToDoDELETE AFTER DEBUG
            $sql = 'INSERT INTO crmemployee (uid,key,val,typ) VALUES ('.$login['id'].",'$key','".$defaults[$key]."','$val')";
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

