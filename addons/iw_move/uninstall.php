<?php 

global $_W;

$sql ="

drop table if exists " . tablename('ims_move_activity') . " ;
drop table if exists " . tablename('ims_move_backup') . " ;
drop table if exists " . tablename('ims_move_boss') . " ;
drop table if exists " . tablename('ims_move_broadband') . " ;
drop table if exists " . tablename('ims_move_control') . " ;
drop table if exists " . tablename('ims_move_customer') . " ;
drop table if exists " . tablename('ims_move_declare') . " ;
drop table if exists " . tablename('ims_move_fixed') . " ;
drop table if exists " . tablename('ims_move_interact') . " ;
drop table if exists " . tablename('ims_move_nearby') . " ;
drop table if exists " . tablename('ims_move_photo') . " ;
drop table if exists " . tablename('ims_move_question') . " ;
drop table if exists " . tablename('ims_move_recommend') . "; 
drop table if exists " . tablename('ims_move_record') . " ;
drop table if exists " . tablename('ims_move_staff') . " ;
drop table if exists " . tablename('ims_move_team') . " ;
drop table if exists " . tablename('ims_move_test') . " ;
drop table if exists " . tablename('ims_move_token') . " ;
drop table if exists " . tablename('ims_move_type') . " ;
";

pdo_query($sql);



 ?>