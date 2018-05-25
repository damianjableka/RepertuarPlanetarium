<?PHP
//zapis zmian
//20:21 19.12.2012 dziala dodawanie dni i przgladanie calego w js
// usuniete niekatywne domyslnie poniedzialki
// wielki problem jak zrobic nieczynny dzien najlepiej poprostu dodac pusty dzien trzeba usunac cos z php co uniemozliwia zapis pustego dnia
// albo dodatkowa baza z dniami  nieczynnymi ktora spowoduje ze nieczynne sie beda wyswietlac w bazie jakos
//12:34 09.01.2013 dodano zapis i odczyt do pliku neczynne.js
// dodawanie nieczynnego dnia dziala na zasadie dodania seansu -1 do senansow wg daty koniecznie trzeba to zmienic w repertuar7.html
//16:40 09.01.2013 dodalem dodoaj seans do js
//10:53 10.01.2013 dodano zapisywanie nowego seansu w bazie seansow
//10:53 10.01.2013 dodano dodawanie pustej tablicy na koncu rep.js dla nowego seansu
//13:26 10.01.2013 dodano edycje dowolnego seansu do kodu js
//13:52 10.01.2013 dziala edycja seansu w php 
//13:40 11.01.2013 dziala kasowanie calego seansu i z opisy i z rep
//22:56 23.01.2013 dziala obsluga nazwy dnia
//10:25 27.02.2013 dodalem obejscie umozliwajace dodanie seansu na bierzacy dzien mimo iz kilka poprzednich nie jest wypelnionych

//todo import z pliku do bazy
//todo przenoszenie baz do archiwóm aby nie bylu za długie
// czasem chyba nie mozna dodac nowego pustego dnia
//problem aktualna data jest nowsza niz najnowszy dodany dzien wtedy nie mozna nadrobic braku dodac tego w przeszlosci

  header("Pragma: no-cache");
      header("cache-Control: no-cache, must-revalidate"); // HTTP/1.1
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	//ini_set('display_errors', 1);
  function zero($x)
  {
   return((($x<10)?("0".$x):($x)));
  }
  
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
  
  function spr_form($x)
  {
   if(!trim($x)) //sprawdza czy po obciecu x z bialych znakow zosalo cos z niego
   {
    return(0);
   }
   else
   {
    $szukaj=Array(";"," : ",".","-"); //separatory godzin od minut
	$str=str_replace($szukaj,':',$x); //zamienia wszystkie powyzsze na :
    $godz_min=explode(",",trim($str)); //rozwija ten ciag znakow obicty z bialych znakow po przecinkach
	$spr=0;
    for($i=0;$i<count($godz_min);$i++)//for po wszystkich godzinach z minutami
	{
	 $gm=explode(":",trim($godz_min[$i]));//pakuje do tablicy $gm aktualna godizne do 0 i minute do 1
	  if(is_numeric($gm[0])&&is_numeric($gm[1])&&($gm[0]<24)&&($gm[0]>=0)&&($gm[1]<60)&&($gm[1]>=0))
	   $spr++;
	 
	}
    if($spr==count($godz_min))
	{
	    return(1);
	}
	else
	{
	   return($x);
    }   
   }
 
  
  }
  
  
  
  
  
session_start(); 
  $e=1;
  include 'pas2.php';
  $srodek=-1;
  if(isset($_GET['s']))
   $srodek=$_GET['s'];
   
   
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
		}
   
   
   
 if(isset($_POST['logowanie'])&&$_POST['logowanie']=="wyloguj")
  {
   $_SESSION = array();
   if (isset($_COOKIE[session_name()])) { 
     setcookie(session_name(), '', time()-42000, '/'); 
   }
   session_destroy();
  }
 
  //$skl = $_POST['login'].':'.$_POST['haslo'];

  
 /*  if($skl != ':') 
   {
    if(strpos(';'.$hasla.';', ';'.$skl.';') !== FALSE) 
    {
       $_SESSION['login'] = $_POST['login'];
       $_SESSION['skl'] = $skl;
       $czy_zalogo="tak";
    }
    else
    {
     
    }
   }
   else
   {
     $czy_zalogo="nie";
   } 
	  
*/
   
   $czy_zalogo="nie";
   
if(isset($_POST['logowanie'])&&!empty($_POST['login'])&&!empty($_POST['haslo']))
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
    $czy_zalogo="zle";
   }
  }
  else
   {
    $czy_zalogo="nie";
   }
 
}

  include 'tablica_fcje.php';
  $czy_zapis=0;
 
