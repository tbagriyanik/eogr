<?php
/*
eOgr - elearning project

Developer Site: http://yunus.sourceforge.net

Support:		http://www.ohloh.net/p/eogr

This project is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 3 of the License, or any later version. See the GNU
Lesser General Public License for more details.
*/
	@session_start();
	header("Content-Type: text/html; charset=utf-8");          

	require "conf.php";	
	
	checkLoginLang(true,true,"addComment2.php");
/*
baglan2: parametresiz, 
veritaban� ba�lant�s�
*/
function baglan2()
{
	global  $_host;
	global  $_username;
	global  $_password;
    return 	@mysqli_connect($_host, $_username, $_password);
}

if(!baglan2())   
 die("<font id='hata'> L&#252;ften, 'veritaban&#305;' <a href=install.php>kurulumunu (installation)</a> yap&#305;n&#305;z!</font>");
 
$yol1 = baglan2();

	if (!$yol1)
	{
		die("<font id='hata'> 
		  Veritaban&#305; <a href=install.php>ayarlar&#305;n&#305;z&#305;</a> yapmad&#305;n&#305;z!<br/>
		  You need to go to <a href=install.php>installing page</a>!<br/>
			 </font>");
	}

/*
getUserIDcomment: kullan�c� ad� ve parola
kullan�c� ad� ve parolas� ile kimlik bilgisi elde edilir
*/
function getUserIDcomment($usernam, $passwor)
{
	global $yol1;
	
	$usernam = substr(temizle($usernam),0,15);
    $sql1 = "SELECT id, userName, userPassword FROM eo_users where userName='".temizle($usernam)."' AND userPassword='".temizle($passwor)."' limit 0,1"; 	

    $result1 = mysqli_query($yol1,$sql1); 

    if ($result1 && mysqli_num_rows($result1) == 1)
    {
       return (mysqli_result($result1, 0, "id"));
    }else {
	   return ("");
	}
}
/*
yorumGonder: kullan�c� ad�,konu no ve yorum
kullan�c� ad� ile belli bir konuya yorum eklenir
*/

function yorumGonder($userID, $konuID, $yorum){
	global $yol1;				
		
		$datem	=	date("Y-n-j H:i:s");		
		
		if(!empty($yorum) && !empty($konuID) && !empty($userID)) {
			$uyeTur = getUserType($_SESSION["usern"]);
			//�ye ��retmen veya y�netici ise onay ver
			if($uyeTur>=1)
				$sql2 = "insert into eo_comments VALUES (NULL , '$userID', '$konuID' , '$yorum', '$datem' , 1)"; 
			 else			  
				$sql2 = "insert into eo_comments VALUES (NULL , '$userID', '$konuID' , '$yorum', '$datem' , 0)"; 

			$result2 = mysqli_query($yol1,$sql2); 
			return $result2;
		 }
		
	return false;
}

$yorumGel = str_replace("'", "`", $_POST['yorum']);

if (isset($_POST['yorum']) 
				       	&& !empty($_POST['yorum']) 
						&& getUserIDcomment($_SESSION["usern"],$_SESSION["userp"])!="") {
	if (yorumGonder(getUserIDcomment($_SESSION["usern"],
			$_SESSION["userp"]), 
			temizle($_POST['konu']),RemoveXSS($yorumGel)) )
		echo $metin[293];
		else
		echo "Error!";
} else {
   echo "";
   }

?>