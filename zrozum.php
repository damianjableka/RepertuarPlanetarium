<?PHP session_start();
// header("Pragma: no-cache");
 //     header("cache-Control: no-cache, must-revalidate"); // HTTP/1.1
 //     header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	  
   	if(isset($_SESSION['czas'])&&(time()-$_SESSION['czas']>900))
	    {
		  session_destroy();
	if(isset($_SESSION['login']))
		unset($_SESSION['login']);
	if(isset($_SESSION['user']))
		unset($_SESSION['user']);
	if(isset($_SESSION['czas']))
		unset($_SESSION['czas']);
	if(isset($_SESSION['nazwa_dnia']))
		unset($_SESSION['nazwa_dnia']);
	if(isset($_SESSION['nieczynne']))
		unset($_SESSION['nieczynne']);
	if(isset($_SESSION['rep']))
		unset($_SESSION['rep']);
		
	if(isset($_SESSION['ID']))
		unset($_SESSION['ID']);
		if(isset($_SESSION['zapis']))
		unset($_SESSION['zapis']);

		}
		

   
   ?>
<html>
<head>
 <META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=UTF-8">
 <meta http-equiv="Content-Language" content="pl">
 <META HTTP-EQUIV="EXPIRES" CONTENT=0>
  <body>

<?PHP
 function assoc_1w_js_na_php($nazwa_pliku)
  {
   $plik=fopen($nazwa_pliku,"r");
   $dlugosc=filesize($nazwa_pliku)-3;
   $zawartosc=fread($plik,$dlugosc);
   fclose($plik);
   $tbl_zawartosci=explode("={\"",$zawartosc);
   $tbl_dane=explode("\",\"",$tbl_zawartosci[1]);
   $a = array();
   for($i=0;$i<count($tbl_dane);$i++)
    {
	 $tbl_dana=explode("\": \"",$tbl_dane[$i]);
	 $a[$tbl_dana[0]]=$tbl_dana[1];
	}
	return($a);
  }
  
  function assoc_1w_php_na_js($nazwa_pliku,$tablica)
  {
    $plik=fopen($nazwa_pliku,"r");
   $dlugosc=filesize($nazwa_pliku)-3;
   $zawartosc=fread($plik,$dlugosc);
   fclose($plik);
   $tbl_zawartosci=explode("={\"",$zawartosc);
   $wsad=$tbl_zawartosci[0]."={";
   
   $klucze=array_keys($tablica);
  // var_dump($klucze);
   
   for($i=0;$i<count($klucze);$i++)
    {
     $wsad.="\"".$klucze[$i]."\": \"".$tablica[$klucze[$i]]."\"";
	 if($i<count($klucze)-1)
	  $wsad.=",";
    }
    $wsad.="};";
	//var_dump($wsad);
	$plik=fopen($nazwa_pliku,"w");
	fputs($plik,$wsad);
	fclose($plik);
  }

$e=1;
include 'tablica_fcje.php';
include 'pas2.php';
//logowanie i otwarcie sesji
$zalogowano=0;

   //ob_start();


if(isset($_POST['log'])&&!empty($_POST['login'])&&!empty($_POST['haslo']))
{
 if(array_key_exists(md5($_POST['login']),$hasla))
 {
  if($hasla[md5($_POST['login'])]==md5($_POST['haslo']))
   {
	$zalogowano=1;
	$_SESSION['login']=1;
	$_SESSION['user']=$_POST['login'];
	$_SESSION['ID']=md5($_POST['login']);
	$_SESSION['czas']=time();
   }
   else
   {
   printf("<center>Błędne dane logowania</center>");
   }
  }
  else
   {
    printf("<center>nieprawidłowy login</center>");
   }
 
}

if(isset($_POST['logout'])&&!empty($_POST['logout']))
{
  session_destroy();
	if(isset($_SESSION['login']))
		unset($_SESSION['login']);
	if(isset($_SESSION['user']))
		unset($_SESSION['user']);
	if(isset($_SESSION['czas']))
		unset($_SESSION['czas']);
	if(isset($_SESSION['nazwa_dnia']))
		unset($_SESSION['nazwa_dnia']);
	if(isset($_SESSION['nieczynne']))
		unset($_SESSION['nieczynne']);
	if(isset($_SESSION['rep']))
		unset($_SESSION['rep']);
		if(isset($_SESSION['ID']))
		unset($_SESSION['ID']);
	
	$zalogowany=0;
}

