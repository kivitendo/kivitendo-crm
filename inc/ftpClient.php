<?php
  class implicitFtp{
    private $server;
    private $username;
    private $password;
    private $curlhandle;

    public function __construct( $server, $username, $password ){
      $this->server = $server;
      $this->username = $username;
      $this->password = $password;
      $this->curlhandle = curl_init();
    }

    public function __destruct(){
      if( !empty( $this->curlhandle ) )
        @curl_close( $this->curlhandle );
    }

    /**
    * @param string $remote remote path
    * @return resource a cURL handle on success, false on errors.
    */
    private function common( $remote ){
      curl_reset( $this->curlhandle );
      curl_setopt( $this->curlhandle, CURLOPT_URL, 'ftps://' . $this->server . '/' . $remote );
      curl_setopt( $this->curlhandle, CURLOPT_USERPWD, $this->username . ':' . $this->password );
      curl_setopt( $this->curlhandle, CURLOPT_SSL_VERIFYPEER, FALSE );
      curl_setopt( $this->curlhandle, CURLOPT_SSL_VERIFYHOST, FALSE );
      curl_setopt( $this->curlhandle, CURLOPT_FTP_SSL, CURLFTPSSL_TRY );
      curl_setopt( $this->curlhandle, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS );
      return $this->curlhandle;
    }

    public function download($remote, $local = null) {
      if( $local === null )
        $local = tempnam( '/tmp', 'implicit_ftp' );
      if( $fp = fopen( $local, 'w' ) ){
        $this->curlhandle = self::common( $remote );
        curl_setopt( $this->curlhandle, CURLOPT_UPLOAD, 0 );
        curl_setopt( $this->curlhandle, CURLOPT_FILE, $fp );
        curl_exec( $this->curlhandle );
        if( curl_error( $this->curlhandle ) ){
          return false;
        }
        else
          return $local;
      }//if fopen
      return false;
    }

    public function upload( $local, $remote ){
      if( $fp = fopen( $local, 'r' ) ){
        $this->curlhandle = self::common( $remote );
        curl_setopt( $this->curlhandle, CURLOPT_UPLOAD, 1 );
        curl_setopt( $this->curlhandle, CURLOPT_INFILE, $fp );
        curl_exec( $this->curlhandle );
        $err = curl_error( $this->curlhandle );
        return !$err;
      }
      return false;
    }

    /**
    * Get file/folder names
    * @param string $remote
    * @return string[]
    */
    public function listnames( $remote ){
      if( substr( $remote, -1 ) != '/' )
        $remote .= '/';
      $this->curlhandle = self::common( $remote );
      curl_setopt( $this->curlhandle, CURLOPT_UPLOAD, 0 );
      curl_setopt( $this->curlhandle, CURLOPT_FTPLISTONLY, 1 );
      curl_setopt( $this->curlhandle, CURLOPT_RETURNTRANSFER, 1 );
      $result = curl_exec( $this->curlhandle );
      if( curl_error( $this->curlhandle ) )
        return false;
      else{
        $files = explode( "\r\n", trim( $result ) );
        return $files;
        return $local;
      }
    }

    /**
    * Get file/folder names ordered by modified date
    * @param string $remote
    * @return string[]
    */
    public function listbydate( $remote ){
      $files = $this->listnames( $remote );
      if( empty( $files ) )
        return null;
      $filedata = array();
      foreach( $files as $file ){
        $this->curlhandle = self::common( $remote . '/' . $file );
        curl_setopt( $this->curlhandle, CURLOPT_NOBODY, 1 );
        curl_setopt( $this->curlhandle, CURLOPT_FILETIME, 1 );
        curl_setopt( $this->curlhandle, CURLOPT_RETURNTRANSFER, 1 );
        $result = curl_exec( $this->curlhandle );
        if( $result ){
          $timestamp = curl_getinfo( $this->curlhandle, CURLINFO_FILETIME );
          $fileobj = array();
          $fileobj['name'] = $file;
          $fileobj['lastmodified'] = ( $timestamp != -1 ) ? date( "Y-m-d H:i:s", $timestamp ) : null;
          $filedata[] = $fileobj;
        }
      }//foreach
      usort( $filedata, function( $item1, $item2 ){
        return date( $item2['lastmodified'] ) <=> date( $item1['lastmodified'] );
      });
      return $filedata;
    }

    /**
    * Get file/folder raw data
    * @param string $remote
    * @return string[]
    */
    public function rawlist( $remote ){
      if( substr( $remote, -1 ) != '/' )
        $remote .= '/';
      $this->curlhandle = self::common($remote);
      curl_setopt( $this->curlhandle, CURLOPT_UPLOAD, 0 );
      curl_setopt( $this->curlhandle, CURLOPT_RETURNTRANSFER, 1 );
      $result = curl_exec( $this->curlhandle );
      if( curl_error( $this->curlhandle ) )
        return false;
      else {
        $files = explode("\n", trim($result));
        return $files;
        return $local;
      }
    }

    /**
    * Get file/folder parsed data into an array
    * @param string $remote
    * @return array[]
    */
    public function list( $remote ){
      $this->curlhandleildren = $this->rawlist( $remote );
      if( !empty( $this->curlhandleildren ) ){
        $items = array();
        foreach( $this->curlhandleildren as $this->curlhandleild ){
          $chunks = preg_split( "/\s+/", $this->curlhandleild );
          list( $item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time'] ) = $chunks;
          array_splice( $chunks, 0, 8 );
          $item['name'] = trim( implode( " ", $chunks ) );
          $item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file';
          $items[] = $item;
        }
        return $items;
      }
      return false;
    }
  }
?>