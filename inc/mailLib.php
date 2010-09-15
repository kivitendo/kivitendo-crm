<?php
function mail_login($host,$port,$user,$pass,$pop=false,$folder="INBOX",$ssl=false)
{
    if ($pop) {
        if (empty($port)) $port = '110';
        $ssl = ($ssl==false)?"/novalidate-cert":"";
        $server = "{"."$host:$port/pop3$ssl"."}$folder";
    } else {
        if (empty($port)) $port = '143';
        $ssl=($ssl==false)?"/notls":"";
        $server = "{"."$host:$port/imap$ssl"."}";
    }
    return (@imap_open($server,$user,$pass));
}
function mail_stat($connection)       
{
    $check = @imap_mailboxmsginfo($connection);
    return ((array)$check);
}
function mail_list($connection,$message="")
{
    mb_internal_encoding(ini_get("default_charset"));
    if ($message)
    {
        $range=$message;
    } else {
        $MC = @imap_check($connection);
        $range = "1:".$MC->Nmsgs;
    }
    $response = @imap_fetch_overview($connection,$range);
    foreach ($response as $msg) {
        $msg->subject = mb_decode_mimeheader($msg->subject);
        $msg->from = mb_decode_mimeheader($msg->from);
        $msg->to = mb_decode_mimeheader($msg->to);
        $date = date_parse($msg->date);
        $msg->date = sprintf("%02d.%02d.%04d",$date["day"],$date["month"],$date["year"]);
        $msg->time = sprintf("%02d:%02d",$date["hour"],$date["minute"]);
        $result[$msg->msgno]=(array)$msg;
    }
    return $result;
}
function mail_retr($conn,$message)
{
    return(@imap_fetchheader($conn,$message,FT_PREFETCHTEXT));
}
function mail_dele($conn,$message)
{
    return(@imap_delete($conn,$message));
}
function mail_parse_headers($headers)
{
    mb_internal_encoding(ini_get("default_charset"));
    $headers=preg_replace('/\r\n\s+/m', '',$headers);
    preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
    foreach ($matches[1] as $key =>$value) $result[$value]=$matches[2][$key];
    if ($result["Subject"] != "") $result["Subject"] = mb_decode_mimeheader($result["Subject"]);
    if ($result["From"] != "") $result["From"] = mb_decode_mimeheader($result["From"]);
    if ($result["To"] != "") $result["To"] = mb_decode_mimeheader($result["To"]);
    return($result);
}
function mail_mime_to_array($conn,$mid,$parse_headers=false)
{
    $struc = imap_fetchstructure($conn,$mid);
    $mail = @mail_get_parts($conn,$mid,$stuc,0);
    if ($parse_headers) $mail[0]["parsed"]=mail_parse_headers($mail[0]["data"]);
    return($mail);
}
function mail_get_parts($conn,$mid,$part,$prefix)
{   
    $attachments=array();
    $attachments[$prefix]=mail_decode_part($conn,$mid,$part,$prefix);
    if (isset($part->parts)) // multipart
    {
        $prefix = ($prefix == "0")?"":"$prefix.";
        foreach ($part->parts as $number=>$subpart)
            $attachments=array_merge($attachments, mail_get_parts($conn,$mid,$subpart,$prefix.($number+1)));
    }
    return $attachments;
}
function mail_decode_part($conn,$message_number,$part,$prefix)
{
    $attachment = array();

    if($part->ifdparameters) {
        foreach($part->dparameters as $object) {
            $attachment[strtolower($object->attribute)]=$object->value;
            if(strtolower($object->attribute) == 'filename') {
                $attachment['is_attachment'] = true;
                $attachment['filename'] = $object->value;
            }
        }
    }

    if($part->ifparameters) {
        foreach($part->parameters as $object) {
            $attachment[strtolower($object->attribute)]=$object->value;
            if(strtolower($object->attribute) == 'name') {
                $attachment['is_attachment'] = true;
                $attachment['name'] = $object->value;
            }
        }
    }

    $attachment['data'] = imap_fetchbody($conn, $message_number, $prefix);
    if($part->encoding == 3) { // 3 = BASE64
        $attachment['data'] = base64_decode($attachment['data']);
    }
    elseif($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
        $attachment['data'] = quoted_printable_decode($attachment['data']);
    }
    return($attachment);
}


