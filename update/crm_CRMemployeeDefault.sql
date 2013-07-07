-- @tag: CRMemployeeDefault
-- @description: Zusätzliche Attribute für den User mit Default-Werten initialisieren 

-- @php: *

$felder = array(    'msrv'                      => 't',
                    'postf'                     => 't',
                    'kennw'                     => 't',
                    'postf2'                    => 't',
                    'abteilung'                 => 't',
                    'position'                  => 't',
                    'interv'                    => 'i',
                    'pre'                       => 't',
                    'vertreter'                 => 'i',
                    'mailsign'                  => 't',
                    'email'                     => 't',
                    'etikett'                   => 'i',
                    'termbegin'                 => 'i',
                    'termend'                   => 'i', 
                    'termseq'                   => 'i',
                    'kdview'                    => 'i',
                    'mailuser'                  => 't',
                    'port'                      => 'i',
                    'proto'                     => 'b',
                    'ssl'                       => 't',
                    'preon'                     => 'b',
                    'icalart'                   => 't',
                    'icaldest'                  => 't',
                    'icalext'                   => 't',
                    'streetview'                => 't',
                    'planspace'                 => 't',
                    'theme'                     => 't',
                    'helpmode'                  => 'b',
                    'listen_theme'              => 't',
                    'auftrag_button'            => 'b',
                    'angebot_button'            => 'b',
                    'rechnung_button'           => 'b',
                    'liefer_button'             => 'b',
                    'zeige_extra'               => 'b',
                    'zeige_lxcars'              => 'b',
                    'zeige_karte'               => 'b',
                    'zeige_tools'               => 'b',
                    'zeige_etikett'             => 'b',
                    'zeige_bearbeiter'          => 'b',
                    'zeige_dhl'                 => 'b',
                    'feature_ac'                => 'b',
                    'feature_ac_minlength'      => 'i',
                    'feature_ac_delay'          => 'i',
                    'feature_unique_name_plz'   => 'b',
                    'show_err'                  => 'b',
                    'data_from_tel'             => 'b',
                    'tinymce'                   => 'b');
                    
$defaults = array(  'msrv'                      => "your_mail_server",
                    'postf'                     => "",
                    'kennw'                     => "",
                    'postf2'                    => "",
                    'abteilung'                 => "",
                    'position'                  => "",
                    'interv'                    => "60",
                    'pre'                       => "%",
                    'vertreter'                 => "",
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
                    'streetview'                => "http://maps.google.de/maps?f=d&hl=de&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%",
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
                    'zeige_bearbeiter'          => "t",
                    'zeige_dhl'                 => "t",
                    'feature_ac'                => "t",
                    'feature_ac_minlength'      => "2",
                    'feature_ac_delay'          => "100",
                    'feature_unique_name_plz'   => "t",
                    'show_err'                  => "f",
                    'php_error'                 => "f",
                    'data_from_tel'             => "f",
                    'tinymce'                   => "t");

$employee = $_SESSION['db']->getAll('select * from employee');
$r1 = $_SESSION['db']->begin();
foreach ($employee as $login) {
    foreach ($felder as $key => $val) {
        if ( array_key_exists($key,$login) ) {
            $sql = 'INSERT INTO crmemployee (uid,key,val,typ) VALUES ('.$login['id'].",'$key','".$login[$key]."','$val')";
        } else {
            $sql = 'INSERT INTO crmemployee (uid,key,val,typ) VALUES ('.$login['id'].",'$key','".$defaults[$key]."','$val')";
        }
        $rc = $_SESSION['db']->query($sql);
        if ( ! $rc ) {
            $_SESSION['db']->rollback();
            return -1;
        }
    }
}
foreach ($felder as $key => $val) {
    if (array_key_exists($key,$login)) $rc = $_SESSION['db']->query('ALTER TABLE employee DROP COLUMN '.$key);
};
$_SESSION['db']->commit();
return true;
-- @exec: *

