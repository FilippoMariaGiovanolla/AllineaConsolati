<?php
	$host_name='localhost';
	$user_name='root';
	$conn=@mysql_connect($host_name,$user_name,'')
		or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
	@mysql_select_db('consolati')
		or die ("Impossibile selezionare il database <i>Consolati</i>, chiudere il programma e riprovare");
	
	//inserisco il nome del Cliente nella tabella cliente
	$clienteDigitato=strtoupper($_POST["cliente"]);
	$cliente=addslashes($clienteDigitato);
	$query="update cliente set cliente='$cliente'";
	$risultato=mysql_query($query)
		or die("Impossibile aggiornare contenuto della tabella <i>Cliente</i>: ".mysql_error());	
		
	// controllo che non ci siano stati errori nell'upload (codice = 0) 
	if ($_FILES['uploadfile']['error'] == 0)
	{
		// upload ok
		// copio il file dalla cartella temporanea a quella di destinazione mantenendo il nome originale 
		copy($_FILES['uploadfile']['tmp_name'], "C:/Program Files (x86)/EasyPHP 2.0b1/www/AllineamentoConsolati/".$_FILES['uploadfile']['name']) or die("Impossibile caricare il file");
	    echo "Il file &egrave; stato correttamente importato sul server<br><br>";
	    // upload terminato, stampo alcune info sul file
		//echo "Nome file: ".$_FILES['uploadfile']['name']."<br>";
		//echo "Dimensione file: ".$_FILES['uploadfile']['size']."<br>";
		//echo "Tipo MIME file: ".$_FILES['uploadfile']['type'];
    }
    else
    {
	   // controllo il tipo di errore
	   if ($_FILES['uploadfile']['error'] == 2)
	   {
		// errore, file troppo grande (> 1MB)
		die("Errore, file troppo grande: il massimo consentito &egrave; 1MB");
       }
	   else
	   {
		// errore generico
		die("Errore, impossibile caricare il file");
	   }
    }

	//query che mi permette di capire se la tabella consola è già popolata
	$query1="SELECT *
			  FROM consola
			  limit 1";
	$risultato1=mysql_query($query1)
		or die("Verifica composizione tabella <i>Consola</i> fallita: ".mysql_error());
	$righe1=mysql_num_rows($risultato1);
	
	//Creo una variabile con il file CSV da importare e lo importo, mostrandone poi a video il contenuto
	if ($righe1==0)
		{
			$CSVFile="C:/Program Files (x86)/EasyPHP 2.0b1/www/AllineamentoConsolati/".$_FILES['uploadfile']['name'];
			$importazione=mysql_query("LOAD DATA LOCAL INFILE '" .$CSVFile. "' INTO TABLE consola FIELDS TERMINATED BY '	'")
				or die ("Impossibile caricare i dati nella tabella <i>Consola</i>");
			$query="select * 
					from consola";
			$risultato=mysql_query($query)
				or die("Impossibile recuperare il contenuto della tabella <i>Consola</i>: ".mysql_error());
			$colonne=mysql_num_fields($risultato)
				or die("Impossibile calcolare quante colonne ha la tabella <i>Consola</i>: ".mysql_error());
			echo("<div align='center'>");
			echo("<b>Ecco il contenuto dell'archivio importato per il Cliente ".$clienteDigitato.":</b><br><br>");
			echo('<br><br>
				  <table border=0 width="100%">
					<tr>
						<td width="50%"><a href="allineamentoConsolati.php">Accedi alla pagina di allineamento consolati</a></td>
						<td width="50%"><div align="right"><a href="index.php">Torna alla pagina iniziale</a></div></td>
					</tr>
				  </table>
				');
			echo("<br>");
			echo("<table border=1>");
				echo("
				<tr><td><b>CodiceCons</b></td><td><b>Consolato</b></td><td><b>Presso</b></td><td><b>AltreInfo</b></td><td><b>ZipCode</b></td><td><b>IstatComune</td></b><td><b>CodMinisteriale</td></b><td><b>DInizioValidita</td></b><td><b>DFineValidita</td></b>
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
			echo("</div>");
			echo('<br><br>
				  <table border=0 width="100%">
					<tr>
						<td width="50%"><a href="allineamentoConsolati.php">Accedi alla pagina di allineamento consolati</a></td>
						<td width="50%"><div align="right"><a href="index.php">Torna alla pagina iniziale</a></div></td>
					</tr>
				  </table>
				');
		}
	else
	   {
		echo("Ma la tabella 'Consola' non &egrave; vuota, per cui non &egrave; possibile caricare nuovi consolati.<br><br>");
		echo('<a href="CancellazioneConsolati.php">Cancellazione tabella <i>Consola</i> caricata</a>');
	   }
	mysql_close($conn);
	
?>