if(isset($_SESSION['login'])&&(time()-$_SESSION['czas'])<900&&array_key_exists($_SESSION['ID'],$hasla)) 
{
  $czy_zalogo="tak"; 
$_SESSION['czas']=time();
  

if((isset($_POST['nowy_seans'])&&$_POST['nowy_seans']=="zapisz")||(isset($_POST['skasuj_seans'])&&$_POST['skasuj_seans']=="skasuj_seans"))//dodaje seans
{
    //backup opisow
	$oldt=fopen("opisy.js","r");
    $dl=filesize("opisy.js");
    $zaw=fread($oldt,$dl);
    if(!is_dir("backup"))
     mkdir("backup",0777);
    $backup_plik=fopen("backup/opisy_".time()."_".$_SESSION['user'].".js","w");
    fputs($backup_plik,$zaw);
    fclose($oldt);
    fclose($backup_plik);
		//koniec backupa opisow

	$opisy_t=tlumacz_tab_js_php("opisy.js");

	    $ile=count($opisy_t);
		$co=array("\"","(",")","'","<","=",">","?","!","\r\n", "\n", "\r");
		$naco=array("&#34;","&#40;","&#41;","&#39;","&#60;","&#61;","&#62;","&#63;","&#33;","<br>","<br>","<br>");
		$nazwa_n=str_ireplace($co,$naco,$_POST['ns_nazwa']);
		$opis_n=str_ireplace($co,$naco,$_POST['ns_opis']);
		
		
	if($_POST['ktory_seans']==-1) //jesli dodajemy nowy seans
    { 	
	$opisy_t[($ile-1)]=array($nazwa_n,$opis_n,$_POST['ns_typ'],$_POST['ns_czas']);
	$wsad_pliku_js=tlumacz_tab_php_js($opisy_t,nazwa_tab_js("opisy.js"));
	}
	else
	{
	 if($_POST['skasuj_seans']=="skasuj_seans")
	  {
	   unset($opisy_t[$_POST['ktory_seans']]);
       $opisy_t = array_values($opisy_t);
	   $wsad_pliku_js=tlumacz_tab_php_js_2($opisy_t,nazwa_tab_js("opisy.js"));

	   
	   $rep_k=tlumacz_tab_js_php("rep.js");
	   unset($rep_k[$_POST['ktory_seans']]);
       $rep_k = array_values($rep_k);
	   
	   $zaw=tlumacz_tab_php_js_2($rep_k,nazwa_tab_js("rep.js"));
	   $nowy_plik_js=fopen("rep.js","w");
       fputs($nowy_plik_js,$zaw); //zapis tablicy
	   fclose($nowy_plik_js);
	   
	  }
	  else
	  {
	  $opisy_t[$_POST['ktory_seans']]=array($nazwa_n,$opis_n,$_POST['ns_typ'],$_POST['ns_czas']);
	  $wsad_pliku_js=tlumacz_tab_php_js_2($opisy_t,nazwa_tab_js("opisy.js"));
	  }
	}
	//var_dump($opisy_t);


   
   $nowy_plik_js=fopen("opisy.js","w");
   fputs($nowy_plik_js,$wsad_pliku_js); //zapis tablicy
   fclose($nowy_plik_js);
   
   if($_POST['ktory_seans']==-1)// jesli dodajemu nowy seans to zawsze na koncu, przy edycji opisu niepotrzebne, przy usuwaniu seansu musi byc konkretna czesc skasowana
    { 	
   	$oldt=fopen("rep.js","r");
    $dl=filesize("rep.js");
    $zaw=fread($oldt,$dl-2);
	$zaw=$zaw.",Array(\"\"));";
	fclose($oldt);
	
	$nowy_plik_js=fopen("rep.js","w");
    fputs($nowy_plik_js,$zaw); //zapis tablicy
	fclose($nowy_plik_js);
	}
}

if(isset($_POST['klik'])||isset($_POST['klik_niecz'])||isset($_POST['nieczynne']))//nazwa dnia
 {
  $tbl_nazwa_dnia=assoc_1w_js_na_php("nazwa_dnia.js"); //tlumaczymy tablice z js na php asocjacyjne obie
  //var_dump($tbl_nazwa_dnia);
   if($_POST['nazwa_dnia']!="")
   {
   $co=array("\"","(",")","'","<","=",">","?","!","\r\n", "\n", "\r");
	$naco=array("&#34;","&#40;","&#41;","&#39;","&#60;","&#61;","&#62;","&#63;","&#33;","<br>","<br>","<br>");
    $tbl_nazwa_dnia[$_POST['kolumna']]=str_ireplace($co,$naco,$_POST['nazwa_dnia']);
   }
   else
    {
	 if(isset($tbl_nazwa_dnia[$_POST['kolumna']]))
	 {
	  unset($tbl_nazwa_dnia[$_POST['kolumna']]);
	 }
	}
	//printf("kolumna: ".$_POST['kolumna']." <br>");
	//var_dump($tbl_nazwa_dnia);
	
	assoc_1w_php_na_js("nazwa_dnia.js",$tbl_nazwa_dnia);
  //najpierw sprawdzamy co jest w okienku nazwa_dnia jesli puste i jestli nie istnieje tablica_nazw_dnia od kolumny to nic nie robimy
  //else jesli nazwa_dnia jest niepusta to tablica_nazw_dnia od kolumny = nazwa dnia
  //else jesli tablica_nazw_dnia od kolumny istnieje to unset( tablica_nazw_dnia od kolumny)
  //php nadpisuje jesli klucz jest rowny jakiemus juz interesujacemu to nadpisze wrtosc.
 }


if(isset($_POST['klik'])&&$_POST['klik']=="zapisz")
 {
  $czy_zapis=1;
  $srodek=$_POST['srodek'];
   //zapis backupa bazy jako .backup/rep_unix_time_login.js
    $oldt=fopen("rep.js","r");
    $dl=filesize("rep.js");
    $zaw=fread($oldt,$dl);
    if(!is_dir("backup"))
     mkdir("backup",0777);
    $backup_plik=fopen("backup/rep_".time()."_".$_SESSION['user'].".js","w");
    fputs($backup_plik,$zaw);
    fclose($oldt);
    fclose($backup_plik);
  //koniec robienia backupa
      $baza=tlumacz_tab_js_php("rep.js");
  
   for($i=0;$i<count($baza);$i++) //for po wszystkich seansach
   {
    $tbl_data=explode(" ",$_POST['kolumna']);//zrozumienie kolumny dzien miesac rok
	// !!!!!!!!!!!!!!!!
    if(isset($_POST[$i])&&spr_form($_POST[$i])==1) //sprawdza czy poslana zostala zmiana do danego seansu sprawdza tylko czy jest to cos poza bialymi znakami trzba dorobic tutaj sprawdzenie czt to jest poprawny format godziny
	{
	//zrozum godziny 
	$przyciety=trim($_POST[$i]); //przycina biale znaki z konca i poczatku
	$szukaj=Array(";",".","-"); //separatory godzin od minut
	$str=str_replace($szukaj,':',$przyciety);
	$tbl_godz=explode(",",$str);//w tablicy sa teraz godziny z minutami

 	 $g=0 ;

	for($l=0;$l<count($baza[$i]);$l++)//for po wszystkich wpisach dla danego seansu
	{
	 $tbl_data_baza[$i][$l]=explode(" ",$baza[$i][$l]);//tablica kolejnych dat po l 
		
	  //przepisuje wszystkie dni inne niz edytowany
	 if(!($tbl_data_baza[$i][$l][0]==$tbl_data[0]&&$tbl_data_baza[$i][$l][1]==$tbl_data[1]&&$tbl_data_baza[$i][$l][2]==$tbl_data[2])) //porownuje date w senasie z data edyowanej kolumny
	 {
	   $baza_n[$i][$g]=$baza[$i][$l];
	  $g++;
	 }
	}
	
	
	//wpisanie do tbl_baza wszystkich godzin z danego dnia na koniec //wpisanie do bazy wartosci z formulaza
    for($m=0;$m<count($tbl_godz);$m++)//for po ilosci podanych godzin w danej dacie dla danego seansu //m godzin z minutami
	 {
	  $tbl_godz_p[$m]=trim($tbl_godz[$m]); //obciecie bialych znakow
	  $tbl_godz_pe[$m]=explode(":",$tbl_godz_p[$m]); //rozdzielenie godzin od minut
	 //wpisanie do tablicy roku miesaca dnia godziny minuty
 	  $baza_n[$i][$g]=$tbl_data[0]." ".zero($tbl_data[1])." ".zero($tbl_data[2])." ".zero($tbl_godz_pe[$m][0])." ".$tbl_godz_pe[$m][1];//wpisanie do bazy kolejnego wpisy z forularza
	   $g++;
	 }
	} //koniec if(post[i])
	else
	{
	$g=0;
		
	for($l=0;$l<count($baza[$i]);$l++)//for po wszystkich wpisach dla danego seansu
	{
     
	 $tbl_data_baza[$i][$l]=explode(" ",$baza[$i][$l]);
	//przepisuje wszystkie wpisy z inna data	
	if(!($tbl_data_baza[$i][$l][0]==$tbl_data[0]&&$tbl_data_baza[$i][$l][1]==$tbl_data[1]&&$tbl_data_baza[$i][$l][2]==$tbl_data[2])) //porownuje date w senasie z data edyowanej kolumny
	  {
	  $baza_n[$i][$g]=$baza[$i][$l];
	   $g++;
	  }
	 }
	} //koniec else
	 rsort($baza_n[$i]);
	}//koniec for(seans)
   //zapis bazy do pliku js
   
   $wsad_pliku_js=tlumacz_tab_php_js_2($baza_n,nazwa_tab_js("rep.js"));
   $nowy_plik_js=fopen("rep.js","w");
   fputs($nowy_plik_js,$wsad_pliku_js); //zapis tablicy
   
 }//koniec if(zapisz)
 // vv  to musi przeszukac sprawdzic i dopisac albo skasowac jesli znajdzie/nieznajdzie
 if(isset($_POST['nieczynne']))
 {
  $tbl_niecz=tlumacz_tab_js_php_1w('nieczynne.js');
  $ile_niecz=count($tbl_niecz);
  
   printf("<br>");
  if($_POST['nieczynne']=="nieczynne")
  {
   $tbl_niecz[$ile_niecz]=$_POST['kolumna']." 12 00";
  }
 
  if($_POST['nieczynne']=="czynne")
  {
    $tbl_niecz=array_unique($tbl_niecz); // to usuwa wielokrotne powtorzenia tej samej wartosci
   
    $pol=array_search($_POST['kolumna']." 12 00",$tbl_niecz);
	unset($tbl_niecz[$pol]);
    $tbl_niecz = array_values($tbl_niecz);
   	
  }
  //zapisik do js
  $niecz_zp=fopen("nieczynne.js","w");
  $wsad_niecz="nieczynne =new Array(\"".implode("\",\"",$tbl_niecz)."\");";
  fputs($niecz_zp,$wsad_niecz);
  fclose($niecz_zp);
 }
}
?>
<html>

