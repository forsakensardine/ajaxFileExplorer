<?php
function shortName($name, $length=13) {
	if (strlen($name) > $length)
		$name = substr($name, 0, $length-2).'..';
	return $name;
}
$exts = array('picture', 'font',  'page_white_excel', 'page_white_word', 'page_white_code', 'page_white_php', 'film', 'music', 'application_xp_terminal', 'page_white_text', 'box');
$hiddenFiles = array('.', '..', 'index.php', 'index.html', 'index.htm', '.htaccess');

function getIcon($ext) {
	$ext = strtolower($ext);
	if (in_array($ext, array('jpg', 'png', 'bmp', 'gif', 'jpeg')))
		return 'picture';
	if (in_array($ext, array('avi', 'mp4', 'mkv', 'divx', 'xvid')))
		return 'film';
	if (in_array($ext, array('mp3', 'wav', 'wma', 'flac', 'ogg')))
		return 'music';
	if (in_array($ext, array('exe', 'msi', 'bat')))
		return 'application_xp_terminal';
	if (in_array($ext, array('txt', 'js', 'css', 'log')))
		return 'page_white_text';
	if ($ext == 'php') return 'page_white_php';
	if ($ext == 'css' || $ext == 'html') return 'page_white_code';
	if ($ext == 'doc' || $ext == 'docx') return 'page_white_word';
	if ($ext == 'xls' || $ext == 'xlsx' || $ext == 'csv') return 'page_white_excel';
	if ($ext == 'ttf' || $ext == 'ttc' || $ext == 'otf') return 'font';
	if (in_array($ext, array('zip', 'rar', 'ace', '7z', 'tar', 'gz')))
		return 'box';
}
function array_path($ar, $count) {
	$ret = '_files/';
	for ($i=0; $i<=$count; ++$i) {
		$ret .= $ar[$i];
		if ($i <= $count) $ret .= '/';
	}
	return rtrim(preg_replace('~//~', '/', $ret), '/');
}
if (isset($_POST['ajax'])) {
	$dir = $_POST['dir'];
	if (preg_match('/^_files/', $dir)) {
		if ($d = opendir($dir)) {
			$_dirs = array();
			$_files = array();
			if (count(explode('/', $dir)) > 1) {
				// $_dirs[] = '[retour]';
			}
			while ($ent = readdir($d)) {
				if (in_array($ent, $hiddenFiles)) continue;
				if (is_dir($dir.'/'.$ent)) {
					$_dirs[] = $ent;
				} else {
					$_files[] = $ent;
				}
			}
			$title = explode('/', preg_replace('/_files/', '', $dir));
			echo '
			<ul class="breadcrumb">
				<li><a href="_files" class="loaddir">[racine]</a></li>';
				for ($i=0,$max=count($title); $i<$max; ++$i) {
					if ($i+1 < count($title)) {
						echo '<li> <a class="loaddir" href="'.array_path($title, $i).'">'.$title[$i].'</a> </li>';
						echo ' <span class="divider"> / </span> ';
					} else {
						echo '<li class="active">'.$title[$i].'</li>';
					}
				}
			echo '</ul>';
			echo '<ul>';
			foreach ($_dirs as $d) {
				if ($d != '[retour]') {
					echo '<li class="dir">
						<a href="'.$dir.'/'.$d.'" class="loaddir">
							<i class="icn folder"></i>
							'.$d.'
						</a>
					</li>';
				} else {
					$back = explode('/', $dir);
					array_pop($back);
					echo '<li class="dir foldable"><a href="'.implode('/', $back).'" class="loaddir">'.$d.'</a></li>';
				}
			}
			foreach ($_files as $f) {
				$ext = $ext = pathinfo($f, PATHINFO_EXTENSION);
				echo '<li class="file">
					<a href="'.$dir.'/'.$f.'">
						<i class="icn '.getIcon($ext).'"></i>
						'.$f.'
					</a>
				</li>';
			}
			echo '</ul>
			<script type="text/javascript">$("li.dirselect").removeClass("active"); $(".dir_'.md5($dir).'").addClass("active");</script>';
		} else {
			echo "Impossible d'ouvrir le dossier $dir";
		}
	} else {
		echo "Can't access $dir";
	}
	die();
}
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Fichiers...</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Le styles -->
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<style type="text/css">
		body { padding-top: 60px; padding-bottom: 40px; }
		#explorer { list-style: none; margin: 0; padding: 0; }
		#explorer li {
			line-height: 16px;
			margin-top: 5px;
		}
		i.icn { width: 16px; height: 16px; vertical-align: middle; display: inline-block; _display: inline; *display: inline; zoom: 1; }
		i.icn.folder { background: url('icons/folder.png') no-repeat center; }<?php
		foreach ($exts as $ex) {
			echo '		i.icn.'.$ex.' { background: url(\'icons/'.$ex.'.png\') no-repeat center; }'."\n";
		}
		?>
		#explorer li.title { text-transform: uppercase; }
		#explorer li.dir { font-weight: bold; }
		#explorer li.file {}

		#dirlist .well { padding: 8px 0 !important; }
	</style>
	<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">

	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" href="http://twitter.github.com/bootstrap/assets/images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="http://twitter.github.com/bootstrap/assets/images/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="http://twitter.github.com/bootstrap/assets/images/apple-touch-icon-114x114.png">
