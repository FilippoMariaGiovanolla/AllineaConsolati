<html>
	<head>
		<title>Reset tabelle</title>
	</head>
	<body>
	<?php
		$hostname='localhost';
		$username='root';
		$conn=mysql_connect($hostname,$username,'')
			or die("Impossibile stabilire una connessione con il server: ".mysql_error());
		$db=mysql_select_db('consolati')
			or die("Impossibile selezionare il database <i>Consolati</i>: ".mysql_error());
		$query="delete from consola";
		$risultato=mysql_query($query)
			or die("Impossibile cancellare il contenuto della tabella 'Consola': ".mysql_error());
		$query="update cliente set cliente=''";
		$risultato=mysql_query($query)
			or die("Impossibile resettare il contenuto della tabella 'cliente': ".mysql_error());
		$query="delete from ConsolatoComuneSede";
		$risultato=mysql_query($query)
			or die("Impossibile cancellare il contenuto della tabella 'ConsolatoComuneSede': ".mysql_error());
		$query="delete from AltreinfoRango";
		$risultato=mysql_query($query)
			or die("Impossibile cancellare il contenuto della tabella 'AltreinfoRango': ".mysql_error());
		$query="delete from DaInserire";
		$risultato=mysql_query($query)
			or die("Impossibile cancellare i dati dalla tabella 'DaInserire'");
		$query="delete from DaAggiornare";
		$risultato=mysql_query($query)
			or die("Impossibile cancellare i dati dalla tabella 'DaAggiornare'");
		echo("Il contenuto della tabella <i>Consola</i> &egrave; stato correttamente cancellato.");
		mysql_close($conn);
	?>
	<br><br>
	<a href="index.php">Torna alla pagina iniziale</a>
	</body>
</html>