function create_part_array($structure, $prefix="") {
    //print_r($structure);
    if (sizeof($structure->parts) > 0) {    // There some sub parts
        foreach ($structure->parts as $count => $part) {
            add_part_to_array($part, $prefix.($count+1), $part_array);
        }
    }else{    // Email does not have a seperate mime attachment for text
        $part_array[] = array('part_number' => $prefix.'1', 'part_object' => $obj);
    }
   return $part_array;
}
// Sub function for create_part_array(). Only called by create_part_array() and itself.
function add_part_to_array($obj, $partno, & $part_array) {
    $part_array[] = array('part_number' => $partno, 'part_object' => $obj);
    if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
        //print_r($obj);
        if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
            foreach ($obj->parts as $count => $part) {
                // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
                if (sizeof($part->parts) > 0) {
                    foreach ($part->parts as $count2 => $part2) {
                        add_part_to_array($part2, $partno.".".($count2+1), $part_array);
                    }
                }else{    // Attached email does not have a seperate mime attachment for text
                    $part_array[] = array('part_number' => $partno.'.'.($count+1), 'part_object' => $obj);
                }
            }
        }else{    // Not sure if this is possible
            $part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
        }
    }else{    // If there are more sub-parts, expand them out.
        if (sizeof($obj->parts) > 0) {
            foreach ($obj->parts as $count => $p) {
                add_part_to_array($p, $partno.".".($count+1), $part_array);
            }
        }
    }
}
function mail_get_file($conn,$mail,$part) 
{
    $partno = $part["part_number"];
    if ($partno < 2) return false;
    $type = $part["part_object"]->subtype;
    $filename = 'mail'.$mail.'attachment'.$partno.'.'.$type;
    if ($part["part_object"]->parameters) { 
        $parameters = $part["part_object"]->parameters;
    } else  if ($part["part_object"]->dparameters) { 
        $parameters = $part["part_object"]->dparameters;
    }
    if ($parameters) foreach ($parameters as $data) {
            if ($data->attribute == "name" ||
                $data->attribute == "filename" ) {
                $filename = $data->value;
                break;
            }
        } 
    $file = imap_fetchbody($conn,$mail,$partno);
    if ($part["part_object"]->encoding == 3) { // 3 = BASE64
        $file = base64_decode($file);
    } else if ($part["part_object"]->encoding == 4) { // 4 = QUOTED-PRINTABLE
        $file = quoted_printable_decode($file);
    }
    $f=fopen("tmp/".$filename,"w");
	fwrite($f,$file);
	fclose($f);
    $data =  array("size"=>$part["part_object"]->bytes,"name"=>$filename,"nummer"=>$partno,"type"=>$type);
    return $data;
}

function mail_get_body($conn,$mail,$part) 
{
    $partno = $part["part_number"];
    $type = $part["part_object"]->subtype;
    if ($type == "PLAIN") {
        $body = imap_fetchbody($conn,$mail,$partno);
    } else if ($type == "HTML") {
        $htmlbody = imap_fetchbody($conn,$mail,$partno);
        //den htmlbody noch als File speichern??
        $body = strip_tags($htmlbody);
    }
    $charset = ini_get("default_charset");
    if ($part["part_object"]->parameters) foreach ($part["part_object"]->parameters as $param) {
        if ($param->attribute == "charset") $charset = $param->value;
    }
    $body = imap_qprint($body);
    $body = iconv($charset,ini_get("default_charset")."//Transient",$body);
    return $body;
}

?>
