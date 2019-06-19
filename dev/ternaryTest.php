<?php
  function one(){
    return 1;
  }
  function null(){
  }
  echo 'Test Null Coalescing Operator und Ternärer Operator<br />';
  echo one() ?? null();
  echo '<br />';
  echo null() ?? one();
  echo '<br />';
  echo null() ?: one();
  echo '<br />';
  echo one() ?: null();

?>