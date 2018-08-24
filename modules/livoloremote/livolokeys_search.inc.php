<?php
/*
* @version 0.1 (wizard)
*/
global $session;

$sendon = gr('sendon', 'int');
if ($sendon) {
    $result=$this->sendBtn($sendon,1);
    $result=$this->redirect("?ok_msg=".urlencode($result));
}
$sendoff = gr('sendoff', 'int');
if ($sendoff) {
    $result=$this->sendBtn($sendoff,0);
    $this->redirect("?ok_msg=".urlencode($result));
}

if ($this->owner->name == 'panel') {
    $out['CONTROLPANEL'] = 1;
}
$qry = "1";
// search filters
// QUERY READY
global $save_qry;
if ($save_qry) {
    $qry = $session->data['livolokeys_qry'];
} else {
    $session->data['livolokeys_qry'] = $qry;
}
if (!$qry) $qry = "1";
$sortby_livolokeys = "TITLE";
$out['SORTBY'] = $sortby_livolokeys;
// SEARCH RESULTS
$res = SQLSelect("SELECT * FROM livolokeys WHERE $qry ORDER BY " . $sortby_livolokeys);
if ($res[0]['ID']) {
    //paging($res, 100, $out); // search result paging
    $total = count($res);
    for ($i = 0; $i < $total; $i++) {
        // some action for every record if required
    }
    $out['RESULT'] = $res;
}