if(isset($_SESSION['login'])&&(time()-$_SESSION['czas'])<900&&array_key_exists($_SESSION['ID'],$hasla))
{
 $_SESSION['czas']=time();
 $zalogowano=1;
if(isset($_POST['zawartosc'])&& !empty($_POST['zawartosc']))
{
$opisy=tlumacz_tab_js_php("opisy.js");

$rep=tlumacz_tab_js_php("rep.js");

$tbl_niecz=tlumacz_tab_js_php_1w("nieczynne.js");


$tbl_nazwa_dnia=assoc_1w_js_na_php("nazwa_dnia.js");

 //$backup_plik=fopen("backup/opisy_".time()."_".$_SESSION['login'].".js","w");
 
//tu chyba zrobimy backup najbezpieczniej bo juz  tak cos przelaliśmy i jak sie spierdzieli to zawsze bedzie jakiś back, a jak sie zrobi kilka razy to komu to szkodzi?
  if(!is_dir("backup"))
     mkdir("backup",0744);
	 
	 copy("opisy.js", "backup/opisy_".time()."_".$_SESSION['login'].".js");
	 copy("rep.js", "backup/rep_".time()."_".$_SESSION['login'].".js");
	 copy("nieczynne.js", "backup/nieczynne_".time()."_".$_SESSION['login'].".js");
	 copy("nazwa_dnia.js", "backup/nazwa_dnia_".time()."_".$_SESSION['login'].".js");
   printf("wykonano backup plików<br>");
$tablica=explode("\r\n",$_POST['zawartosc']);
$rok_t=explode(" ",$tablica[1]);
$rok=$rok_t[1];
$data="";
$napis="";
$bledy=0;
foreach($tablica as $element=> $zawartosc)
{

 $tmp=explode(" ",$zawartosc);
 if(preg_match("/([\d][\d].[\d][\d])/",$tmp[0])) 
   {
    $data_a=explode(" ",str_replace(" 0"," ",str_replace("."," ",$tmp[0])));
	$data=$rok." ".$data_a[1]." ".$data_a[0];
	if(count($tmp)<2||!preg_match("[^godz]",$tmp[2]))
	{//tutaj rozpoczyna sie opis dnia albo napisane ze nieczynne
	 $napis.=implode(" ",array_slice($tmp,2));
	 $i=$element+1;
	 do{
	 $e=0;
	 $tmpa=explode(" ",$tablica[$i]);
	 if(!preg_match("[^godz]",$tmpa[0])&&(count($tmpa)<2||!preg_match("[^godz]",$tmpa[2]))&&!preg_match("/([\d][\d].[\d][\d])/",$tmpa[0]))
	  {
	   $napis.="<br>".implode(" ",$tmpa);
	   $i++;
	   $e=1;
	  }
	  if(!isset($tablica[$i]))
	     $e=0;
		 
	 }while($e);
	 	if(strpos($napis,"NIECZYNNE")===false)
	    {
	    
	    $tbl_nazwa_dnia[str_replace(" 0"," ",$data)]=$napis;
		
		printf("<br>".$rok." ".$data." ".$napis."<br>");

		}
	  else
	  {
	   if(strlen($napis)>9)
	   {
  	   $tbl_nazwa_dnia[str_replace(" 0"," ",$data)]=$napis;
	   $wpis=$data." 12 00";
	   $ile_niecz=count($tbl_niecz);
	   $tbl_niecz[$ile_niecz]=$wpis;
	   printf("<br>".$rok." ".$data." NIECZYNNE ".$napis."<br>");
       }
	   else
	   {
	    $wpis=$data." 12 00";
	   $ile_niecz=count($tbl_niecz);
	   $tbl_niecz[$ile_niecz]=$wpis;
	   printf("<br>".$rok." ".$data." NIECZYNNE <br>");
	   }
	  }
	 //sprawdź czy nieczynne czy czynne i dodaj do odpowieniej tablicy
$napis="";
	}
   }
   

    
   $poz=0;
   
   if(count($tmp)>2&&preg_match("[^godz]",$tmp[2]) )
	 {
	   $poz=3;	 
   }
   else
   {
    if(preg_match("[^godz]",$tmp[0]))
	{
	 $poz=1;
	}
	}
	
	if($poz)
	 {
	 
	 $tytul=implode(" ",array_slice($tmp,$poz+1));
 	 if(strlen($tmp[$poz])<3)
	 {
	 $wpis=$data." ".$tmp[$poz]." 00";
	 }
	 else
	  $wpis=$data." ".str_replace(":"," ",$tmp[$poz]);
	 
	 $myslniki=array(chr(151),chr(150),"–","-","seans na żywo","seans dla dzieci","seans nie tylko dla dzieci","?","&#63;","(",")","&#40;","&#41;");
	 $nic=array("","","","","","","","","","","","","");
	  //preg_replace('/(?<!\?)\?(?!\?)/', '',
	$i=0;

	while($i<count($opisy)&&strcasecmp(trim(str_ireplace($myslniki,$nic,trim($opisy[$i][0]))),trim(str_ireplace($myslniki,$nic,trim($tytul))))!=0)
	 {
	// printf("<br>to z    : ".trim(str_ireplace($myslniki,$nic,trim($tytul)))."<br> z tym p: ".trim(str_ireplace($myslniki,$nic,trim($opisy[$i][0])))."<br>");
	 $i++;
	 }
	 if($i==count($opisy))
	  {//nowy seans
	   	printf("<br> <font color=#ff0000> nie znalazlem tego seansu:<br> ".$data." ".$rok." ".trim(str_ireplace($myslniki,$nic,trim($tytul)))."<br> konieczne bedzie dodanie go ręcznie w <a href='edit.php'>edit</a></font><br>");
	    $bledy++;
	  }
	  else
	  {
	   printf("<br>".$wpis." ".trim(str_ireplace($myslniki,$nic,trim($opisy[$i][0])))."<br>");
	   if(!(array_search($wpis,$rep[$i])===FALSE))
	    {
		 printf("<br><font color=#ff0000>identyczny wpis już istnieje</font></br>");
		 $bledy++;
		}
	   array_unshift($rep[$i],$wpis);
	  }
	 }
	
  echo '<hr>';
}

$_SESSION['nazwa_dnia']=$tbl_nazwa_dnia;
$_SESSION['nieczynne']=$tbl_niecz;
$_SESSION['rep']=$rep;
$_SESSION['zapis']=1;
if(!$bledy)
{
printf("<center><hr>nie znaleziono błędów, jeśli sposób dekodowania przedstawiony powyżej jest prawidłowy kliknij zapisz");
}
else
{
printf("<center><hr>zdekodowano w sposób przedstawiony powyżej<br><font color=#ff0000>znaleziono ".$bledy." blędów</font> <br> klikniecie zapisz w przypadku istnienia niedopasowanych tytułów lub duplikatów<br> bedzie wymagało ingerencji w <a href='edit.php'>edit</a><br>naprawienie wszytkiego może być skomplikowane<br>zancznie lepiej dodać tam nowe seanse przed przetworzeniem zawartosci pdf-a<br>lub poprawić zawartość wklejonych danych<br>");
}
printf(" <form name='proceed' method='POST' action='zrozum.php' enctype='multipart/form-data'>");
printf("	<input type='submit' value='zapisz' name='save'></form></center>");
// zatrzymanie  wypisanie i pytanie o potwierdzenie musi być otwarta sesia
}
 
if(isset($_POST['save'])=="zapisz"&&isset($_SESSION['zapis'])&&$_SESSION['zapis']==1)
  { 
  
  $tbl_nazwa_dnia=$_SESSION['nazwa_dnia'];
     $tbl_niecz=$_SESSION['nieczynne'];
	 $rep=$_SESSION['rep'];
	 $_SESSION['zapis']=0;
	 
	 $aktualnie=time();
	 
	 //czyszczenie starych rep
	 $dl_g=(count($rep));
	 for($g=0;$g<$dl_g;$g++)
	  {
	   $dl_h=(count($rep[$g]));
	  for($h=0;$h<$dl_h;$h++)
	   if($rep[$g][$h]!="")
	   {
	     $tbl_d=explode(' ',$rep[$g][$h]);
		 $tabelowy=mktime(intval($tbl_d[3]),intval($tbl_d[4]),0,intval($tbl_d[1]),intval($tbl_d[2]),intval($tbl_d[0]));
	     if(($tabelowy+60*86400)<$aktualnie)
		  {
		  unset($rep[$g][$h]);
		  }
	   }
	   }
	   //czyszczenie starych nieczynnych
	   $dl_g=(count($tbl_niecz));
	 for($g=0;$g<$dl_g;$g++)
	   if($tbl_niecz[$g]!="")
	   {
	     $tbl_d=explode(' ',$tbl_niecz[$g]);
		 $tabelowy=mktime(intval($tbl_d[3]),intval($tbl_d[4]),0,intval($tbl_d[1]),intval($tbl_d[2]),intval($tbl_d[0]));
	     if(($tabelowy+60*86400)<$aktualnie)
		  {
		  unset($tbl_niecz[$g]);
		  }
	   }
	 //czyszczenie starych nazw dnia
  
	   
	   foreach($tbl_nazwa_dnia as $dzien=>$nazwa)
	    {
		  $tbl_d=explode(' ',$dzien);
		  $tabelowy=mktime(0,0,0,intval($tbl_d[1]),intval($tbl_d[2]),intval($tbl_d[0]));
		  if(($tabelowy+60*86400)<$aktualnie)
		  {
		  unset($tbl_nazwa_dnia[$dzien]);
		  }
		}
	   //koniec czyszczenia zaprzeszlych
	   
	 //jeszcze te trzy tablize trzeba tu posortować tak zaby edit sobie radził z miesiacami niepololei dodawanymi a i bedzie to wszybciej działać w wyświetlaniu jak bedzie posortowane
	 foreach($rep as $seans=>$wyswietlanie)
	  {
	   rsort($rep[$seans], SORT_NATURAL);
	  }
	  
	  rsort($tbl_niecz, SORT_NATURAL);
	  
	 
   assoc_1w_php_na_js("nazwa_dnia.js",$tbl_nazwa_dnia);
      
   $niecz_zp=fopen("nieczynne.js","w");
   $wsad_niecz="nieczynne =new Array(\"".implode("\",\"",$tbl_niecz)."\");";
   fputs($niecz_zp,$wsad_niecz);
   fclose($niecz_zp);


   $wsad_pliku_js=tlumacz_tab_php_js_2($rep,nazwa_tab_js("rep.js"));
   $nowy_plik_js=fopen("rep.js","w");
   fputs($nowy_plik_js,$wsad_pliku_js); //zapis tablicy
   //zamkniecie plików zapicie sesji
   fclose($nowy_plik_js);
   
    if(isset($_SESSION['nazwa_dnia']))
		unset($_SESSION['nazwa_dnia']);
	if(isset($_SESSION['nieczynne']))
		unset($_SESSION['nieczynne']);
	if(isset($_SESSION['rep']))
		unset($_SESSION['rep']);
  printf("<br>zapisano<br> możesz przystapić do kolejnego dekodowania<br>"); 
  }

 }
else{//printf("");
if(!$zalogowano){
printf("<table border=0 align=center> <tr><td align=center>");
    printf("<form name='login' method='POST' action='zrozum.php' enctype='multipart/form-data'>");
	printf("login:</td></tr><tr><td align=center>");
    printf("<input type='text' length=20 name='login'><br>");
	printf("</td></tr><tr><td align=center>hasło:</td></tr><tr><td>");
	printf("<input type='password' length=20 name='haslo'><br>");
   printf("</td></tr><tr><td align=center>");
	printf("<input type='submit' value='logowanie' name='log'></form>");
		printf("</td></tr></table>");
		}
}
  ?>
<?PHP
 if(isset($zalogowano)&&$zalogowano)
  {
  //wyloguj...
  printf(" <form name='logout' method='POST' action='zrozum.php' enctype='multipart/form-data'>");
printf("	<input type='submit' value='wyloguj' name='logout'></form>");

  printf(" <form name='zawartosc' method='POST' action='zrozum.php' enctype='multipart/form-data'>");
  printf("  <textarea name='zawartosc' cols=120 rows=30></textarea><br>");
printf("	<input type='submit' value='zrozum' name='zaw'>");
 printf("  </form> ");
   }
   
 
   ?>
   
 </body>
</html>
