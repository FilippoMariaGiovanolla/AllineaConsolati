<html>
	<head>
		<title>Inserimento consolati</title>
	</head>
	<body>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
			@mysql_select_db('consolati')
				or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
				
			/*
			consola.codicecons=daInserire.CodiceConsolato
			consola.consolato=daInserire.ComuneSede
			consola.presso=daInserire.Indirizzo
			consola.altreinfo=daInserire.Rango
			consola.CodMinisteriale=daInserire.CodiceConsolato
			
			se in 'consola' non è presente un record che nel campo "codicecons" ha il valore del campo daInserire.CodiceConsolato, faccio la insert in 'consola'
			
			se in 'consola' è presente un record che nel campo "codicecons" ha il valore del campo daInserire.CodiceConsolato, devo controllare il campo "CodMinisteriale": 
				- se nel campo "CodMinisteriale" della tabella 'consola' c'è un valore che non è presente in nessun campo "CodiceConsolato" della
				  tabella 'ConsolatiMinistero', posso fare l'update proprio sul campo "CodMinisteriale" della tabella 'consola'; 
				- se invece nel campo "CodMinisteriale" della tabella 'consola' è presente il codice ministeriale di un altro consolato, porto a
				  video questo consolato (il record in esame della tablla consola) e non faccio nulla, lasciando decidere all'utente come 
				  comportarsi.
			*/
			
			$consolatiNonAggiornabili=0;
			$query="select * from daInserire";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i dati dei consolati da inserire: ".mysql_error());
			while($riga=mysql_fetch_row($risultato))
			{
				$codiceDaInserire=$riga[0];
				$ricerca="select * from consola where codicecons=$codiceDaInserire";
				$risultatoRicerca=mysql_query($ricerca)
					or die("Impossibile cercare tra i consolati della tabella <i>Consola</i>: ".mysql_error());
				$quantiTrovati=mysql_num_rows($risultatoRicerca);
				$consolato=addslashes($riga[3]);
				$presso=addslashes($riga[5]);
				$altreinfo=addslashes($riga[4]);
				if($quantiTrovati==0)
				{
					$insert="insert into consola (codicecons,consolato,presso,altreinfo,codministeriale)
							 values ('$codiceDaInserire','$consolato','$presso','$altreinfo','$codiceDaInserire')";
					$risultatoInsert=mysql_query($insert)
						or die("Impossibile effettuare l'inserimento nella tabella <i>Consola</i>: ".mysql_error());
				}
				else
				{
					while($rigaTrovata=mysql_fetch_row($risultatoRicerca))
					{
						$query="select * from consolatiministero where codiceconsolato=$rigaTrovata[6]";
						$risultatoQuery=mysql_query($query)
							or die("Impossibile estrarre i dati dalla tabella <i>consolatiministero</i>: ".mysql_error());
						$numeroRigheTrovate=mysql_num_rows($risultatoQuery);
						if($numeroRigheTrovate==0)
						{
							$update="update consola set CodMinisteriale=$codiceDaInserire where codicecons=$codiceDaInserire";
							$risultatoUpdate=mysql_query($update)
								or die("Impossibile aggiornare il campo <i>CodMinisteriale</i> della tabella <i>Consola</i>: ".mysql_error());
						}
					}
				}
			}
			echo("<h3>Inserimento consolati terminato correttamente</h3>");
			
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
						echo("<h3><div align='justify'><font color='red'>ATTENZIONE!! I seguenti consolati devono essere aggiornati manualmente nella tabella locale, perch&eacute; nel campo <i>CodiceCons</i> della tabella <i>Consola</i> &egrave; presente un valore corretto, ma differente rispetto a quello presente nel campo <i>CodMinisteriale</i> (anch'esso corretto, ma associato ad un altro consolato della tabella ministeriale), per cui non &egrave; possibile effettuare operazioni automatiche su di essi:</font></div></h3>");
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
			if($conta>0)
			{
				echo("</table>");
				echo("<br>");
				echo("<div align='center'>");
					echo("<form name='cliente' action='nonInseribili.php' method='POST'>");
						echo("<input type='hidden' name='nonInseribili' value='$conta'>");
						echo("<input type='submit' name='go' value='Esporta risultato in Excel'>");
					echo("</form>");
				echo("</div>");
			}
			
			$query="delete from DaInserire";
			$risultato=mysql_query($query)
				or die("Impossibile cancellare il contenuto della tabella <i>DaInserire</i>: ".mysql_error());
			
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