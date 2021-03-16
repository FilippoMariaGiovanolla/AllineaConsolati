<html>
	<head>
		<title>Allineamento consolati</title>
	</head>
	<body>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
			@mysql_select_db('consolati')
				or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
			
			//aggiorno il campo consolato della tabella consola
			$query="select * from ConsolatoComuneSede";
			$risultato=mysql_query($query)
				or die("Impossibile selezionare il contenuto della tabella <i>ConsolatoComuneSede</i>: ".mysql_error());
			while($riga=mysql_fetch_row($risultato))
			{
				$ComuneSede=addslashes($riga[2]);
				$update="update consola 
						set Consolato='$ComuneSede' 
						where CodiceCons=$riga[0]";
				$risultatoUpdate=mysql_query($update)
					or die("Impossibile aggiornare il campo <i>consolato</i> della tabella <i>consola</i>: ".mysql_error());
			}
			
			//aggiorno il campo AltreInfo della tabella consola
			$query="select * from AltreInfoRango";
			$risultato=mysql_query($query)
				or die("Impossibile selezionare il contenuto della tabella <i>AltreInfoRango</i>: ".mysql_error());
			while($riga=mysql_fetch_row($risultato))
			{
				$rango=addslashes($riga[2]);
				$update="update consola
						set AltreInfo='$rango'
						where codicecons=$riga[0]";
				$risultatoUpdate=mysql_query($update)
					or die("Impossibile aggiornare il campo <i>AltreInfo</i> della tabella <i>consola</i>: ".mysql_error());
			}
			
			//cancello i dati della tabella ConsolatoComuneSede
			$query="delete from ConsolatoComuneSede";
			$risultato=mysql_query($query)
				or die("Impossibile cancellare i dati della tabella <i>ConsolatoComuneSede</i>: ".mysql_error());
				
			//cancello i dati della tabella AltreInfoRango
			$query="delete from AltreInfoRango";
			$risultato=mysql_query($query)
				or die("Impossibile cancellare i dati della tabella <i>AltreInfoRango</i>: ".mysql_error());
			
			//controllo se ci sono dei consolati di cui non è stato possibile l'allineamento per via di discrepanza di codici
			$query="select a.codMinisteriale, a.consolato, b.comunesede, a.altreinfo, b.rango, a.codicecons
					from consola a, consolatiministero b
					where a.codministeriale=b.codiceconsolato and (a.consolato!=b.comunesede or a.altreinfo!=b.rango)";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i consolati con dati da aggiornare: ".mysql_error());
			$righe=mysql_num_rows($risultato);
			if($righe>0)
			{
				echo("<h3>I dati della tabella <i>consola</i> sono stati allineati.</h3>");
				echo("<div align='justify'>Per i seguenti ".$righe." consolati non &egrave; stato per&ograve; possibile effettuare l'aggiornamento, in quanto, sulla tabella <i>Consola</i>, uno tra i codici presenti nei campi <i>CodiceCons</i> e <i>CodMinisteriale</i> &egrave; sbagliato.<br>
				Per questi consolati occorre quindi procedere manualmente nella tabella locale, verificando se siano doppi ed eventualmente aggiornando il codice sbagliato con quello corretto.</div><br>");
				echo("<table border=1 align='center'>
						<tr>
							<td><div align='center'><b>CodiceCons</b></div></td>
							<td><div align='center'><b>CodMinisteriale</b></div></td>
							<td><div align='center'><b>Consolato</b></div></td>
							<td><div align='center'><b>AltreInfo</b></div></td>
						</tr>");
				while($riga=mysql_fetch_row($risultato))
				{
					echo("<tr>
							<td valign='middle'><div align='center'>".$riga[5]."</div></td>
							<td valign='middle'><div align='center'>".$riga[0]."</div></td> ");
						if($riga[1]!=$riga[2])
						{
							echo("<td><div align='center'>".$riga[1]."<br><b><font color='red'>".$riga[2]." (tab. ministeriale)</font></b></div></td>");
							
							/* ora popolo la tabella di raccordo per l'eventuale successivo aggiornamento del campo consolato della tabella consola
							if($righeConsolatoComuneSede==0)
							{
								$consolatoDaInserire=addslashes($riga[1]);
								$comunesedeDaInserire=addslashes($riga[2]);
								$insert="insert into ConsolatoComuneSede values ($riga[0],'$consolatoDaInserire','$comunesedeDaInserire')";
								$risultatoInsert=mysql_query($insert)
									or die("Impossibile inserire i dati nella tabella <i>ConsolatoComuneSede</i>: ".mysql_error());
							}*/
						}							
						else
						{
							echo("<td valign='middle'><div align='center'>".$riga[1]."</div></td>");
						}
						
						if($riga[3]!=$riga[4])
						{
							echo("<td><div align='center'>".$riga[3]."<br><b><font color='red'>".$riga[4]." (tab. ministeriale)</font></b></div></td>");
							
							/* ora popolo la tabella di raccordo per l'eventuale successivo aggiornamento del campo AltreInfo della tabella consola 
							if($righeAltreinfoRango==0)
							{
								$altreinfoDaInserire=addslashes($riga[3]);
								$rangoDaInserire=addslashes($riga[4]);
								$insert="insert into AltreinfoRango values ($riga[0],'$altreinfoDaInserire','$rangoDaInserire')";
								$risultatoInsert=mysql_query($insert)
									or die("Impossibile inserire i dati nella tabella <i>AltreinfoRango</i>: ".mysql_error());
							}*/
						}
						else
							echo("<td valign='middle'><div align='center'>".$riga[3]."</div></td>");
						
						echo("</tr>");
				}
				echo("</table>");
				echo("<br><br>");
				echo("<div align='center'>");
					echo("<form name='cliente' action='conversioneExcelDaAggiornare.php' method='POST'>");
						echo("<input type='hidden' name='numDaAggiornare' value='$righe'>");
						echo("<input type='submit' name='go' value='Esporta risultato in Excel'>");
					echo("</form>");
				echo("</div>");
			}	
			else
			{
				echo("<h3>I dati della tabella <i>consola</i> sono stati allineati.</h3>
				      <div align='justify'>Accedi nuovamente alla pagina di <a href='daAggiornare.php'>controllo dei dati da aggiornare</a> per verificare che non ci siano pi&ugrave; dati discordanti tra la tabella locale e la tabella ministeriale</div>");
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