<head>
 <META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=UTF-8">
 <meta http-equiv="Content-Language" content="pl">
 <META HTTP-EQUIV="EXPIRES" CONTENT=0>
 
 <script type="text/javascript" SRC="rep.js?u=<?PHP printf(time());?>"> 
 </script>

 <script type="text/javascript" SRC="opisy.js?ul=<?PHP printf(time());?>">
 </script>

<script type="text/javascript" SRC="nieczynne.js?ul=<?PHP printf(time());?>">
 </script>
 
 <script type="text/javascript" SRC="nazwa_dnia.js?ul=<?PHP printf(time());?>">
 </script>
 
<script>
//kolory
/*kc="10a3a5"
kcj="12c6c8"
kj="17d3d5"
kjj="1dfcff"
*/

kc="c0c0c0";
kcj="b8b8b8";
kj="b0b0b0";
kjj="a8a8a8";
kolor_nieczynny="bb3333";
srodek=<?PHP printf($srodek);?>;

function wczesniej(k,pm)
 {
 
  srodek+=pm*86400000;
  //07:36 27.02.2013
   if(k==-3)
    {
   	var du=rep_wg_daty.length
	var u=rep_wg_daty[du-1][0]+pm*86400000;
//	alert("u wczesniej\n"+u);
	    rep_wg_daty[du]=new Array(u," ");

	}
  //07:36 27.02.2013
  
  start(k)
  
  
  //zrobic z repertuar wg_daty zmienna globalna i tutaj dodac najnowszy jako dzisiaj przy mijaniu kilku starych niewypelnionych dni a jak nie to trzba tam na dole gedzie jest if(k==-2)
 }

