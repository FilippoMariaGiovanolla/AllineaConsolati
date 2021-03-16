<?php
$host_name='localhost';
$user_name='root';
$conn=@mysql_connect($host_name,$user_name,'')
	or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
@mysql_select_db('consolati')
	or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
$queryCliente="select cliente
   from cliente";
$risultatoCliente=mysql_query($queryCliente)
	or die("Impossibile estrarre il nome del Cliente in esame: ".mysql_error());
while($cliente=mysql_fetch_row($risultatoCliente))
$clienteVisualizzato=$cliente[0];
$clienteNomeFile=str_replace(" ","_",$clienteVisualizzato);
$filename="Consola".$clienteNomeFile."DaZippare.csv";
header ("Content-Type: application/vnd.ms-excel");
header ("Content-Disposition: inline; filename=$filename");
$query="select CodiceCons,'	',Consolato,'	',Presso,'	',AltreInfo,'	',ZipCode,'	',IstatComune,'	',CodMinisteriale,'	',DInizioValidita,'	',DFineValidita from consola";
$risultato=mysql_query($query)
	or die("Impossibile estrarre i dati della tabella <i>Consola</i>: ".mysql_error());
$colonne=mysql_num_fields($risultato);
while($riga=mysql_fetch_row($risultato))
{
	for($i=0;$i<$colonne;$i++)
	{
		if($i==($colonne-1))
			echo("$riga[$i]
"); // NON TOCCARE QUESTA RIGA: E' GIUSTO CHE SIA SCRITTA A CAPO IN QUESTO MODO: SERVE PER MANDARE A CAPO L'OUTPUT NEL FILE CSV
		else
			echo("$riga[$i]");
	}
}
mysql_close($conn);
?>
