<?php
	$version = '1.0';


	if (!empty($_GET["mysql"]) or !empty($_GET["files"])){
		if ( !shell_exec("type type")) { $err = "Weak PHP!"; die; }
		$dir = (!empty($_GET["folder"]) ? $_GET["folder"] : "backup");
		$configFile = "./core/config/config.inc.php";
		if (file_exists($configFile)) {
		    include($configFile);
		    $date = date("Ymd-His");
		    $targetSql = "$dir/{$dbase}_{$date}_mysql.sql";
		    $targetTar = "$dir/{$dbase}_{$date}_files.tar";
		    system("mkdir $dir");
		    
		    if (!empty($_GET["mysql"])){
		    	system("mysqldump --host=$database_server --user=$database_user --password=$database_password --databases $dbase --no-create-db --default-character-set=utf8 --result-file={$targetSql}");
		    }
		    
		    if (!empty($_GET["files"])){
		    	system("tar cf {$targetTar} --exclude=$dir ./");
		    }
		    
		    $backup = true;
		    
		}
		
		
		if (file_exists($targetSql)) {
			$mysql_name = basename($targetSql);
			$mysql_size = round(filesize($targetSql) / 1000000, 2);
		}
		if (file_exists($targetTar)) {
			$files_name = basename($targetTar);
			$files_size = round(filesize($targetTar) / 1000000, 2);
		}
		
		
		if (!empty($_GET["cron"])) {
			echo ($backup == true) ? 'Finished' : 'Error';
		}
		
	}
	
	
	if (!empty($_GET["remove"])){
		unlink(basename(__FILE__));
		
		if (empty($_GET["update"])) {
			header("Location: /");
		}else {
			header("Location: install.php");
		}
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
	    
	    <?php if ($backup != true): ?>
	    <section class="container">
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
	    </section>
	    
	    <?php else: ?>
	    
	    <section class="result container">
	    	<h2>Backup Finished!</h2>
	    	<p>
	    		<span>MySQL Database:</span> <?php echo (!empty($mysql_name) ? '<a href="'.$targetSql.'" target="_blank">'.$mysql_name.'</a> ('.$mysql_size.' MB)' : 'No Backup!'); ?><br />
	    		<span>Files:</span> <?php echo (!empty($files_name) ? '<a href="'.$targetTar.'" target="_blank">'.$files_name.'</a> ('.$files_size.' MB)' : 'No Backup!'); ?>
	    	</p>
	    	<p>
	    		<a href="?remove=1" class="btn">Remove Backup-Script</a>
	    		<?php if (file_exists("install.php")): ?><a href="?remove=1&update=1" class="btn">Remove Backup-Script & Update MODX</a><?php endif; ?>
	    		<?php if (file_exists("install.php")): ?><a href="install.php" class="btn">Update MODX</a><?php endif; ?>
	    	</p>
	    </section>
	    <?php endif; ?>
	    
	    
	</body>
</html>

<?php endif; ?>