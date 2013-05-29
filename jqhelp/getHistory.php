<?php
require_once("../inc/stdLib.php");

$history_data = history();
$liste = '';
foreach ( $history_data as $key => $value ) {
    $liste .= '<li id="'.$value[2].$value[0].'"><a href="javascript:void(0);">'.$value[1].'</a></li>';
}
echo $liste;
/*
echo '
<li id="opt_1.1"><a href="javascript:void(0);">Option-1.1</a></li>
    <hr>
    <li id="opt_1.2"><a href="javascript:void(0);"><img
        src="images/feed.png"
        style="margin-right: 3px; margin-bottom: -2px; border: 0">Option-1.2</a></li>
    <li id="opt_1.3"><a href="javascript:void(0);"><img
        src="images/feed_edit.png"
        style="margin-right: 3px; margin-bottom: -2px; border: 0">Option-1.3</a>
    </li>
    <li id="opt_1.4"><a href="javascript:void(0);"><img
        src="images/feed_delete.png"
        style="margin-right: 3px; margin-bottom: -2px; border: 0">Option-1.4<br>
      <small>with second line</small>
    </a></li>
    <hr>
    <li id="opt_1.5">
      <a href="javascript:void(0);">
        <div style="display: table-row">
          <input type="checkbox" id="chk1" style="margin-left: 0">
          <label for="chk1"
               style="display: table-cell; text-align: left; width: 100%;">Option-1.5</label>
        </div>
      </a>
    </li>
  </ul>
';
*/
?>