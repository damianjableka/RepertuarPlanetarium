  <body>
<?PHP

printf("<table border=0 align=center> <tr><td align=center>");
    printf("<form name='login' method='POST' action='md5.php' enctype='multipart/form-data'>");
	printf("login:</td></tr><tr><td align=center>");
    printf("<input type='text' length=20 name='login'><br>");
	printf("</td></tr><tr><td align=center>hasło:</td></tr><tr><td>");
	printf("<input type='password' length=20 name='haslo'><br>");
	printf("</td></tr><tr><td align=center>powtórz hasło:</td></tr><tr><td>");
	printf("<input type='password' length=20 name='haslo2'><br>");
   printf("</td></tr><tr><td align=center>");
	printf("<input type='submit' value='generuj' name='log'></form>");
		printf("</td></tr></table>");
		
if(isset($_POST['log'])&&!empty($_POST['haslo'])&&!empty($_POST['login']))
{
 if($_POST['haslo']==$_POST['haslo2'])
 {
  printf("\"".md5($_POST['login'])."\"=>\"".md5($_POST['haslo'])."\"");
 }
 else
  printf("hasła nie zgadzają się");

}
?>
 
 </body>
</html>
