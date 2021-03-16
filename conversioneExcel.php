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
			$queryCliente="select cliente
						   from cliente";
			$risultatoCliente=mysql_query($queryCliente)
				or die("Impossibile estrarre il nome del Cliente in esame: ".mysql_error());
			while($cliente=mysql_fetch_row($risultatoCliente))
				$clienteVisualizzato=$cliente[0];
			$NumConsolatiDaAggiornare=$_POST["numDaAggiornare"];
			$clienteNomeFile=str_replace(" ","_",$clienteVisualizzato);
			$filename="AllineamentoConsolati".$clienteNomeFile.".xls";
			header ("Content-Type: application/vnd.ms-excel");
			header ("Content-Disposition: inline; filename=$filename");			
			$query="select a.codMinisteriale, a.consolato, b.comunesede, a.altreinfo, b.rango
					from consola a, consolatiministero b
					where a.codministeriale=b.codiceconsolato and (a.consolato!=b.comunesede or a.altreinfo!=b.rango)";
			$risultato=mysql_query($query)
				or die("Impossibile estrarre i consolati con dati da aggiornare: ".mysql_error());
			echo("<h3>Sono stati rilevati <u>".$NumConsolatiDaAggiornare." consolati con dati da aggiornare</u> per ".$clienteVisualizzato."</h3>");
			echo("<table border=1 align='center'>
					<tr>
						<td><div align='center'><b>CodMinisteriale</b></div></td>
						<td><div align='center'><b>Consolato</b></div></td>
						<td><div align='center'><b>AltreInfo</b></div></td>
					</tr>");
			while($riga=mysql_fetch_row($risultato))
			{
				echo("<tr>
						<td valign='middle'><div align='center'>".$riga[0]."</div></td> ");
					if($riga[1]!=$riga[2])
						echo("<td><div align='center'>".$riga[1]."<br><b><font color='red'>".$riga[2]." (tab. ministeriale)</font></b></div></td>");
					else
						echo("<td valign='middle'><div align='center'>".$riga[1]."</div></td>");
					if($riga[3]!=$riga[4])
						echo("<td><div align='center'>".$riga[3]."<br><b><font color='red'>".$riga[4]." (tab. ministeriale)</font></b></div></td>");
					else
						echo("<td valign='middle'><div align='center'>".$riga[3]."</div></td>");
					echo("</tr>");
			}
			echo("</table>");
			echo("<br><br>");			
			mysql_close($conn);
		?>
	</body>
</html>