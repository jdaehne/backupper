<?php
	
	set_time_limit(0);

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

	$version = '1.4';

	if (!empty($_GET["mysql"]) or !empty($_GET["files"])){
		$dir = (!empty($_GET["folder"]) ? $_GET["folder"] : "backup");
		$configFile = "./core/config/config.inc.php";
		if (file_exists($configFile)) {
			include($configFile);
			$date = date("Ymd-His");
			$targetSql = "$dir/{$dbase}_{$date}_mysql.sql";
			$targetTar = "$dir/{$dbase}_{$date}_files.tar";
			$targetCom = "$dir/{$dbase}_{$date}_combined.tar";
			system("mkdir $dir");

			if (!empty($_GET["mysql"])){
				
				system("mysqldump --host=$database_server --user=$database_user --password=$database_password --databases $dbase --no-create-db --default-character-set=utf8 --result-file={$targetSql}");
				
				//If no mysqldump was possible try:
				if (file_exists($targetSql) or filesize($targetSql) <= 0) {
					system(sprintf('mysqldump --no-tablespaces --opt -h%s -u%s -p"%s" %s --result-file=%s', $database_server, $database_user, $database_password, $dbase, $targetSql));
				}

			}

			if (!empty($_GET["files"])){
				system("tar cf {$targetTar} --exclude=$dir --exclude=".basename(__FILE__)." ./");
			}

			//Combine SQL and Files in one archive
			if (file_exists($targetSql) and file_exists($targetTar) and filesize($targetSql) > 0) {
				system("tar cf {$targetCom} {$targetSql} {$targetTar}");
			}

			$backup = true;

		}
		
		echo $mysql;
		
		if (file_exists($targetSql) and filesize($targetSql) > 0) {
			$mysql_name = basename($targetSql);
			$mysql_size = round(filesize($targetSql) / 1000000, 2);
		}
		if (file_exists($targetTar)) {
			$files_name = basename($targetTar);
			$files_size = round(filesize($targetTar) / 1000000, 2);
		}
		if (file_exists($targetCom)) {
			$combi_name = basename($targetCom);
			$combi_size = round(filesize($targetCom) / 1000000, 2);
		}
		
		
		if (!empty($_GET["cron"])) {
			
			$infos = json_encode(
				array(
					"dir" => $dir,
					"sql" => $mysql_name,
					"tar" => $files_name,
					"com" => $combi_name,
					)
				);


			echo ($backup == true) ? $infos : 'Error';
		}
		
	}
	
	
	
	
	if (!empty($_GET["tarfile"])){
		system("tar xf " . $_GET["tarfile"]);
		$extract = true;
	}
	
	
	
	
	//Delete Backup-Script
	if (!empty($_GET["remove"])){
		unlink(basename(__FILE__));
		
		//Backups löschen
		if (!empty($_GET["removebackup"]) and !empty($_GET["dir"])) {
			$dir = $_GET["dir"];
			foreach(glob("$dir/*") as $file) {
				unlink($file);
			}
			rmdir($dir);
		}
		
		header("Location: /");
	}
	
	//Delete Tar-File
	if (!empty($_GET["removetarfile"])){
		unlink($_GET["tarfile"]);
	}


?>


<?php if (empty($_GET["cron"])): ?>

