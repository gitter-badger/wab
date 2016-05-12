<?php
/*
   * e107 website system
   *
   * Copyright (C) 2008-2010 e107 Inc (e107.org)
   * Released under the terms and conditions of the
   * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
   *
   * User information
   *
   * $URL$
   * $Id$
   *
*/
//HCL define('PAGE_NAME', 'Members');

//require_once("../../class2.php");

// Next bit is to fool PM plugin into doing things
$fp=fopen('test.txt','a+');
fwrite($fp,'ddd');
fclose($fp);
global $user;
$user['user_id'] = USERID;

//if(e_AJAX_REQUEST)
//{
//	if(vartrue($_GET['q']))
//	{
#		$q = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
#		if($sql->select("user", "user_id,user_name", "user_name LIKE '". $q."%' ORDER BY user_name LIMIT 15"))
#		{
#			while($row = $sql->db_Fetch())
#			{
#				$id = $row['user_id'];
#				$data[$id] = $row['user_name'];
#			}
#
#			if(count($data))
#			{
#				echo json_encode($data);
#			}
#		}
#//	}
	$data['1']='fred';
	$data['2']='bert';
		echo json_encode($data);
	exit;
//}

?>