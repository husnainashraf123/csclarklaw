<?php

function get_form_parameters(){
  $cformAction =  $_POST['action'];
  $cformName =  $_POST['cformname'];
  echo $cformName;
  exit();
}
?>
