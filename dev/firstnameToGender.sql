       $sql = 'DROP TABLE IF EXISTS firstnameToGender';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'DROP TABLE IF EXISTS firstnameToGender';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE firstnameToGender( gender CHAR(1), firstname TEXT UNIQUE PRIMARY KEY )';
        $GLOBALS['dbh']->query( $sql );