<!DOCTYPE html>
<html>
	<head>
	    <title>MODX Backupper v<?php echo $version; ?></title>
	    <meta charset="utf-8">
	    <style type="text/css">
			h2,p{color:#555}.btn,button,label{cursor:pointer}.btn,button,header .logo,header span,section.result span{display:inline-block}article,aside,audio,b,body,canvas,dd,details,div,dl,dt,em,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,header,hgroup,html,i,img,label,li,mark,menu,nav,ol,p,section,span,strong,summary,table,tbody,td,tfoot,th,thead,time,tr,u,ul,video{margin:0;padding:0;border:0;outline:0;vertical-align:top;background:0 0;font-size:100%}body{background:#f2f2f2;font-size:14px}*{font-family:"Lucida Grande","Lucida Sans Unicode",Verdana,Arial,Helvetica,sans-serif}h2{font-size:20px;margin-bottom:30px}a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:0 0;text-decoration:none;color:#4e96cc}a:hover{text-decoration:underline}p{margin-bottom:1em;line-height:1.6}.btn,button,header *{color:#fff;font-size:16px}.container{margin:10px 30px}.btn,button{background:#41a796;padding:10px 20px;border-radius:5px;transition:all .4s;border:0;margin-top:20px;margin-right:20px}.btn:hover,button:hover{text-decoration:none;background:#378f80}input[type=text]{padding:5px 10px;width:150px}header{background:#3b515e;padding:10px 30px;margin-bottom:60px}header .logo{background:url(manager/templates/default/images/modx-icon-color.svg) center center no-repeat;text-indent:-999999px;width:30px;height:30px}header span{line-height:30px;padding-left:15px}header .btn.github{float:right;padding:5px 10px;margin:0}section.result span{min-width:140px}
	    </style>
	</head>
	<body>
	    <header>
	    	<a class="logo" href="?">MODX Backupper</a><span>MODX Backupper <?php echo $version; ?></span>
	    	<a class="btn github" href="https://github.com/jdaehne/backupper" target="_blank">Github</a>
	    </header>
	    
	    <?php
			if (is_array($err)) {
				foreach ($err as $msg){
					echo '<p>'.$msg.'</p>';
				}
			}
	    ?>
	    
	    <?php if ($backup != true and $extract != true): ?>			
		    <section class="container">
		    	<?php
		    		if (empty($_GET["notarlist"]))
			    	foreach (glob("*.tar") as $filename) {
						$tarfiles = '<input type="radio" name="tarfile" id="tarfile" value="' . $filename . '" checked> ' . $filename . ' (' . round(filesize($filename) / 1000000, 2) . ' MB)';
					}
				?>
				
				<?php if (!empty($tarfiles)): ?>
					<h2>Found Tarfile(s): Extract Archive?</h2>
					<form method="get">	
						<p><?php echo $tarfiles; ?></p>
						<p>
							<input type="checkbox" name="removetarfile" id="removetarfile" value="1" checked><label for="removetarfile"> Remove Archive</label><br />
						</p>
						<button type="submit">Extract</button> <a href="?notarlist=1">No, go to Backup.</a>
					</form>
				<?php else: ?>
			    	<h2>Choose Elements to backup</h2>
			    	<form method="get">
			    		<p>
			    			<input type="checkbox" name="mysql" id="mysql" value="1" checked><label for="mysql"> MySQL Database</label><br />
			    			<input type="checkbox" name="files" id="files" value="1" checked><label for="files"> Files</label>
			    		</p>
			    		<p>
			    			Folder to place Files: <input type="text" name="folder" value="backup"><br>
							<button type="submit">Start Backup</button>
			    		</p>
			    			
			    	</form>
		    	<?php endif; ?>
		    </section>
	    <?php else: ?>
		    <section class="result container">
		    	<?php if ($backup == true): ?>		    
			    	<h2>Backup Finished!</h2>
			    	<p>
			    		<span>MySQL Database:</span> <?php echo (!empty($mysql_name) ? '<a href="'.$targetSql.'" target="_blank" download>'.$mysql_name.'</a> ('.$mysql_size.' MB)' : 'No Backup!'); ?><br />
			    		<span>Files:</span> <?php echo (!empty($files_name) ? '<a href="'.$targetTar.'" target="_blank" download>'.$files_name.'</a> ('.$files_size.' MB)' : 'No Backup!'); ?>
			    		<?php echo (!empty($combi_name) ? '<br /><span>MySQL & Files:</span> <a href="'.$targetCom.'" target="_blank" download>'.$combi_name.'</a> ('.$combi_size.' MB)' : ''); ?>
			    	</p>
			    	<p>
			    		<a href="?remove=1" class="btn">Remove Backup-Script</a>
			    		<a href="?remove=1&removebackup=1&dir=<?php echo $dir; ?>" class="btn">Remove Backup-Script & Backup-Files</a>
			    	</p>
		    	<?php else: ?>
			    	<h2>Extract Files Finished!</h2>
			    	<p>
			    		<a href="?remove=1" class="btn">Remove Backup-Script</a>
			    		<a href="?remove=1&removebackup=1&dir=<?php echo $dir; ?>" class="btn">Remove Backup-Script & Backup-Files</a>
			    	</p>		    	
		    	<?php endif; ?>
		    </section>
	    <?php endif; ?>
	    
	    
	</body>
</html>

<?php endif; ?>
