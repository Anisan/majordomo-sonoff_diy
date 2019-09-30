<?php
/**
* Sonoff DIY 
* @package project
* @author Eraser <eraser1981@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 19:09:24 [Sep 27, 2019])
*/
//
//
require_once("./modules/sonoff_diy/mdns.php"); 

class sonoff_diy extends module {
/**
* sonoff_diy
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="sonoff_diy";
  $this->title="Sonoff DIY";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
  $this->mdns = new mDNS();
  
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 if (!gg('cycle_sonoff_diyRun')) {
            setGlobal('cycle_sonoff_diyRun',1);
        }

        if ((time() - gg('cycle_sonoff_diyRun')) < 60 ) {
            $out['CYCLERUN'] = 1;
        } else {
            $out['CYCLERUN'] = 0;
        }
 $out['API_URL']=$this->config['API_URL'];
 if (!$out['API_URL']) {
  $out['API_URL']='http://';
 }
 $out['API_KEY']=$this->config['API_KEY'];
 $out['API_USERNAME']=$this->config['API_USERNAME'];
 $out['API_PASSWORD']=$this->config['API_PASSWORD'];
 if ($this->view_mode=='update_settings') {
   global $api_url;
   $this->config['API_URL']=$api_url;
   global $api_key;
   $this->config['API_KEY']=$api_key;
   global $api_username;
   $this->config['API_USERNAME']=$api_username;
   global $api_password;
   $this->config['API_PASSWORD']=$api_password;
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='sonoff_diy_devices' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_sonoff_diy_devices') {
   $this->search_sonoff_diy_devices($out);
  }
  if ($this->view_mode=='edit_sonoff_diy_devices') {
   $this->edit_sonoff_diy_devices($out, $this->id);
  }
  if ($this->view_mode=='delete_sonoff_diy_devices') {
   $this->delete_sonoff_diy_devices($this->id);
   $this->redirect("?data_source=sonoff_diy_devices");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='sonoff_diy_data') {
  if ($this->view_mode=='' || $this->view_mode=='search_sonoff_diy_data') {
   $this->search_sonoff_diy_data($out);
  }
  if ($this->view_mode=='edit_sonoff_diy_data') {
   $this->edit_sonoff_diy_data($out, $this->id);
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* sonoff_diy_devices search
*
* @access public
*/
 function search_sonoff_diy_devices(&$out) {
  require(DIR_MODULES.$this->name.'/sonoff_diy_devices_search.inc.php');
 }
/**
* sonoff_diy_devices edit/add
*
* @access public
*/
 function edit_sonoff_diy_devices(&$out, $id) {
  require(DIR_MODULES.$this->name.'/sonoff_diy_devices_edit.inc.php');
 }
/**
* sonoff_diy_devices delete record
*
* @access public
*/
 function delete_sonoff_diy_devices($id) {
  $rec=SQLSelectOne("SELECT * FROM sonoff_diy_devices WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM sonoff_diy_devices WHERE ID='".$rec['ID']."'");
 }
/**
* sonoff_diy_data search
*
* @access public
*/
 function search_sonoff_diy_data(&$out) {
  require(DIR_MODULES.$this->name.'/sonoff_diy_data_search.inc.php');
 }
/**
* sonoff_diy_data edit/add
*
* @access public
*/
 function edit_sonoff_diy_data(&$out, $id) {
  require(DIR_MODULES.$this->name.'/sonoff_diy_data_edit.inc.php');
 }
 function propertySetHandle($object, $property, $value) {
   $this->getConfig();
   $table='sonoff_diy_data';
   $properties=SQLSelect("SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
      $device_id = $properties[$i]["DEVICE_ID"];
      $table='sonoff_diy_devices';
      $rec=SQLSelectOne("SELECT * FROM $table WHERE ID=$device_id");
      if ($rec['ID']) {
        $ip = $rec["IP"];
        $port = $rec["PORT"];
        if ($properties[$i]["TITLE"] == "switch")
        {
             $url = "http://$ip:$port/zeroconf/switch";
             if ($value == 1) $val = 'on';
             if ($value == 0) $val = 'off';
             $data = array();
             $data['deviceid'] = $rec['DEVICE_ID'];
             $data['data'] = array();
             $data['data']['switch'] = $val;
             //registerError('sonoff_diy', $url . print_r($data));
             $this->callApi($url,$data);
        }
        if ($properties[$i]["TITLE"] == "startup") // on off stay
        {
            $url = "http://$ip:$port/zeroconf/startup";
            $data = array();
            $data['deviceid'] = $rec['DEVICE_ID'];
            $data['data'] = array();
            $data['data']['startup'] = $value;
            $this->callApi($url,$data);
        }
        if ($properties[$i]["TITLE"] == "sledOnline") 
        {
            $url = "http://$ip:$port/zeroconf/ledOnline";
            if ($value == 1) $val = 'on';
            if ($value == 0) $val = 'off';
            $data = array();
            $data['deviceid'] = $rec['DEVICE_ID'];
            $data['data'] = array();
            $data['data']['ledOnline'] = $val;
            $this->callApi($url,$data);
        }
        if ($properties[$i]["TITLE"] == "pulse")
        {
            $url = "http://$ip:$port/zeroconf/pulse";
            if ($value == 1) $val = 'on';
            if ($value == 0) $val = 'off';
            $table='sonoff_diy_data';
            $pulseWidth=SQLSelectOne("SELECT * FROM $table WHERE DEVICE_ID=". $rec['ID'] ." and TITLE = 'pulseWidth'");
            
            $data = array();
            $data['deviceid'] = $rec['DEVICE_ID'];
            $data['data'] = array();
            $data['data']['pulse'] = $val;
            $data['data']['pulseWidth'] = intval($pulseWidth["VALUE"]);
             
            $this->callApi($url,$data);
        }
        if ($properties[$i]["TITLE"] == "pulseWidth")
        {
            $url = "http://$ip:$port/zeroconf/pulse";
            $table='sonoff_diy_data';
            $pulse=SQLSelect("SELECT * FROM $table WHERE DEVICE_ID=". $rec['ID'] ." and TITLE = 'pulse'");
            if ($pulse["VALUE"] == 1) $val = 'on';
            if ($pulse["VALUE"] == 0) $val = 'off';
            
            $data = array();
            $data['deviceid'] = $rec['DEVICE_ID'];
            $data['data'] = array();
            $data['data']['pulse'] = $val;
            $data['data']['pulseWidth'] = intval($value);
            
            $this->callApi($url,$data);
        }
      } 
    }
   }
 }
 
 function callAPI($url, $params = 0)
{
    try
    { 
        $data_string = json_encode($params);
        registerError('sonoff_diy', $url. " " . $data_string);
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        registerError('sonoff_diy', 'Result query - '.$url.' == '. $result);
        $result = json_decode($result);
        
    }
    catch (Exception $e)
    {
        registerError('sonoff_diy', 'Error send query - '.$url.' == '.get_class($e) . ', ' . $e->getMessage());
        $result = array();
        $result["error"] = 1;
        $result["data"] = array();
        $result["data"]["class"] = get_class($e);
        $result["data"]["message"] = $e->getMessage();
    } 
    return $result;
}

 
 function updateDevice($name, $key, $value)
 {
    $table_name='sonoff_diy_devices';
    $rec=SQLSelectOne("SELECT * FROM $table_name WHERE MDNS_NAME='$name'");
    $rec['MDNS_NAME'] = $name;
    if ($rec['ID']) {
        //SQLUpdate($table_name, $rec); // update
    } else {
        $rec['TITLE'] = $name;
        $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    if ($key!="")
    {
        if ($rec[$key] != $value)
        {
            $rec[$key] = $value;
            SQLUpdate($table_name, $rec);
        }
    }
 }
 function updateData($name, $data)
 {
    print_r($data);
    $table_name='sonoff_diy_devices';
    $rec=SQLSelectOne("SELECT * FROM $table_name WHERE MDNS_NAME='$name'");
    
    if ($rec['ID']) {
        //print_r($rec);
        $table_name='sonoff_diy_data';
        $id = $rec['ID'];
        $values=SQLSelect("SELECT * FROM $table_name WHERE DEVICE_ID='$id'");
        foreach ($data as $key => $val)
        {
            //echo $key."-".$val."\n";
            $value_ind = array_search($key, array_column($values, 'TITLE'));
            if ($value_ind !== False)
                $value = $values[$value_ind];
            else
                $value = array();
            //print_r($value);
            $value["TITLE"] = $key;
            $value["DEVICE_ID"] = $rec['ID'];
            $value["UPDATED"] = date('Y-m-d H:i:s');
            if ($key != "startup")
            {
                if ($val == 'on') $val = 1;
                if ($val == 'off') $val = 0;
            }
            if ($value['ID']) {
                if ($value["VALUE"] != $val)
                {   
                    $value["VALUE"] = $val;
                    SQLUpdate($table_name, $value);
                    if ($value['LINKED_OBJECT'] && $value['LINKED_PROPERTY']) {
                        setGlobal($value['LINKED_OBJECT'] . '.' . $value['LINKED_PROPERTY'], $val, array($this->name => '0'));
               }
                }
            }
            else{
                $value["VALUE"] = $val;
                SQLInsert($table_name, $value);
            }
        }
    }
 }
 
 function processCycle() {
    $this->getConfig();
    // Search for devices
	// For a bit more surety, send multiple search requests
	//$this->mdns->query("_ewelink._tcp.local",1,12,"");
	//$this->mdns->query("_ewelink._tcp.local",1,16,"");
	//$this->mdns->query("_ewelink._tcp.local",1,33,"");
    $inpacket = $this->mdns->readIncoming();
    //print_r ($inpacket);
    //echo '<br>';
	//$mdns->printPacket($inpacket);
    // If our packet has answers, then read them
	if (sizeof($inpacket->answerrrs)> 0) {
		for ($x=0; $x < sizeof($inpacket->answerrrs); $x++) {
            if (strpos($inpacket->answerrrs[$x]->name ,"_ewelink._tcp.local")  === false &&
                strpos($inpacket->answerrrs[$x]->name ,"eWeLink")  === false)
                continue;
            echo date('Y-m-d H:i:s')." ".$inpacket->answerrrs[$x]->name . "\n";
            $name = substr($inpacket->answerrrs[$x]->name, 0, strpos($inpacket->answerrrs[$x]->name,'.'));
            echo date('Y-m-d H:i:s')." ".$name . "\n";
            //print_r($inpacket->answerrrs[$x]);
            // PTR
			if ($inpacket->answerrrs[$x]->qtype == 12) {
                if ($inpacket->answerrrs[$x]->name == "_ewelink._tcp.local") {
					$name = "";
					for ($y = 0; $y < sizeof($inpacket->answerrrs[$x]->data); $y++) {
						$nameMDNS .= chr($inpacket->answerrrs[$x]->data[$y]);
					}
                    $name = substr($nameMDNS, 0, strpos($nameMDNS,'.'));
                    print_r($name);
                    // add device 
                    $this->updateDevice($name,"","");
					// Send a a SRV query
					$this->mdns->query($nameMDNS, 1, 16, "");
				}
			}
            // TXT data
            if ($inpacket->answerrrs[$x]->qtype == 16) {
                //print_r($inpacket->answerrrs[$x]->data);
                $d = array();
                for ($y = 0; $y < sizeof($inpacket->answerrrs[$x]->data); $y++) {
                    $len = $inpacket->answerrrs[$x]->data[$y];
                    $c = $y;
                    $kv = false;
                    $key ="";
                    $value = "";
                    ++$y;
                    while ($y<=$c+$len){
                        $ch = chr($inpacket->answerrrs[$x]->data[$y]);
                        if ($ch == '=')
                            $kv = true;
                        else {
                            if (!$kv)
                                $key .= $ch;
                            else
                                $value .= $ch;
                        }
                        ++$y;
                    }
                    --$y;
                    $d[$key] = $value;
                }
				
				print_r($d);
                $this->updateDevice($name,"DEVICE_ID",$d['id']);
                $this->updateDevice($name,"UPDATED",date('Y-m-d H:i:s'));
                //update data device
                $this->updateData($name,json_decode($d['data1']));
			}
            // SRV
			if ($inpacket->answerrrs[$x]->qtype == 33) {
				$d = $inpacket->answerrrs[$x]->data;
				$port = ($d[4] * 256) + $d[5];
				// We need the target from the data
				$offset = 6;
				$size = $d[$offset];
				$offset++;
				$target = "";
				for ($z=0; $z < $size; $z++) {
					$target .= chr($d[$offset + $z]);
				}
				$target .= ".local";
                // update $port device
				//$port  $target
                echo "PORT ".$port." ".  $name."\n";
                $this->updateDevice($name,"PORT",$port);
				// We know the name and port. Send an A query for the IP address
				$this->mdns->query($target,1,1,"");
			}
            // A
			if ($inpacket->answerrrs[$x]->qtype == 1) {
				$d = $inpacket->answerrrs[$x]->data;
				$ip = $d[0] . "." . $d[1] . "." . $d[2] . "." . $d[3];
                // update $IP device
                echo "IP ".$ip." ".  $name."\n";
                $this->updateDevice($name,"IP",$ip);

			}
		}
	}
  
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS sonoff_diy_devices');
  SQLExec('DROP TABLE IF EXISTS sonoff_diy_data');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
sonoff_diy_devices - 
sonoff_diy_data - 
*/
  $data = <<<EOD
 sonoff_diy_devices: ID int(10) unsigned NOT NULL auto_increment
 sonoff_diy_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_devices: MDNS_NAME varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_devices: DEVICE_ID varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_devices: IP varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_devices: PORT varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_devices: UPDATED datetime
 sonoff_diy_data: ID int(10) unsigned NOT NULL auto_increment
 sonoff_diy_data: TITLE varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_data: VALUE varchar(255) NOT NULL DEFAULT ''
 sonoff_diy_data: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 sonoff_diy_data: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_data: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_data: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_data: UPDATED datetime
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgU2VwIDI3LCAyMDE5IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
