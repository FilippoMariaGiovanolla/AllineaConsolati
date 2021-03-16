<html>
	<head>
		<title>Record da aggiornare</title>
	</head>
	<body>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
			@mysql_select_db('consolati')
				or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
				
			/* a parità di codiceConsolato, devo controllare i record della tabella consola che hanno valori diversi da quella ministeriale nei campi consolato e altreInfo:
			
			consola.CodMinisteriale=consolatiministero.CodiceConsolato
			consola.consolato=consolatiministero.ComuneSede
			consola.AltreInfo=consolatiministero.Rango
			*/	
			
			echo("<h3><div align='center'>Consolati presenti nella tabella locale che hanno dati diversi rispetto alla tabella ministeriale</div></h3>");
			echo("<div align='justify'>A parit&agrave; di codice (il confronto con il codice della tabella ministeriale avviene sul campo <i>CodMinisteriale</i> della tabella <i>Consola</i> locale), vengono evidenziati quei consolati che, rispetto alla tabella ministeriale, hanno valori diversi nei campi <i>Consolato</i> e <i>AltreInfo</i>.</div>");
			
			$query="select a.codMinisteriale, a.consolato, b.comunesede, a.altreinfo, b.rango
					from consola a, consolatiministero b
					where a.codministeriale=b.codiceconsolato and (a.consolato!=b.comunesede or a.altreinfo!=b.rango)";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i consolati con dati da aggiornare: ".mysql_error());
			$righe=mysql_num_rows($risultato);
			if($righe>0)
			{
				$queryCliente="select cliente
							   from cliente";
				$risultatoCliente=mysql_query($queryCliente)
					or die("Impossibile estrarre il nome del Cliente in esame: ".mysql_error());
				while($cliente=mysql_fetch_row($risultatoCliente))
				{
					$clienteVisualizzato=$cliente[0];
					echo("<h3>Sono stati rilevati <u>".$righe." consolati con dati da aggiornare</u> per ".$clienteVisualizzato."</h3>");
				}
				echo("<table border=1 align='center'>
						<tr>
							<td><div align='center'><b>CodMinisteriale</b></div></td>
							<td><div align='center'><b>Consolato</b></div></td>
							<td><div align='center'><b>AltreInfo</b></div></td>
						</tr>");
				
				
				/*con questa query controllo il contenuto della tabella di raccordo 'ConsolatoComuneSede', in modo tale che, se l'utente aggiorna con F5 la pagina daAggiornare.php, il programma non fa l'insert degli stessi dati più volte -> vedi cond. if($righeConsolatoComuneSede==0) */
				$queryControllo="select * from ConsolatoComuneSede";
				$risultatoControllo=mysql_query($queryControllo)
					or die("Impossibile controllare il contenuto della tabella <i>ConsolatoComuneSede</i>: ".mysql_error());
				$righeConsolatoComuneSede=mysql_num_rows($risultatoControllo);
				
				/*con questa query controllo il contenuto della tabella di raccordo 'AltreInfoRango', in modo tale che, se l'utente aggiorna con F5 la pagina daAggiornare.php, il programma non fa la insert degli stessi dati più volte -> vedi condizione if($righeAltreinfoRango==0) */
				$queryControllo2="select * from AltreinfoRango";
				$risultatoControllo2=mysql_query($queryControllo2)
					or die("Impossibile controllare il contenuto della tabella <i>AltreinfoRango</i>: ".mysql_error());
				$righeAltreinfoRango=mysql_num_rows($risultatoControllo2);
				
				
				while($riga=mysql_fetch_row($risultato))
				{
					echo("<tr>
							<td valign='middle'><div align='center'>".$riga[0]."</div></td> ");
						if($riga[1]!=$riga[2])
						{
							echo("<td><div align='center'>".$riga[1]."<br><b><font color='red'>".$riga[2]." (tab. ministeriale)</font></b></div></td>");
							
							/* ora popolo la tabella di raccordo per l'eventuale successivo aggiornamento del campo consolato della tabella consola */
							if($righeConsolatoComuneSede==0)
							{
								$consolatoDaInserire=addslashes($riga[1]);
								$comunesedeDaInserire=addslashes($riga[2]);
								$insert="insert into ConsolatoComuneSede values ($riga[0],'$consolatoDaInserire','$comunesedeDaInserire')";
								$risultatoInsert=mysql_query($insert);
									//or die("Impossibile inserire i dati nella tabella <i>ConsolatoComuneSede</i>: ".mysql_error());
							}
						}							
						else
						{
							echo("<td valign='middle'><div align='center'>".$riga[1]."</div></td>");
						}
						
						if($riga[3]!=$riga[4])
						{
							echo("<td><div align='center'>".$riga[3]."<br><b><font color='red'>".$riga[4]." (tab. ministeriale)</font></b></div></td>");
							
							/* ora popolo la tabella di raccordo per l'eventuale successivo aggiornamento del campo AltreInfo della tabella consola */
							if($righeAltreinfoRango==0)
							{
								$altreinfoDaInserire=addslashes($riga[3]);
								$rangoDaInserire=addslashes($riga[4]);
								$insert="insert into AltreinfoRango values ($riga[0],'$altreinfoDaInserire','$rangoDaInserire')";
								$risultatoInsert=mysql_query($insert);
									//or die("Impossibile inserire i dati nella tabella <i>AltreinfoRango</i>: ".mysql_error());
							}
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
					echo("<form name='cliente' action='allineamento.php' method='POST'>");
						echo("<input type='submit' name='go2' value='Allinea tabella locale con tabella ministeriale'>");
					echo("</form>");
				echo("</div>");
			}
			else
			{
				echo("<br><br>");
				echo("<h3><font color='red'>Non sono presenti consolati con dati disallineati rispetto alla tabella ministeriale</font></h3>");
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