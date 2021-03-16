<html>
	<head>
		<title>Allineamento consolati</title>
	</head>
	<body>
		<div align="center"><b><h3>Allineamento consolati</h3></b></div>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server: ".mysql_error());
			@mysql_select_db('consolati')
				or die ("Impossibile selezionare il database di confronto archivi, chiudere il programma e riprovare: ".mysql_error());
			$query="select * from consola";
			$risultato=mysql_query($query)
				or die("Impossibile selezionare gli elementi della tabella 'consola': ".mysql_error());
			$righe=mysql_num_rows($risultato);
			echo("<ul>");
			if($righe>0)
			{
				echo("<li>Caricamento file da confrontare con tabella ministeriale dei consolati (passo gi&agrave; eseguito)</li>");
				$query="select * from cliente";
				$risultato=mysql_query($query)
					or die("Impossibile estrarre il nome del Cliente: ".mysql_error());
				$riga=mysql_fetch_row($risultato);
				echo("<li><a href='allineamentoConsolati.php'>Accedi alla pagina di allineamento consolati</a></li>");
				echo("<li><a href='CancellazioneConsolati.php'>Cancellazione tabella <i>Consola</i> popolata con i consolati di ".$riga[0]."</a></li>");
			}
			else
			{
				echo("<li><a href='importDati.php'>Caricamento file da confrontare con tabella ministeriale dei consolati</a></li>");
				echo("<li>Accedi alla pagina di allineamento consolati</li>");
				echo("<li>Cancellazione tabella <i>Consola</i></li>");
			}
			echo("</ul>");
			mysql_close($conn);
		?>
	</body>
</html>