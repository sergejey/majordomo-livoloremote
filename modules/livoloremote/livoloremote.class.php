<?php
/**
* LivoloRemote 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 11:08:52 [Aug 24, 2018])
*/
//
//
class livoloremote extends module {
/**
* livoloremote
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="livoloremote";
  $this->title="LivoloRemote";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
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
 $out['API_URL']=$this->config['API_URL'];

 $ok_msg=gr('ok_msg');
    if ($ok_msg) {
        $out['OK_MSG']=$ok_msg;
    }

 if ($this->view_mode=='update_settings') {
   global $api_url;
   $this->config['API_URL']=$api_url;
   $this->saveConfig();
   $this->redirect("?ok_msg=".LANG_DATA_HAS_BEEN_SAVED);
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='livolokeys' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_livolokeys') {
   $this->search_livolokeys($out);
  }
  if ($this->view_mode=='edit_livolokeys') {
   $this->edit_livolokeys($out, $this->id);
  }
  if ($this->view_mode=='delete_livolokeys') {
   $this->delete_livolokeys($this->id);
   $this->redirect("?");
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
* livolokeys search
*
* @access public
*/
 function search_livolokeys(&$out) {
  require(DIR_MODULES.$this->name.'/livolokeys_search.inc.php');
 }
/**
* livolokeys edit/add
*
* @access public
*/
 function edit_livolokeys(&$out, $id) {
  require(DIR_MODULES.$this->name.'/livolokeys_edit.inc.php');
 }
/**
* livolokeys delete record
*
* @access public
*/
 function delete_livolokeys($id) {
  $rec=SQLSelectOne("SELECT * FROM livolokeys WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM livolokeys WHERE ID='".$rec['ID']."'");
 }

 function sendBtn($id,$value) {
     $this->getConfig();
     if (!$this->config['API_URL']) {
         return 0;
     }
     $rec=SQLSelectOne("SELECT * FROM livolokeys WHERE ID=".(int)$id);
     if ($value) {
         $url='http://'.$this->config['API_URL'].'/rfkey?button='.$rec['KEYNUM'].'&remote='.$rec['REMOTEID'];
     } else {
         $url='http://'.$this->config['API_URL'].'/rfkey?button=off&remote='.$rec['REMOTEID'];
     }
     $data=getURL($url,0);
     return $data;
 }

 function propertySetHandle($object, $property, $value) {
   $properties=SQLSelect("SELECT ID FROM livolokeys WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
        echo $this->sendBtn($properties[$i]['ID'],$value);
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
  SQLExec('DROP TABLE IF EXISTS livolokeys');
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
livolokeys - 
*/
  $data = <<<EOD
 livolokeys: ID int(10) unsigned NOT NULL auto_increment
 livolokeys: TITLE varchar(100) NOT NULL DEFAULT ''
 livolokeys: KEYNUM int(3) NOT NULL DEFAULT 0
 livolokeys: REMOTEID int(10) NOT NULL DEFAULT 0
 livolokeys: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 livolokeys: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgQXVnIDI0LCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