</head>
<body>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="">Fichiers</a>
				<div class="nav-collapse">
					<ul class="nav">
						<li class="active"><a href="#download">Télécharger</a></li>
						<li><a href="#send">Envoyer</a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class="span3" id="dirlist">
				<div class="well hidden-phone">
					<ul class="nav nav-list">
						<li class="nav-header">Dossiers</li>
						<li class="active dirselect dir dir_<?php echo md5('_files'); ?>"><a href="_files" class="loaddir"><i class="icn folder"></i> [racine]</a></li>
						<?php
						$allDirs = array();
						function scan_dir($dir, $lvl=0) {
							global $hiddenFiles, $allDirs;
							$ret = '';
							if ($d = opendir($dir)) {
								while ($f = readdir($d)) {
									if (in_array($f, $hiddenFiles)) continue;
									if (is_dir($dir.'/'.$f)) {
										$allDirs[] = array('dir' => $dir.'/'.$f, 'label' => $f, 'level' => (int)$lvl);
										$ret .= '<ul class="nav nav-list"><li class="dirselect dir dir_'.md5($dir.'/'.$f).' level-'.$lvl.'"><a class="loaddir" href="'.$dir.'/'.$f.'"><i class="icn folder"></i> '.$f.'</a>';
										$ret .= scan_dir($dir.'/'.$f, $lvl+1);
										$ret .= '</ul>';
									}
								}
								closedir($d);
							}
							return $ret;
						}
						$scannedDirs = scan_dir('_files');
						echo $scannedDirs;
						?>
					</ul>
				</div>
				<div class="visible-phone">
					<form class="form form-inline">
						<label for="dirPickerMobile">Dossier</label>
						<select id="dirPickerMobile" style="white-space: pre !important;">
							<?php
							foreach ($allDirs as $opt) {
								echo '<option style="white-space: pre !important;" value="'.$opt['dir'].'">'.str_repeat('&nbsp;', $opt['level']*3).$opt['label'].'</option>';
							}
							?>
						</select>
					</form>
				</div>
			</div>
			<div class="span9">
				<fieldset>
					<legend>Explorateur de fichiers</legend>
					<div id="explorer"></div>
				</fieldset>
			</div>
		</div>
	  	<hr>
	</div> <!-- /container -->

	<!-- Le javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function loadDir(_dir) {
			$("#explorer").load('', { ajax: true, dir: _dir });
		}
		$(function() {
			$("body").on('click', 'a.loaddir', function(e) {
				e.preventDefault();
				/* $(".level-0").not(self.closest('.level-0')).find('ul').stop(true, true).slideUp();
				var targets = self.parent().children("ul");
				targets.stop(true, true).slideToggle(targets.is(':hidden')); //*/
				var self = $(this);
				var href = self.attr('href');
				$("#dirPickerMobile").val(href);
				loadDir(href);
			});
			$("#dirPickerMobile").change(function() {
				loadDir($(this).val());
			});
			loadDir('_files');
			// $(".level-0 ul").hide();
		});
	</script>
	<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.sylvainthrd.fr/piwik//";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
    g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();

</script>
<noscript><p><img src="http://www.sylvainthrd.fr/piwik/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Code -->
</body>
</html>