function zrozum(czas_text)
 {
var czas_tbl=czas_text.split(" ");
   string_czas_text=czas_tbl[0]+"-"+czas_tbl[1]+"-"+czas_tbl[2]+"T"+czas_tbl[3]+":"+czas_tbl[4]+":0.00"
  
  
  var czas_o=new Date(string_czas_text);
    return(czas_o.getTime())
 }
 
function sort_fc(Item1,Item2)
{
  var item1 = Item1[0];
  var item2 = Item2[0];
  if (item1 > item2) return 1;
  if (item1 < item2) return -1;
  return 0;
}


function godzina_form(x)
	{
	 var uu=new Date(x);
	 var godz_f=uu.getHours();
	 var minuta_f=uu.getMinutes();
       if(godz_f<10)
         godz_f="0"+godz_f;
       if(minuta_f<10)
       minuta_f="0"+minuta_f;
	 calosc=godz_f+":"+minuta_f;
	 return(calosc);
 	} 

dni_tyg=new Array("Niedziela","Poniedziałek","Wtorek","Środa","Czwartek","Piątek","Sobota")


 
function start(k)
  {
  
     
 if(<?PHP printf("\"".$czy_zalogo."\"");?>=="tak")
 zalogowano=1;
 else
 zalogowano=0;



   zawartosc="<form name='nowy' method='post' action='edit.php' enctype='multipart/form-data'>";// method='post'
   zawartosc+="<table width=1255 border=0 CELLPADDING=0 CELLSPACING=0>";
 if(k!=-3)
 rep_wg_daty=new Array(); //chyba tylko tu skasowac var aby ten dzien po minienciu

   var i
   var ko=0
 if(k!=-3)
 for(i=0;i<opisy.length;i++)
   {
    for(j=0;j<rep[i].length;j++)
	{
	 if(rep[i][j])
	 { 
	  rep_wg_daty[ko]=new Array(zrozum(rep[i][j]),i);
	  ko++;
	 }
	}
   }
   
  for(i=0;i<nieczynne.length;i++) //do listy seansow dodano lizte dni nieczynnych ale cyz to ma sens ? Ma i to dziala doskonae ale konieczne testy
   {
    rep_wg_daty[ko]=new Array(zrozum(nieczynne[i]),-1);
	ko++
   }

   rep_wg_daty.sort(sort_fc) //tablice wektorow seansow posortowana wg daty
   
  	
	var u_bak=1	
   if(k==-2) //dodajemy dzien  //jesli mialoby mijac kilka dni to takze tutuaj trzeba co s wykombinowac, ale warto sprobowac z fcja wczesniej 
   {
   	var du=rep_wg_daty.length
	var u=rep_wg_daty[du-1][0]+86400000;
	
	    rep_wg_daty[du]=new Array(u," ");
		 u_bak=0;
	k=u;
   }
   
   if(k==-3)
    {
	var dul=rep_wg_daty.length
	var ul=rep_wg_daty[dul-1][0];
	//alert("u w if-3\n"+ul);
	 u_bak=0;
	 k=ul
	}   
   
   //tutaj funkcja wczesniej zmienia tego fora  //musi tez byc przycisk dzis itp
	var ile_rep_daty=rep_wg_daty.length-1;       
	var ile_wyswietl=10; // ile dni wyswietlac  (ale zeby to byli liczby parzyste raczej)   w przyszlosci mozna to zmieniac fcja.
	
	if(srodek==-1)
	{
	
	 pocz=rep_wg_daty[ile_rep_daty][0];
	 koniec=((pocz>rep_wg_daty[0][0]+ile_wyswietl*86400000)?(pocz-ile_wyswietl*86400000):rep_wg_daty[0][0]);
	 srodek=pocz-Math.floor(ile_wyswietl/2)*86400000;
	 srodek=((srodek<0)?rep_wg_daty[0][0]:srodek)
	 
	 }
	else
	 {
	   pocz=srodek+Math.floor(ile_wyswietl/2)*86400000;
	   pocz=((pocz>rep_wg_daty[ile_rep_daty][0])?rep_wg_daty[ile_rep_daty][0]:pocz);
	   pocz=((pocz<rep_wg_daty[0][0]+ile_wyswietl*86400000)?(rep_wg_daty[0][0]+ile_wyswietl*86400000):pocz)
	   koniec=srodek-Math.floor(ile_wyswietl/2)*86400000;
	   koniec=((koniec<rep_wg_daty[0][0])?rep_wg_daty[0][0]:koniec);
	   koniec=((koniec>(rep_wg_daty[ile_rep_daty][0]-ile_wyswietl*86400000))?(rep_wg_daty[ile_rep_daty][0]-ile_wyswietl*86400000):koniec)
	 }
//document.write("ile_rep_daty "+ile_rep_daty+"\n pocz "+pocz+" srodek "+srodek+" koniec "+koniec);
  // alert("ile_rep_daty "+ile_rep_daty+"\n pocz "+pocz+" srodek "+srodek+" koniec "+koniec);
    poz="<a href='javascript:wczesniej("+k+",1)'> <<< później<a>"
		
    if(pocz>=rep_wg_daty[ile_rep_daty][0]-1)  //18:40 21.12.2012
	{
	poz="";
	}
	wcze="<a href='javascript:wczesniej("+k+",-1)'>wcześniej >>><a>"
	if(koniec<=rep_wg_daty[0][0])//+86400000
	{
	wcze="";
	}
    
	
   zawartosc+="<tr><td></td><td colspan="+Math.floor(ile_wyswietl/2)+" align=right>"+poz+"</td><td></td><td colspan="+Math.floor(ile_wyswietl/2)+">"+wcze+"</td></tr>";
    
   //--wyswietlanie naglowkow daty dziennej
   zawartosc+="<tr >"
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
   if(u_bak&&zalogowano)//tutaj trzeba dodac czasem wczesniej(-2,+2) jesli poniedzialek //18:40 21.12.2012
   {
   
   dzis_roz=new Date()
	//zamiast mod (a-a%b)/b
	roznica=Math.floor((dzis_roz.getTime()-rep_wg_daty[ile_rep_daty][0])/86400000);
	//alert("dzis\n"+dzis_roz.getTime())
	//alert("ostatni\n"+rep_wg_daty[ile_rep_daty][0])
 //------ tutaj dodawanie dnia musi uwzgledzniec miniecie paru dni 
   if(roznica>1)//jais dobry warunek typu jesli teraz > najnowszego zapisanego
   {
    zawartosc+="<td width=155><button onclick='javascript:wczesniej(-3,"+(roznica+1)+")' style='width:100'>dodaj\n dzien -3 </button><br>"
   }
   else
    zawartosc+="<td width=155><button onclick='javascript:start(-2),wczesniej(-2,+1)' style='width:100'>dodaj\n dzien </button><br>"//w tej td musi byc dodanie dnia
   //zawartosc+="<a href='javascript:popup_seans(1)'>dodaj\n seans</a></td>"
   zawartosc+="<button onclick='javascript:start(-1),popup_seans(-1)' style='width:100'>dodaj\n seans</button></td>" //w tej td musi byc dodanie seansu
   }
   else
   zawartosc+="<td width=155></td>"
    var ddl=-1
	var kol=0;
	var nieczynne_dni=new Array();
	var knd=0;
	 	
	//srodek
    //for(i=rep_wg_daty[pocz][0];i>=rep_wg_daty[koniec][0];i-=86400000)
	//alert("poczatek"+pocz+" koniec= "+koniec+"dni="+(pocz-koniec)/86400000);
	for(i=pocz;i>=koniec;i-=86400000)
	{
	 kolor=(kol%2==1)?kcj:kc
	 kol++
	  var nag_daty=new Date(i) 
	//  zawartosc+="<td width=100 height=110 align=center bgcolor=#"+kolor+" style='width: 100'>"+nag_daty.getFullYear()+"<br>"+(nag_daty.getMonth()+1)+" "+nag_daty.getDate()+"<br>"+dni_tyg[nag_daty.getDay()];
zawartosc_uu="";	

dzien_nazwy_dnia=nag_daty.getFullYear()+" "+(nag_daty.getMonth()+1)+" "+nag_daty.getDate();
//alert(dzien_nazwy_dnia);
zawartosc_nd=""
if(nazwa_dnia[dzien_nazwy_dnia])
  zawartosc_nd=nazwa_dnia[dzien_nazwy_dnia]
   if(zalogowano)
	  {
	   var teraz=new Date()
       if(Math.floor(teraz.getTime()/86400000)<=Math.floor(nag_daty.getTime()/86400000)) // if ktory uniemozliwia edycje przeszlosci
	   {
	   //v
	    var czynne="<br> <input type='submit' value='nieczynne' name='nieczynne' style='width:80'> " //jesli czynne to przycisk nieczynne
		 var zapisz="<br><input type='submit' value='zapisz' name='klik' style='width:80'>";
		 for(da=0;da<nieczynne.length;da++)
		 {
		   var mi=new Date(zrozum(nieczynne[da]))
		  if(nag_daty.getDate()==mi.getDate()&&nag_daty.getMonth()==mi.getMonth()&&nag_daty.getFullYear()==mi.getFullYear())
		  {
		  kolor=kolor_nieczynny;
		   nieczynne_dni[knd]=i;
		   knd++;
		   czynne="<br> <input type='submit' value='czynne' name='nieczynne' style='width:80'>  " //jesli nieczynne to przycisk czynne
		   zapisz="<br><input type='submit' value='zapisz' name='klik_niecz' style='width:80'>";
		  }
		  }
		 //^ 
	    if(k==nag_daty.getTime())
		{
	     zawartosc_uu+="<input type='hidden' name='kolumna' value='"+nag_daty.getFullYear()+" "+(nag_daty.getMonth()+1)+" "+nag_daty.getDate()+"'>"
		 zawartosc_uu+="<input type='hidden' name='srodek' value='"+srodek+"'>"
		//stad
		
		 zawartosc_nd="nazwa dnia:<br><input size=12 type='text' name='nazwa_dnia' value='"+zawartosc_nd+"'>"
		 		  
		 zawartosc_uu+=zapisz;
		 zawartosc_uu+=czynne;
		 zawartosc_uu+="<br><button onclick='javascript:start(-1)' style='width:80'>anuluj</button>"
		 }
	    else        
	     zawartosc_uu+="<br><button onclick='javascript:start("+nag_daty.getTime()+")' style='width:80'>edytuj</button><br>"
	   }
	   else
	    zawartosc_uu+="<br> "

	   
	  }
	  zawartosc+="<td width=100 height=110 align=center bgcolor=#"+kolor+" style='width: 100'>"
	  zawartosc+=zawartosc_nd+"<br>"
	  zawartosc+=nag_daty.getFullYear()+"<br>"+(nag_daty.getMonth()+1)+" "+nag_daty.getDate()+"<br>"+dni_tyg[nag_daty.getDay()];
	  zawartosc+=zawartosc_uu;
	  zawartosc+="</td>"
	}
	zawartosc+="</tr>"
		
   //--koniec wyswietlanie naglowkow daty dziennej
   
    var nt=new Date() // data ktora edytujemy
	   nt.setTime(k)
	 
  // var t=0
   //--wyswietlene bazy
//istnieje problem dotyczacy bazy z jednym wpisem dla seansu nei potrafi tego przetrawic wiesza sie js na nieskonczonej petli
   for(i=0;i<opisy.length;i++)//leci po seansach 
   {
     kolor=(i%2==1)?  kcj : kjj;
	 zawartosc+="<tr><td bgcolor=#"+kolor+" style='height: 60'>"+opisy[i][0];
	 if(zalogowano)
	  zawartosc+="<br><button onclick='javascript:start(-1),popup_seans("+i+")' style='width:100'>edytuj seans</button>";
	 zawartosc+="</td>"
	 var g=rep[i].length;   //ile dat jest dla danego seansu //dziala
	 
     var jas=0
	  
	   
	 //srodek
	 
	for(j=pocz;j>=koniec;j-=86400000) //to jest petla po naglowkach dat
	{ 
	   var nieczynnosc=0
	   for(utnd=0;utnd<nieczynne_dni.length;utnd++)
	   {
	   if(j==nieczynne_dni[utnd])
	    nieczynnosc=1
	   }
	   
	 kolor=(i%2==1)?((jas%2==1)? kcj:kc): ((jas%2==1)? kjj:kj);
	 jas++
	 kolor=(nieczynnosc==1)?kolor_nieczynny:kolor;
	 	 var nag_daty=new Date(j) 
		 
	   //----czesc o nieczynnych poniedzialkach

	  //--- koniec czesci o nieczynnych poniedzialkach !!                                                !! pamietaj aby dodac jeszcze zmiane przy dodawaniu dnia z okazji nieczynnego
	var godziny=" ";
  //wersja z 07:56 19.12.2012
    t=0 //od kazdego naglowka daty zerujemy licznik
	if(g)//zabezpieczenie jesli nie ma wogole daty np w nowym seansie
	{
	var tt=new Date(zrozum(rep[i][t])); //w tt jest pierwsza data w danym seansie
 godziny=" ";
 
  var przecinek=0 //w tym forze ponizej jest problem z przkroczeniem roku byc moze misaca tez?  22:55 23.12.2012  poprawiono na sprawdzanie Time zamiast dzien misac rok
	while(nag_daty.getTime()-86400000<=tt.getTime()&&t<g) //jedzie od t=0 az do ostatniego t dla aktualnej daty
	 {
	 	//	alert("j="+j+"\n i="+i+"\n t="+t)
	  
	  if(nag_daty.getDate()==tt.getDate()&&nag_daty.getMonth()==tt.getMonth()&&nag_daty.getFullYear()==tt.getFullYear()&&t<g)//sprawdza kolejne daty czy nie sa z tego samego dnia i dodaje godziny po przecinku
	  {
	   colon=((przecinek==0)?" ":",") //to odpowiada za robirnie przecinka tylko po godzinach
	   godziny+=colon+godzina_form(zrozum(rep[i][t]))//dopisuje kolejna godzine dla danejn datydla danego seansu 
	   przecinek=1
	  }
	  t++
	  if(t<g)
	   tt=new Date(zrozum(rep[i][t]))
	 }
	}
	
	
	
	// vv tutaj ewentualnie mozna dodac cos o zmianie koloru dla nieczynnych badz dezaktywacja textinmputow jesli nieczynne
	
	
	
	if(nag_daty.getDate()==nt.getDate()&&nag_daty.getMonth()==nt.getMonth()&&nag_daty.getFullYear()==nt.getFullYear())
	   {
	    var nieakt=(nieczynnosc==1)?"disabled='disabled'":"";
	    zawartosc+="<td width=100 bgcolor=#"+kolor+"><input size=12 type='text' name='"+i+"' value='"+godziny+"' "+nieakt+">"; //+godzina_form(zrozum(rep[i][t]))+
		//zawartosc+="<input type='hidden' name='kolumna' value='"+k+"'></td>"//value zmienic na format daty a nie k bedzie latwiej w php i wyslac tylko raz jakos
	   }
	   else
	   {
	   zawartosc+="<td bgcolor=#"+kolor+">"+godziny+"</td>"
	   } 
	/*  }
	 else
	  {
	  if(nag_daty.getDate()==nt.getDate()&&nag_daty.getMonth()==nt.getMonth()&&nag_daty.getFullYear()==nt.getFullYear())
	   {
	   zawartosc+="<td bgcolor=#"+kolor+" ><input type='text' name='"+i+"' size=12>";//value=' '
	   //zawartosc+="<input type='hidden' name='kolumna' value='"+k+"'></td>"
	   }
	   else
	   {
	   zawartosc+="<td bgcolor=#"+kolor+" ></td>";
	   }
	   }*/
	 }
   }
   //--koniec wyswietlania bazy
  

    zawartosc+="</table>";
wyswietl(zawartosc,"main");
  }


