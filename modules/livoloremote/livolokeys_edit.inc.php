<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='livolokeys';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($this->mode=='update') {
   $ok=1;
  //updating '<%LANG_TITLE%>' (varchar, required)
   $rec['TITLE']=gr('title');
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'KEYNUM' (varchar)
   $rec['KEYNUM']=gr('keynum');
  //updating 'REMOTEID' (varchar)
   $rec['REMOTEID']=gr('remoteid');
  //updating '<%LANG_LINKED_OBJECT%>' (varchar)
   $rec['LINKED_OBJECT']=gr('linked_object');
  //updating '<%LANG_LINKED_PROPERTY%>' (varchar)
   $rec['LINKED_PROPERTY']=gr('linked_property');
  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }

    if ($rec['LINKED_OBJECT'] && $rec['LINKED_PROPERTY']) {
        addLinkedProperty($rec['LINKED_OBJECT'],$rec['LINKED_PROPERTY'],$this->name);
    }

    $out['OK']=1;
       $this->redirect("?ok_msg=".LANG_DATA_SAVED);
   } else {
    $out['ERR']=1;
   }
  }
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
