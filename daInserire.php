<html>
	<head>
		<title>Record da inserire</title>
	</head>
	<body>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
			@mysql_select_db('consolati')
				or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
			echo("<h3><div align='center'><br>Consolati non presenti nella tabella locale che sono invece presenti nella tabella ministeriale</div></h3>");
			echo("<div align='justify'>Di seguito vengono elencati quei consolati della tabella ministeriale che non trovano nessuna corrispondenza nel campo <i>CodMinisteriale</i> della tabella locale.</div>");
			echo("<br><br>");
			
			/*con questa query controllo il contenuto della tabella di raccordo 'DaInserire', in modo tale che, se l'utente aggiorna con F5 la pagina daInserire.php, il programma non fa l'insert degli stessi dati più volte --> vedi condizione if($numRigheControllo==0) */
			$queryControllo="select * from DaInserire";
			$risultatoControllo=mysql_query($queryControllo)
				or  die("Impossibile estrarre i dati della tabella <i>DaInserire</i>: ".mysql_error());
			$numRigheControllo=mysql_num_rows($risultatoControllo);
			
			
			$ConsolatiMancanti=0;			
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
					if($ConsolatiMancanti==0)
					{
						echo("<table border=1 align='center'>");
						echo("<tr>
								<td><div align='center'><b>CodiceConsolato</b></div></td>
								<td><div align='center'><b>CodStatoAppartenenza</b></div></td>
								<td><div align='center'><b>StatoAppartenenza</b></div></td>
								<td><div align='center'><b>ComuneSede</b></div></td>
								<td><div align='center'><b>Rango</b></div></td>
								<td><div align='center'><b>Indirizzo</b></div></td>
							  </tr>");
					}
					echo("<tr>
							<td><div align='center'>".$riga[0]."</div></td>
							<td><div align='center'>".$riga[1]."</div></td>
							<td><div align='center'>".$riga[2]."</div></td>
							<td><div align='center'>".$riga[3]."</div></td>
							<td><div align='center'>".$riga[4]."</div></td>
							<td><div align='center'>".$riga[5]."</div></td>
						  </tr>");
					if($numRigheControllo==0)
					{
						$CodiceConsolato=$riga[0];
						$CodStatoAppartenenza=$riga[1];
						$StatoAppartenenza=addslashes($riga[2]);
						$ComuneSede=addslashes($riga[3]);
						$Rango=addslashes($riga[4]);
						$Indirizzo=addslashes($riga[5]);
						$insert="insert into DaInserire 
								 values ('$CodiceConsolato','$CodStatoAppartenenza','$StatoAppartenenza','$ComuneSede','$Rango','$Indirizzo')";
						$risultatoInsert=mysql_query($insert)
							or die("Impossibile inserire i dati nella tabella di raccordo <i>DaInserire</i>: ".mysql_error());
					}
					$ConsolatiMancanti++;
				}
			}
			if($ConsolatiMancanti>0)
				echo("</table>");
			if($ConsolatiMancanti==0)
			{
				echo("<br>");
				echo("<h3><font color='red'>Tutti i consolati della tabella ministeriale sono presenti anche in <i>Consola</i></font></h3>");
			}
			else
			{
				echo("<div align='center'>");
					echo("<form name='cliente' action='conversioneExcelDaInserire.php' method='POST'>");
						echo("<input type='hidden' name='numDaInserire' value='$ConsolatiMancanti'>");
						echo("<h3>Totale consolati da inserire nella tabella locale: ".$ConsolatiMancanti."</h3>  ");
						echo("<input type='submit' name='go' value='Esporta risultato in Excel'>");
					echo("</form>");
					echo("<form name='cliente' action='inserimento.php' method='POST'>");
						echo("<input type='submit' name='go2' value='Aggiorna tabella locale con consolati mancanti'>");
					echo("</form>");
				echo("</div>");
			}
			mysql_close($conn);
		?>
		<br>
		<table border=0 width="100%">
			<tr>
				<td width="50%"><a href="allineamentoConsolati.php">Torna alla pagina di allineamento consolati</a></td>
				<td width="50%"><a href="index.php"><div align="right">Torna alla pagina iniziale</div></a></td>
				
			</tr>
		</table>
	</body>
</html>