function popup_seans(u)
 {
  wysw="<form name='n_senas' method='post' action='edit.php' enctype='multipart/form-data'>"
  wysw+="<table width=1255 border=0 CELLPADDING=20 CELLSPACING=20 bgcolor=#444444 align=center>";
  wysw+="<tr><td  align=center><table border=0 CELLPADDING=5 CELLSPACING=5 bgcolor=#88888 >"
  wysw+="<tr><td  align=center colspan=2>Dodaj/Edytuj Seans</td></tr>"
  if(u<0)
  {
  wysw+="<input type='hidden' name='ktory_seans' value=-1>"
  wysw+="<tr><td>Tytyuł:</td><td><input type='text' name='ns_nazwa' size=132></td></tr>"
  wysw+="<tr><td>Opis:</td><td><textarea name='ns_opis' cols=100 rows=10></textarea></td></tr>"
  wysw+="<tr><td>Typ:</td><td><select name='ns_typ'><option value='D'>Seans dla dzieci</option><option value='P'>Seans popularny</option></select></td></tr>"
  wysw+="<tr><td>Czas trwaia:</td><td><input type='text' name='ns_czas' size=8 maxlength='3'> minuty</td></tr>"
  wysw+="<tr><td></td><td></td></tr>"
  wysw+="<tr><td></td><td><input type='submit' value='zapisz' name='nowy_seans'><button onclick='javascript:start(-1)'>anuluj</button></td></tr>"
  }
  else
  {
   wysw+="<input type='hidden' name='ktory_seans' value="+u+">"
  wysw+="<tr><td>Tytyuł:</td><td><input type='text' name='ns_nazwa' size=132 value='"+opisy[u][0].replace(/<br>/gi,"\r\n")+"'></td></tr>"
  wysw+="<tr><td>Opis:</td><td><textarea name='ns_opis' cols=100 rows=10> "+opisy[u][1].replace(/<br>/gi,"\r\n")+"</textarea></td></tr>"
   dd=((opisy[u][2]=="D")?"selected":" ")
   dp=((opisy[u][2]=="P")?"selected":" ")
   wysw+="<tr><td>Typ:</td><td><select name='ns_typ'><option value='D' "+dd+">Seans dla dzieci</option><option value='P'  "+dp+">Seans popularny</option></select></td></tr>"
  wysw+="<tr><td>Czas trwaia:</td><td><input type='text' name='ns_czas' size=8 maxlength='3'  value='"+opisy[u][3]+"'> minuty</td></tr>"
  wysw+="<tr><td></td><td></td></tr>"
  wysw+="<tr><td></td><td><input type='submit' value='skasuj_seans' name='skasuj_seans'><input type='submit' value='zapisz' name='nowy_seans'><button onclick='javascript:start(-1)'>anuluj</button></td></tr>"
  }
  wysw+="</table></td></tr>"
  wysw+="</table>"
  wysw+="</form>"
  wyswietl(wysw,"popup");
 }

    function wyswietl(co,gdzie)
    {
     if(document.all)
     { 
      document.all[gdzie].innerHTML=co;
     }
     else
     {
      if(document.layers)
      {
       document.layers[gdzie].document.open();
       document.layers[gdzie].document.write(co);
       document.layers[gdzie].document.close();
      }
      if(document.getElementById)
       document.getElementById(gdzie).innerHTML=co;
     }

<?PHP
//odsierzanie przeladowanie
if($czy_zapis)
{//wytagowane ale potrzene narazie aby bylo widac bledy
//printf("setTimeout(function(){window.location.href='./edit.php?s=$srodek';}, 100);");

}/*?ref=1
if($_GET[ref]==1)
{

//printf("var t = Math.floor(Math.random()*1000);");
//printf("window.location.href = window.location.href + '&rn=' + t;");

printf("setTimeout(function(){window.location.href='./edit.php';}, 100);");
}
printf("function buu(){}");*/
?>

}

