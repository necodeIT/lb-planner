<?php
	require_once(root.'/snippets/db.php');
	function genExtRef($text,$href){
		echo "<a class='extref btn' href='$href'><span>$text </span>";
		include("./resources/extref.svg");
		echo '</a>';
	}
	function genThemeCard($theme){
		global $context_theme;
		if($theme==$context_theme){
			$current = ' current';
		}else{
			$current = '';
		}
		$_tmp = $context_theme;
		$context_theme = $theme;
		echo "<a class='card$current' href='./?";
			echo genContextURLParams();
			echo '\'>';
			include("./resources/theme-$theme.svg");
			EDS('theme_'.$theme);
		echo '</a>';
		$context_theme = $_tmp;
	}
	function genLearnCard($name,$i){
		echo '<a class="card learn" href="/docs/'.genContextURLParams().'#'.$name.'">';
			include(root."/resources/feature_$name.svg");
			echo '<h4>'.GDS('title_learn'.$i).'</h4>'.
				'<p>'.GDS('p_learn'.$i).'</p>'.
				'<span>'.GDS('lnk_learn').'</span>'.
		'</a>';
	}
	function genDownloadCard($os){
		global $context_os;
		if($os==$context_os){
			$current=' current';
		}else{
			$current='';
		}
		echo "<a class='card dl$current' id='${os}dlcard' href='/download?os=$os'>";
			echo '<h4 class="sectionheader">'.GDS('title_download').'</h4>';
			include(root."/resources/os_$os.svg");
			echo "<span>$os</span>";
		echo '</a>';
	}
	function genFeatureSection($title,$paragraph){
		echo '<section class="screenpage pagetype2">'.
			'<img class="card" src="/resources/example_cal.png" alt="example screenshot"/>'.
			"<h2>$title</h2>".
			"<p>$paragraph</p>".
		'</section>';
	}
	function setContext(){
		global $contextURLParams;
		_setContext_helper('theme',array('light','dark','ocean','sakura'));
		_setContext_helper('lang',array('EN','DE'));
		_setOSContext();
		$contextURLParams = genContextURLParams();
	}
	function _setContext_helper($varname,$varvals){
		$globalname = 'context_'.$varname;
		if(array_key_exists($globalname,$GLOBALS)){
			;
		}else if(!array_key_exists($varname,$_GET)){
			$GLOBALS[$globalname]=$varvals[0];
			return;
		}else{
			$GLOBALS[$globalname]=$_GET[$varname];
		}
		//checking if value is valid and setting to default if not
		if(!in_array($GLOBALS[$globalname],$varvals)){
			$GLOBALS[$globalname] = $varvals[0];
		}
	}
	function _setOSContext(){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		
		$osarr = array();
		
		if(preg_match('/macintosh|mac os x/i',$user_agent)){
			array_push($osarr,'mac');
		}else if(preg_match('/linux|ubuntu/i',$user_agent)){
			array_push($osarr,'linux');
		}else{
			array_push($osarr,'windows');
		}
		
		_setContext_helper('os',$osarr);
	}
	/* generates URL parameters that correspond with the current context */
	function genContextURLParams(){
		global $context_theme,$context_lang,$context_os;
		return "theme=$context_theme&lang=$context_lang&os=$context_os";
	}
?>