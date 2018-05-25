<?PHP
   //   header("Pragma: no-cache");
 //     header("cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    //  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
// 18:17 21.12.2012 zmiana w funkcji zapisz tlumacz_tab_php_js_2 aby mozna bylo robic puste tablice (") na ("")
// 11:35 09.01.2013 dodano funkcje tlumacz_tab_js_php_1w

function tlumacz_tab_js_php($nazwa_pliku)
{
 $stare_dane=fopen($nazwa_pliku,"r");
 $dlugosc_pliku=filesize($nazwa_pliku)-4;
 $zawartosc_js=fread($stare_dane,$dlugosc_pliku);
 fclose($stare_dane);
  //zastanow sie nas tym czy te cudzyslowie sa potrzebne lepiej je obcinac niz dzielic je wzgladem nich
 $tab_tym=explode("=new Array(",$zawartosc_js); //dzieli tablice na nazwe i ciag wartosci
 
 $tablica_php="\"),".$tab_tym[1]; //dodaje do ciagu danych "), 

 $tablica_druga=explode("\"),Array(\"",$tablica_php); //dzieli czesc z danymi wzgledem "),Array(\" 
 
  for($i=0;$i<count($tablica_druga);$i++)
  {
   $tbl_tmp=explode("\",\"",$tablica_druga[$i+1]); //dzieli ","
   $finall[$i]=$tbl_tmp;
  }
  //var_dump($finall[count($tablica_druga)-1]);
  return($finall);
 }
 
  
  function tlumacz_tab_js_php_1w($nazwa_pliku)
 {
  $stare_dane=fopen($nazwa_pliku,"r");
  $dlugosc_pliku=filesize($nazwa_pliku)-3;
  $zawartosc_js=fread($stare_dane,$dlugosc_pliku);
  fclose($stare_dane);
  //zastanow sie nas tym czy te cudzyslowie sa potrzebne lepiej je obcinac niz dzielic je wzgladem nich
  $tab_tym=explode("=new Array(\"",$zawartosc_js); //dzieli tablice na nazwe i ciag wartosci
 
  $finall=explode("\",\"",$tab_tym[1]); //dzieli ","
   
  return($finall);
 }
  
  
  
  function nazwa_tab_js($nazwa_pliku)
  {
   $stare_dane=fopen($nazwa_pliku,"r");
   $dlugosc_pliku=filesize($nazwa_pliku);
   $zawartosc_js=fread($stare_dane,$dlugosc_pliku);
   fclose($stare_dane);
   $tab_tym=explode("=new Array(",$zawartosc_js);
   
   return($tab_tym[0]);
  }
    
  
 function tlumacz_tab_php_js($tablica,$nazwa_tab_js)
 {
  $wsad=$nazwa_tab_js."=new Array(";
  
  for($i=0;$i<(count($tablica));$i++)
  {
    $wsad.="Array(\"".implode("\",\"",$tablica[$i])."\")";
	if($i!=(count($tablica)-1))
	$wsad.=",";
   }
   $wsad.=");";
  $wsad=str_replace('""','"',$wsad);
   return($wsad);
 }



 function tlumacz_tab_php_js_2($tablica,$nazwa_tab_js)
 {
  $wsad=$nazwa_tab_js."=new Array(";
  
  for($i=0;$i<(count($tablica)-1);$i++)
  {
    $wsad.="Array(\"".implode("\",\"",$tablica[$i])."\")";
	if($i!=(count($tablica)-2))
	$wsad.=",";
   }
   $wsad.=");"; 
   
  $wsad=str_replace(',",','," ",',$wsad);
    $wsad=str_replace('""','"',$wsad);
    $wsad=str_replace('(")','("")',$wsad); //dodane 18:31 21.12.2012 aby dzialalo puste dni
	$wsad=str_replace('",")','")',$wsad); //dodane 2012 10 04 to polepsza dodawanie do pustej tablicy
   return($wsad);
 }
?>
