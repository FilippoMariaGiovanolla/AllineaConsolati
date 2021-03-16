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
			$queryCliente="select cliente
						   from cliente";
			$risultatoCliente=mysql_query($queryCliente)
				or die("Impossibile estrarre il nome del Cliente in esame: ".mysql_error());
			while($cliente=mysql_fetch_row($risultatoCliente))
				$clienteVisualizzato=$cliente[0];
			$clienteNomeFile=str_replace(" ","_",$clienteVisualizzato);
			$filename="ConsolatiEliminabili".$clienteNomeFile.".xls";
			header ("Content-Type: application/vnd.ms-excel");
			header ("Content-Disposition: inline; filename=$filename");
			echo("Ecco i consolati potenzialmente eliminabili per ".$clienteVisualizzato."<br><br>");
			
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
			
			echo(" <br> ");
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
				echo("<table border=1>");
				echo("
				<tr>					<td><b>CodiceCons</b></td><td><b>Consolato</b></td><td><b>Presso</b></td><td><b>AltreInfo</b></td><td><b>ZipCode</b></td><td><b>IstatComune</td></b><td><b>CodMinisteriale</td></b><td><b>DInizioValidita</td></b><td><b>DFineValidita</td></b>
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
			
			mysql_close($conn);
		?>
	</body>
</html>