</script>
<title>Edycja bazy danych repertuaru Planetarium Śląskiego</title>
</head>
 <body onload='start(-1)' bgcolor=cccccc>
<div align=right>
<?PHP

if($czy_zalogo=="nie")
{
printf( "zaloguj sie aby dokonywać zmian<br>");
printf( "<form name=\"nowy1\" method=\"post\" action=\"edit.php\" enctype=\"multipart/form-data\">");
printf( "<input type=text name=\"login\" size=10>");
printf( "<input type=password name=\"haslo\" size=10 value=''>");
printf( "<input type='submit' value='zaloguj' name='logowanie'><br>");
}
if($czy_zalogo=="zle")
{
printf( "błędna nazwa użytkownika badz hasło spróbuj ponownie<br>");
printf( "<form name=\"nowy1\" method=\"post\" action=\"edit.php\" enctype=\"multipart/form-data\">");
printf( "<input type=text name=\"login\" size=10>");
printf( "<input type=password name=\"haslo\" size=10 value=''>");
printf( "<input type='submit' value='zaloguj' name='logowanie'><br>");
}

if($czy_zalogo=="tak")
{
printf( "Zostałeś zalogowany jako: ".$_SESSION['user']."<br>");
printf( "<form name=\"nowy1\" method=\"post\" action=\"edit.php\" enctype=\"multipart/form-data\">");
printf( "<input type='submit' value='wyloguj' name='logowanie'><br>");
}
?>
</div>
 <center>
    <div id="popup" style="font-size: small; position:fixed; left:10; top:150; z-index:10"> </div>
    <div id="main" style="font-size: small; position:absolute; z-index:1"> </div>
<hr>
 </body>
</html>
