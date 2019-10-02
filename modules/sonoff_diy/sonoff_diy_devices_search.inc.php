<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['sonoff_diy_devices_qry'];
  } else {
   $session->data['sonoff_diy_devices_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_sonoff_diy_devices="ID DESC";
  $out['SORTBY']=$sortby_sonoff_diy_devices;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM sonoff_diy_devices WHERE $qry ORDER BY ".$sortby_sonoff_diy_devices);
  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $tmp=explode(' ', $res[$i]['UPDATED']);
    $res[$i]['UPDATED']=fromDBDate($tmp[0])." ".$tmp[1];
    $table_name='sonoff_diy_data';
    $id = $res[$i]['ID'];
    $value=SQLSelectOne("SELECT * FROM $table_name WHERE DEVICE_ID='$id' and TITLE='alive'");
    if ($value['VALUE'])
        $res[$i]['ONLINE'] = 1;
    else
        $res[$i]['ONLINE'] = 0;
   }
   $out['RESULT']=$res;
  }
