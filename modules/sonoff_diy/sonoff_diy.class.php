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
      $device=SQLSelectOne("SELECT * FROM $table WHERE ID=$device_id");
      if ($device['ID']) {
        $params = array();
             
        if ($properties[$i]["TITLE"] == "switch")
        {
             $cmd = "zeroconf/switch";
             if ($value == 1) $val = 'on';
             if ($value == 0) $val = 'off';
             $params['switch'] = $val;
        }
		if ($properties[$i]["TITLE"] == "switch0" ||
			$properties[$i]["TITLE"] == "switch1" ||
			$properties[$i]["TITLE"] == "switch2" ||
			$properties[$i]["TITLE"] == "switch3" )
        {
             $cmd = "zeroconf/switches";
             if ($value == 1) $val = 'on';
             if ($value == 0) $val = 'off';
			 $switch = array();
             $switch['switch'] = $val;
			 $switch['outlet'] = intval(substr($properties[$i]["TITLE"],6,1));
			 $params['switches'] = $switch;
        }
        if ($properties[$i]["TITLE"] == "startup") // on off stay
        {
            $cmd = "zeroconf/startup";
            $params['startup'] = $value;
        }
        if ($properties[$i]["TITLE"] == "sledOnline") 
        {
            $cmd = "zeroconf/sledOnline";
            if ($value == 1) $val = 'on';
            if ($value == 0) $val = 'off';
            $params['sledOnline'] = $val;
        }
        if ($properties[$i]["TITLE"] == "pulse")
        {
            $cmd = "zeroconf/pulse";
            if ($value == 1) $val = 'on';
            if ($value == 0) $val = 'off';
            $table='sonoff_diy_data';
            $pulseWidth=SQLSelectOne("SELECT * FROM $table WHERE DEVICE_ID=". $device['ID'] ." and TITLE = 'pulseWidth'");
            
            $params['pulse'] = $val;
            $params['pulseWidth'] = intval($pulseWidth["VALUE"]);
        }
        if ($properties[$i]["TITLE"] == "pulseWidth")
        {
            $cmd = "zeroconf/pulse";
            $table='sonoff_diy_data';
            $pulse=SQLSelect("SELECT * FROM $table WHERE DEVICE_ID=". $device['ID'] ." and TITLE = 'pulse'");
            if ($pulse["VALUE"] == 1) $val = 'on';
            if ($pulse["VALUE"] == 0) $val = 'off';
            
            $params['pulse'] = $val;
            $params['pulseWidth'] = intval($value);
        }
        
        
        $res = $this->callApi($device,$cmd, $params);
        
        if ($res["error"] == 1)
        {
            $data = array();
            $data['alive'] = '0';
            $this->updateData($device['MDNS_NAME'],$data);
        }
      } 
    }
   }
 }

function callAPI($device, $cmd, $params)
{
    $ip = $device["IP"];
    $port = $device["PORT"];
    
    $url = "http://$ip:$port/$cmd";
        
    $data = array();
    $data['deviceid'] = $device['DEVICE_ID'];
    if ($device['DEVICE_KEY']!='')
    {
        $data['encrypt']=true;
        $data['sequence']=strval(time());
        //$data['selfApiKey']='9b341765-44e0-4c5b-819b-960b2f6a6977';
        $data['selfApiKey']=$device['DEVICE_KEY'];
        $iv = $this->generate_iv();
        $data['iv']=$iv;
        if (empty($params))
            $str_params = "{}";
        else
            $str_params = json_encode($params);
        $data['data'] = $this->encrypt($device['DEVICE_KEY'],$iv,$str_params);
    }
    else
        $data['data'] = $params;
    
    return $this->sendRequest($url, $data);
}
 
