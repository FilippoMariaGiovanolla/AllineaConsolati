<html>
	<head>
		<title>Record gi&agrave; aggiornati</title>
	</head>
	<body>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
			@mysql_select_db('consolati')
				or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
				
			/* a parita'  di codiceConsolato, devo controllare i record della tabella consola che hanno valori identici rispetto alla tabella ministeriale nei campi consolato e altreInfo:
			
			consola.CodMinisteriale=consolatiministero.CodiceConsolato
			consola.consolato=consolatiministero.ComuneSede
			consola.AltreInfo=consolatiministero.Rango
			*/	
				
			echo("<h3><div align='center'>Consolati presenti sia nella tabella locale che nella tabella ministeriale con dati gi&agrave; allineati<br>
			          <font size=3 color='blue'>Confronto effettuato sui campi <i>CodMinisteriale</i>, <i>Consolato</i> e <i>AltreInfo</i> della tabella <i>Consola</i> del database locale.</font></div></h3>");
			echo('<br>
				  <table border=0 width="100%">
					<tr>
						<td width="50%"><a href="allineamentoConsolati.php">Torna alla pagina di allineamento consolati</a></td>
						<td width="50%"><div align="right"><a href="index.php">Torna alla pagina iniziale</a></div></td>
					</tr>
				  </table>
				');
			echo("<br>");
			$ConsolatiPresenti=0;
			echo("<table border=1 align='center'>");
			echo("<tr>
					<td><div align='center'><b>CodiceCons</b></div></td>
					<td><div align='center'><b>Consolato</b></div></td>
					<td><div align='center'><b>Presso</b></div></td>
					<td><div align='center'><b>AltreInfo</b></div></td>
					<td><div align='center'><b>ZipCode</b></div></td>
					<td><div align='center'><b>IstatComune</b></div></td>
					<td><div align='center'><b>CodMinisteriale</b></div></td>
					<td><div align='center'><b>DInizioValitita</b></div></td>
					<td><div align='center'><b>DFineValitita</b></div></td>
				  </tr>");
			$query="select codiceconsolato, comunesede, rango
					from consolatiministero";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i consolati dalla tabella <i>ConsolatiMinistero</i>: ".mysql_error());
			while($riga=mysql_fetch_row($risultato))
			{
				$ComuneSede=addslashes($riga[1]);
				$Rango=addslashes($riga[2]);
				$query="select *
						from consola
						where codministeriale=$riga[0] and consolato='$ComuneSede' and altreInfo='$Rango'";
				$risultato2=mysql_query($query)
					or die("Impossibile cercare il consolato ".$riga[0]." nella tabella <i>consola</i>: ".mysql_error());
				while($riga2=mysql_fetch_row($risultato2))
				{
					echo("<tr>
							<td><div align='center'>".$riga2[0]."</div></td>
							<td><div align='center'>".$riga2[1]."</div></td>
							<td><div align='center'>".$riga2[2]."</div></td>
							<td><div align='center'>".$riga2[3]."</div></td>
							<td><div align='center'>".$riga2[4]."</div></td>							
							<td><div align='center'>".$riga2[5]."</div></td>
							<td><div align='center'>".$riga2[6]."</div></td>");
							if($riga2[7]!='0000-00-00')
							{
								$timestamp=strtotime($riga2[7]);
								echo("<td><div align='center'>".date('d/m/Y',$timestamp)."</div></td>");
							}
							else
								echo("<td><div align='center'>00-00-0000</div></td>");
							if($riga2[8]!='0000-00-00')
							{
								$timestamp=strtotime($riga2[8]);
								echo("<td><div align='center'>".date('d/m/Y',$timestamp)."</div></td>");
							}
							else
								echo("<td><div align='center'>00-00-0000</div></td>");
					echo("</tr>");
					$ConsolatiPresenti++;
				}
			}
			echo("</table>");
			echo("<h3>Sono stati rilevati ".$ConsolatiPresenti." consolati presenti sia nella tabella locale che nella tabella ministeriale con dati gi&agrave; allineati</h3>");
			echo('<br>
				  <table border=0 width="100%">
					<tr>
						<td width="50%"><a href="allineamentoConsolati.php">Torna alla pagina di allineamento consolati</a></td>
						<td width="50%"><div align="right"><a href="index.php">Torna alla pagina iniziale</a></div></td>
					</tr>
				  </table>
				');
		?>
	</body>
</html>