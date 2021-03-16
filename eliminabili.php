<html>
	<head>
		<title>Consolati potenzialmente eliminabili</title>
	</head>
	<body>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
			@mysql_select_db('consolati')
				or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
			
			
			//estrazione dei consolati che non sono presenti in assoluto nella tabella ministeriale
			$query="select a.*
				    from consola a
					where not exists
					( 
						select *
						from consolatiministero b
						where a.codicecons=b.codiceconsolato
					)
					and not exists
					(
						select *
						from consolatiministero b
						where a.codministeriale=b.codiceconsolato
					)";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i dati dei consolati cancellabili: ".mysql_error());
			$righe=mysql_num_rows($risultato);
			$colonne=mysql_num_fields($risultato);
			if($righe>0)
			{
				echo("<h3><div align='justify'>I seguenti consolati sono potenzialmente eliminabili, in quanto i rispettivi codici <i>CodiceCons</i> e <i>CodMinisteriale</i> non sono presenti nella tabella ministeriale:</div></h3>");
				echo('<table border=0 width="100%">
						<tr>
							<td width="50%"><a href="allineamentoConsolati.php">Torna alla pagina di allineamento consolati</a></td>
							<td width="50%"><a href="index.php"><div align="right">Torna alla pagina iniziale</div></a></td>
						</tr>
					  </table><br>');
				echo("<div align='center'>");
					echo("<form name='cliente' action='conversioneExcelEliminabili.php' method='POST'>");
						echo("<input type='submit' name='go' value='Esporta intero risultato in Excel'>");
					echo("</form>");
				echo("</div>");
				echo("<table border=1>");
				echo("
				<tr>					<td><b>CodiceCons</b></td><td><b>Consolato</b></td><td><b>Presso</b></td><td><b>AltreInfo</b></td><td><b>ZipCode</b></td><td><b>IstatComune</td></b><td><b>CodMinisteriale</td></b><td><b>DInizioValidita</td></b><td><b>DFineValidita</td></b>
				</tr>
				");
				while($riga=mysql_fetch_row($risultato))
				{
					echo("<tr>");
					for($j=0;$j<$colonne;$j++)
						if((($j==7) or ($j==8)) and ($riga[$j]!='0000-00-00'))
						{
							$timestamp=strtotime($riga[$j]);
							echo("<td>".date('d/m/Y',$timestamp)."</td>");
						}
						elseif((($j==7) or ($j==8)) and ($riga[$j]=='0000-00-00'))
							echo("<td>00-00-000</td>");
						else
							echo("<td>".$riga[$j]."</td>");
					echo("</tr>");
				}
				echo("</table>");
			}
			// fine estrazione dei consolati che non sono presenti in assoluto nella tabella ministeriale
			
			
			// inizio estrazione dei consolati che, considerando il campo 'CodiceConsolato' della tabella ConsolatiMinistero, non hanno corrispondenza con il campo consola.codicecons oppure con il campo consola.codministeriale
			$query="select a.*
				    from consola a
					where exists
					( 
						select *
						from consolatiministero b
						where a.codicecons=b.codiceconsolato
					)
					and not exists
					(
						select *
						from consolatiministero b
						where a.codministeriale=b.codiceconsolato
					)";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i dati dei consolati che hanno corrispondenza nella tabella ministeriale rispetto al <i>CodiceCons</i>, ma non hanno corrispondenza nella tabella ministeriale rispetto al <i>CodMinisteriale</i>: ".mysql_error());
			$righe1=mysql_num_rows($risultato);
			
			$query2="select a.*
				    from consola a
					where not exists
					( 
						select *
						from consolatiministero b
						where a.codicecons=b.codiceconsolato
					)
					and exists
					(
						select *
						from consolatiministero b
						where a.codministeriale=b.codiceconsolato
					)";
			$risultato2=mysql_query($query2)
				or die("Impossibile estrarre i dati dei consolati che hanno corrispondenza nella tabella ministeriale rispetto al <i>CodiceCons</i>, ma non hanno corrispondenza nella tabella ministeriale rispetto al <i>CodMinisteriale</i>: ".mysql_error());
			$righe2=mysql_num_rows($risultato2);
			
			$righeTotali=$righe1+$righe2;
			
			if($righeTotali>0)
			{
				echo("<h3><div align='justify'>I seguenti consolati potrebbero essere eliminabili, ma occorre prima accedere alle pagine di <a href='daAggiornare.php'>visualizzazione dei record della tabella locale con dati da aggiornare</a> e di <a href='daInserire.php'>visualizzazione dei record da inserire nella tabella locale</a>, in quanto questo elenco potrebbe variare dopo aver aggiornato gli archivi (anche manualmente):</div></h3>");
				/*echo('<table border=0 width="100%">
						<tr>
							<td width="50%"><a href="allineamentoConsolati.php">Torna alla pagina di allineamento consolati</a></td>
							<td width="50%"><a href="index.php"><div align="right">Torna alla pagina iniziale</div></a></td>
						</tr>
					  </table><br>');*/
				echo("<table border=1>");
				echo("
				<tr><td><b>CodiceCons</b></td><td><b>Consolato</b></td><td><b>Presso</b></td><td><b>AltreInfo</b></td><td><b>ZipCode</b></td><td><b>IstatComune</td></b><td><b>CodMinisteriale</td></b><td><b>DInizioValidita</td></b><td><b>DFineValidita</td></b>
				</tr>
				");
				while($riga=mysql_fetch_row($risultato))
				{
					echo("<tr>");
					for($j=0;$j<9;$j++)
						if((($j==7) or ($j==8)) and ($riga[$j]!='0000-00-00'))
						{
							$timestamp=strtotime($riga[$j]);
							echo("<td>".date('d/m/Y',$timestamp)."</td>");
						}
						elseif((($j==7) or ($j==8)) and ($riga[$j]=='0000-00-00'))
							echo("<td>00-00-000</td>");
						else
							echo("<td>".$riga[$j]."</td>");
					echo("</tr>");
				}
				while($riga=mysql_fetch_row($risultato2))
				{
					echo("<tr>");
					for($j=0;$j<9;$j++)
						if((($j==7) or ($j==8)) and ($riga[$j]!='0000-00-00'))
						{
							$timestamp=strtotime($riga[$j]);
							echo("<td>".date('d/m/Y',$timestamp)."</td>");
						}
						elseif((($j==7) or ($j==8)) and ($riga[$j]=='0000-00-00'))
							echo("<td>00-00-000</td>");
						else
							echo("<td>".$riga[$j]."</td>");
					echo("</tr>");
				}
				echo("</table>");
			}			
			// fine estrazione dei consolati che, considerando il campo 'CodiceConsolato' della tabella ConsolatiMinistero, non hanno corrispondenza con il campo consola.codicecons oppure con il campo consola.codministeriale
			echo("<br><div align='center'>");
					echo("<form name='cliente' action='conversioneExcelEliminabili.php' method='POST'>");
						echo("<input type='submit' name='go' value='Esporta intero risultato in Excel'>");
					echo("</form>");
				echo("</div>");
			mysql_close($conn);
		?>
		<table border=0 width="100%">
			<tr>
				<td width="50%"><a href="allineamentoConsolati.php">Torna alla pagina di allineamento consolati</a></td>
				<td width="50%"><a href="index.php"><div align="right">Torna alla pagina iniziale</div></a></td>
				
			</tr>
		</table>
	</body>
</html>