function sendRequest($url, $params = 0)
{
    try
    { 
        $data_string = json_encode($params);
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        DebMes('API request - '.$url.' => '. $data_string, 'sonoff_diy');
        
        $result = curl_exec($ch);
        DebMes('API responce - '.$url.' => '. $result, 'sonoff_diy');
        //echo $result . "\n";
        if ($result == "")
        {
            $result = array();
            $result["error"] = 1;
            $result["data"] = array();
            $result["data"]["message"] = "Empty responce result";
        }
        else
            $result = json_decode($result,true);
    }
    catch (Exception $e)
    {
        registerError('sonoff_diy', 'Error send query - '.$url.' == '.get_class($e) . ', ' . $e->getMessage());
		DebMes('API error - '.$url.' => '. get_class($e) . ', ' . $e->getMessage(), 'sonoff_diy');
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
 function checkAlive() {
    $this->getConfig();
    $table_name='sonoff_diy_devices';
    $devices=SQLSelect("SELECT * FROM $table_name");
    $total=count($devices);
    for($i=0;$i<$total;$i++) {
        $cmd = "zeroconf/info";
        $params = array();
        $res = $this->callApi($devices[$i],$cmd,$params);
        //print_r($res);
        if ($res["error"] == 1)
        {
            $data = array();
            $data['alive'] = '0';
            $data['error'] = $res["data"]["message"];
            if ($devices[$i]['DEVICE_MODE'] == 1 && $devices[$i]['DEVICE_KEY']=='')
                $data['error'] = $data['error'] . " (maybe wrong device key)";
            $this->updateData($devices[$i]['MDNS_NAME'],$data);
        }
        else
        {
            $data = array();
            $data['alive'] = '1';
            $data['error'] = '';
            $this->updateData($devices[$i]['MDNS_NAME'],$data);
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
            //echo date('Y-m-d H:i:s')." ".$inpacket->answerrrs[$x]->name . "\n";
            $name = substr($inpacket->answerrrs[$x]->name, 0, strpos($inpacket->answerrrs[$x]->name,'.'));
            //echo date('Y-m-d H:i:s')." ".$name . "\n";
			//print_r($inpacket->answerrrs[$x]);
			//DebMes($inpacket->answerrrs[$x], 'sonoff_diy');
            // PTR
			if ($inpacket->answerrrs[$x]->qtype == 12) {
                if ($inpacket->answerrrs[$x]->name == "_ewelink._tcp.local") {
					$name = "";
					for ($y = 0; $y < sizeof($inpacket->answerrrs[$x]->data); $y++) {
						$nameMDNS .= chr($inpacket->answerrrs[$x]->data[$y]);
					}
                    $name = substr($nameMDNS, 0, strpos($nameMDNS,'.'));
                    //print_r($name);
					DebMes($name . ' qtype='.$inpacket->answerrrs[$x]->qtype . " nameDns=".$nameMDNS, 'sonoff_diy');
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
				
				DebMes($name. " txt=" .json_encode($d), 'sonoff_diy');
                $this->updateDevice($name,"DEVICE_ID",$d['id']);
                $this->updateDevice($name,"UPDATED",date('Y-m-d H:i:s'));
                                
                $df = $d['data1'];
                if (array_key_exists('data2', $d)) $df = $df.$d['data2'];
                if (array_key_exists('data3', $d)) $df = $df.$d['data3'];
                
                //update data device
                if ($d["encrypt"] == "true")
                {
                    $this->updateDevice($name,"DEVICE_MODE",1);
                    $table_name='sonoff_diy_devices';
                    $device=SQLSelectOne("SELECT * FROM $table_name WHERE MDNS_NAME='$name'");
                    $data = json_decode($this->decrypt($device['DEVICE_KEY'] ,$d["iv"],$df),true);
                }
                else
                {
                    $this->updateDevice($name,"DEVICE_MODE",0);
                    $data = json_decode($df,true);
                }
                if ($d["type"] == 'strip')
				{
                    foreach ($data['switches'] as $key => $val)
					{
						$data['switch'.$val['outlet']] = $val['switch'];
					}
					foreach ($data['configure'] as $key => $val)
					{
						$data['startup'.$val['outlet']] = $val['startup'];
					}
					unset($data['switches']);
					unset($data['pulses']);
					unset($data['configure']);
				}
                $data["alive"] = 1;
				//print_r($data);
				DebMes($name. " data=" .json_encode($data), 'sonoff_diy');
                //DebMes($data, 'sonoff_diy');
                $this->updateData($name,$data);
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
                //echo "PORT ".$port." ".  $name."\n";
				DebMes($name. " port=" .$port, 'sonoff_diy');
                $this->updateDevice($name,"PORT",$port);
				// We know the name and port. Send an A query for the IP address
				$this->mdns->query($target,1,1,"");
			}
            // A
			if ($inpacket->answerrrs[$x]->qtype == 1) {
				$d = $inpacket->answerrrs[$x]->data;
				$ip = $d[0] . "." . $d[1] . "." . $d[2] . "." . $d[3];
                // update $IP device
                //echo "IP ".$ip." ".  $name."\n";
				DebMes($name. " ip=" .$ip, 'sonoff_diy');
                $this->updateDevice($name,"IP",$ip);

			}
		}
	}
  
 }
 
 function generate_iv()
 {
    $iv = random_bytes(16);
    return base64_encode($iv);
 }
            
 
 function encrypt($device_key, $iv, $data)
 {
    $key = md5($device_key, true);
    $encodedData = base64_encode(openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, base64_decode($iv)));
    return $encodedData;
        
 }
 
 function decrypt($device_key, $iv, $data)
 {
    $key = md5($device_key, true);
    $decryptedData = openssl_decrypt(base64_decode($data), 'aes-128-cbc', $key, OPENSSL_RAW_DATA, base64_decode($iv));
    return $decryptedData;
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
 sonoff_diy_devices: DEVICE_KEY varchar(100) NOT NULL DEFAULT ''
 sonoff_diy_devices: DEVICE_MODE int(10) NOT NULL DEFAULT 0
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
