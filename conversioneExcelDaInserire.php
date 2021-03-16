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
			$queryCliente="select cliente
						   from cliente";
			$risultatoCliente=mysql_query($queryCliente)
				or die("Impossibile estrarre il nome del Cliente in esame: ".mysql_error());
			while($cliente=mysql_fetch_row($risultatoCliente))
			$clienteVisualizzato=$cliente[0];
			$NumConsolatiDaInserire=$_POST["numDaInserire"];
			$clienteNomeFile=str_replace(" ","_",$clienteVisualizzato);
			$filename="ConsolatiDaInserire".$clienteNomeFile.".xls";
			header ("Content-Type: application/vnd.ms-excel");
			header ("Content-Disposition: inline; filename=$filename");	
			echo("<h3><div align='left'>Consolati non presenti nella tabella locale che sono invece presenti nella tabella ministeriale</div></h3>");
			echo("<div align='justify'><b>Sono stati rilevati ".$NumConsolatiDaInserire." consolati da inserire nella tabella 'Consola' del database locale</b></div>");
			echo("<br><br>");
			//$ConsolatiMancanti=0;
			echo("<table border=1 align='center'>");
			echo("<tr>
					<td><div align='center'><b>CodiceConsolato</b></div></td>
					<td><div align='center'><b>CodStatoAppartenenza</b></div></td>
					<td><div align='center'><b>StatoAppartenenza</b></div></td>
					<td><div align='center'><b>ComuneSede</b></div></td>
					<td><div align='center'><b>Rango</b></div></td>
					<td><div align='center'><b>Indirizzo</b></div></td>
				  </tr>");
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
					echo("<tr>
							<td><div align='center'>".$riga[0]."</div></td>
							<td><div align='center'>".$riga[1]."</div></td>
							<td><div align='center'>".$riga[2]."</div></td>
							<td><div align='center'>".$riga[3]."</div></td>
							<td><div align='center'>".$riga[4]."</div></td>
							<td><div align='center'>".$riga[5]."</div></td>
						  </tr>");
					//$ConsolatiMancanti++;
				}
			}
			echo("</table>");
			/*if($ConsolatiMancanti==0)
			{
				echo("<br>");
				echo("<h3>Tutti i consolati della tabella ministeriale sono presenti anche nella tabella locale</h3>");
			}
			else
			{
				echo("<div align='center'>");
					echo("<form name='cliente' action='conversioneExcelDaInserire.php' method='POST'>");
						echo("<input type='hidden' name='numDaAggiornare' value='$ConsolatiMancanti'>");
						echo("<h3>Totale consolati da inserire nella tabella locale: ".$ConsolatiMancanti."</h3>  ");
						echo("<input type='submit' name='go' value='Esporta risultato in Excel'>");
					echo("</form>");
				echo("</div>");
			}*/
			mysql_close($conn);
		?>
		<!--<br>
		<table border=0 width="100%">
			<tr>
				<td width="50%"><a href="allineamentoConsolati.php">Torna alla pagina di allineamento consolati</a></td>
				<td width="50%"><a href="index.php"><div align="right">Torna alla pagina iniziale</div></a></td>
				
			</tr>
		</table> -->
	</body>
</html>