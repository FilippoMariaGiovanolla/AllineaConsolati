<html>
	<head>
		<title>NonInseribili</title>
	</head>
	<body>
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
			$NumConsolatiNonInseribili=$_POST["nonInseribili"];
			$clienteNomeFile=str_replace(" ","_",$clienteVisualizzato);
			$filename="ConsolatiNonInseribili".$clienteNomeFile.".xls";
			header ("Content-Type: application/vnd.ms-excel");
			header ("Content-Disposition: inline; filename=$filename");
			echo("<h3>Consolati da gestire a mano nella tabella locale:</h3>");
			$conta=0;
			$query="select * from consolatiministero";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i consolati dalla tabella <i>ConsolatiMinistero</i>: ".mysql_error());
			while($riga=mysql_fetch_row($risultato))
			{
				$query="select codministeriale
						from consola
						where codministeriale=$riga[0]";
				$risultato2=mysql_query($query)
					or die("Impossibile cercare il consolato ".$riga[0]." nella tabella <i>consola</i>: ".mysql_error());
				$righe=mysql_num_rows($risultato2);
				if($righe==0)
				{
					if($conta==0)
					{
						echo("<font color='red'><b>I seguenti consolati devono essere aggiornati manualmente nella tabella locale,</b><br>");
						echo("<font color='red'><b>perch&eacute; nel campo 'CodiceCons' della tabella 'Consola' &egrave; presente un</font></b><br>");
						echo("<font color='red'><b>valore corretto, ma differente rispetto a quello presente nel campo 'CodMinisteriale'</b></font><br>");
						echo("<font color='red'><b>(anch'esso corretto, ma associato ad un altro consolato della tabella ministeriale), per</font></b><br>");
						echo("<font color='red'><b>cui non &egrave; possibile effettuare operazioni automatiche su di essi:</b></font><br><br>");
						echo("<table border=1 align='center'>");
						echo("<tr>
								<td><div align='center'><b><font color='blue'>CodiceCons</font></b></div></td>
								<td><div align='center'><b>CodStatoAppartenenza</b></div></td>
								<td><div align='center'><b>StatoAppartenenza</b></div></td>
								<td><div align='center'><b>ComuneSede</b></div></td>
								<td><div align='center'><b>Rango</b></div></td>
								<td><div align='center'><b>Indirizzo</b></div></td>
							  </tr>");
						echo("<tr>
								<td><div align='center'><font color='blue'>".$riga[0]."</font></div></td>
								<td><div align='center'>".$riga[1]."</div></td>
								<td><div align='center'>".$riga[2]."</div></td>
								<td><div align='center'>".$riga[3]."</div></td>
								<td><div align='center'>".$riga[4]."</div></td>
								<td><div align='center'>".$riga[5]."</div></td>
							  </tr>");
					}
					else
					{
						echo("<tr>
								<td><div align='center'>".$riga[0]."</div></td>
								<td><div align='center'>".$riga[1]."</div></td>
								<td><div align='center'>".$riga[2]."</div></td>
								<td><div align='center'>".$riga[3]."</div></td>
								<td><div align='center'>".$riga[4]."</div></td>
								<td><div align='center'>".$riga[5]."</div></td>
							  </tr>");
					}
					$conta++;
				}
			}
			mysql_close($conn);
		?>
	</body>
</html>