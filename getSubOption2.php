<?php header("Content-Type: text/html; charset=iso-8859-9"); ?>
<?php 
require("conf.php");  	
    if ( !isset( $_SESSION ['ready'] ) ) 
     { 
      session_start (); 
      $_SESSION ['ready'] = TRUE; 
     }

     $taraDili=$_COOKIE["lng"];    
   if(!($taraDili=="TR" || $taraDili=="EN")) 
    $taraDili="EN";
   dilCevir($taraDili);
   
	if (!check_source()) die ("<font id='hata'>$metin[295]</font>");	

//Bu sayfa printIt i�indir...   ********************************************************************************/
/*
anaMetniOku:
sayfa bilgisinin ana metnini getirir
*/
function anaMetniOku($konuID)
{
	global $yol1;
	global $metin;
	
	$sonuc = "";
	
	if (empty($konuID)) return "<font id='uyari'><?php echo $metin[176]?></font>";
	
	$sql1	= 	"select eo_5sayfa.id,eo_5sayfa.anaMetin as ana ,eo_5sayfa.cevap as cevap, eo_5sayfa.eklenmeTarihi as tarih,eo_users.userName as user, 
					eo_4konu.konuAdi as konuAdi,eo_4konu.konuyuKilitle as konuyuKilitle, 
					eo_4konu.oncekiKonuID as oncekiKonuID, eo_4konu.calismaHakSayisi as calismaHakSayisi, 
					eo_4konu.sinifaDahilKullaniciGorebilir as sinifaDahilKullaniciGorebilir, 
					eo_4konu.bitisTarihi as bitisTarihi, eo_4konu.sadeceKayitlilarGorebilir as skg  
					from eo_5sayfa, eo_users, eo_4konu where eo_5sayfa.konuID='$konuID' and  
					(eo_users.id=eo_5sayfa.ekleyenID) and (eo_4konu.id=eo_5sayfa.konuID) and (eo_5sayfa.cevap='')
					order by eo_5sayfa.sayfaSirasi";
					// cevap bo� ise SORU de�ildir, �yleyse ekrana listelenebilir
	$result1= 	mysql_query($sql1,$yol1);

	if($result1) {		
		
		$kayitSayisi = @mysql_numrows($result1);
		
		$sonuc = "";
		
		while ($row = mysql_fetch_array($result1, MYSQL_ASSOC)) {	
			
					$tarih			= tarihOku($row["tarih"]);
					$user			= $row["user"];
					$konuAdi		= $row["konuAdi"];
					$konuyuKilitle	= $row["konuyuKilitle"];
					$bitisTarihi	= $row["bitisTarihi"];
					$sKayitlilarG	= $row["skg"];
					$calismaHakS	= $row["calismaHakSayisi"];
					$sinifOgreK		= $row["sinifaDahilKullaniciGorebilir"];
					$oncekiKonuID	= $row["oncekiKonuID"];
					
					if($bitisTarihi!="0000-00-00")
						$gunFarki = getDayCount(date("Y-n-j"),$bitisTarihi);
						else
						$gunFarki = 1;
								
				$adi	=temizle(substr($_SESSION["usern"],0,15));
				$par	=temizle($_SESSION["userp"]);	
				$tur	=checkRealUser($adi,$par);			
				
				if($kayitSayisi>0) {
						
						if($sKayitlilarG=="1" && !in_array($tur, array("1","2","0"))) 
							return "<font id='hata'>'$konuAdi' ".$metin[181]."<br/><a href='newUser.php'>$metin[3]!</a></font><hr noshade='noshade'/>";
							
						if($sKayitlilarG=="1" && in_array($tur, array("1","2","0"))) //login olmu�, hak say�s�na bak
						  {
							if (kullaniciHakSayisi($konuID, $adi, $par)>= $calismaHakS &&  $calismaHakS>0) 
							return "<font id='hata'>'$konuAdi', ".$metin[208]."</font><hr noshade='noshade'/>";
						  }
				
						if($sKayitlilarG=="1" && $tur=="0") //login olmu�, &ouml;�renci s�n�fa dahil mi?
						  {
							if (ogrenciSinifaDahil($adi, $par, $konuID)==0 &&  $sinifOgreK==1) return "<font id='hata'>'$konuAdi', ".$metin[214]."</font>";
						  }
				
						if($konuyuKilitle=="1") 
							return "<font id='hata'>'$konuAdi' ".$metin[179]."</font><hr noshade='noshade'/>";
							
						if($gunFarki <= 0) 
							return "<font id='hata'>'$konuAdi' ".$metin[180]."</font><hr noshade='noshade'/>";
							
						$sonuc .= "<font size='-1' style='font-style:italic;'>$user $konuAdi $tarih</font><br/>";
						$sonuc .= html_entity_decode($row["ana"])."<hr noshade='noshade'/>";
									
						}
					else
					return "<font id='hata'>".$metin[182]."</font><hr noshade='noshade'/>";
					
		} //while
					return $sonuc;
				 }
				else	
					return "<font id='hata'>".$metin[183]."</font>";
 
 return "<font id='hata'>".$metin[184]."</font>";
}
	

if (isset($_GET['konuID'])){
	  if(!empty($_GET['konuID'])){//t&uuml;m metinler gelsin
			echo anaMetniOku(temizle($_GET['konuID']));
	  }
   }else
   echo "";

?>