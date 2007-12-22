<?php
/*
Plugin Name: WP_MonsterID
Version: 1.01
Plugin URI: http://scott.sherrillmix.com/blog/blogger/WP_MonsterID
Description: This plugin generates email specific monster icons for each user based on code and images by <a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a>.
Author: Scott Sherrill-Mix
Author URI: http://scott.sherrillmix.com/blog/
*/

define('WP_MONSTERID_DIR', 'wp-content/plugins/monsterid/');
define('WP_MONSTERID_DIR_INTERNAL', ABSPATH.'wp-content/plugins/monsterid/');
define('WP_MONSTERPARTS_DIR', ABSPATH.'wp-content/plugins/monsterid/parts/');


function monsterid_menu() {
	if (function_exists('add_options_page')) {
		add_options_page('MonsterID Control Panel', 'MonsterID', 1, basename(__FILE__), 'monsterid_subpanel');
	}
}


function monsterid_findparts($partsarray){
	$dir=WP_MONSTERPARTS_DIR;
	$noparts=true;
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (is_file($dir.$file)){
				$partname=explode('_',$file);
				$partname=$partname[0];
				if (array_key_exists($partname,$partsarray)){
					array_push($partsarray[$partname],$file);
					$noparts=false;
				}
			}
		}
	}
	if ($noparts) return false;
	closedir($dh);
	//sort for consistency across servers
	foreach($partsarray as $key => $value) sort($partsarray[$key]);
	return $partsarray;
}

function monsterid_get_options(){
	$monsterID_array=get_option('monsterID');
	if (!isset($monsterID_array['size'])||!isset($monsterID_array['backb'])){
		//Set Default Values Here
		$default_array=array('size'=>65,'backr'=>array(220,255),'backg'=>array(220,255),'backb'=>array(220,255),'legs'=>0,'autoadd'=>1,'gravatar'=>0);
		add_option('monsterID',$default_array,'Options used by MonsterID',false);
		$monsterID_array=$default_array;
	}
	return($monsterID_array);
}

function monsterid_subpanel() {
	echo "<div class='wrap'>";
	if (isset($_POST['submit'])) { //update the monster size option
		$monsterID_options=monsterid_get_options();
		$monstersize=intval($_POST['monstersize']);
		if ($monstersize > 0 & $monstersize < 400){
			$monsterID_options['size']=$monstersize;
		}else{
			echo "<div class='error'><p>Please enter an integer for size. Preferably between 30-200.</p></div>";		
		}
		foreach(array('backr','backg','backb') as $color){//update background color options
			$colorarray=explode('-',$_POST[$color]);
			if (count($colorarray)==1){
				$colorarray[1]=$colorarray[0];
			}
			$colorarray[0]=intval($colorarray[0]);
			$colorarray[1]=intval($colorarray[1]);
			if ($colorarray[0]>=0 & $colorarray[0]<256 & $colorarray[1]>=0 & $colorarray[1]<256){
				$monsterID_options[$color]=$colorarray;
			}else{
				echo "<div class='error'><p>Please enter a range between two integers for the background color (e.g. 230-255) between 1 and 255. For a single color please enter a single value (e.g. white = 255 for r,g and b).</p></div>";		
			}
		}
		//Not using else on the odd chance some weird input gets sent
		if ($_POST['legs'] == 0) $monsterID_options['legs']=0;
		elseif ($_POST['legs'] == 1){
			if(is_writable(WP_MONSTERPARTS_DIR))
				$monsterID_options['legs']=1;
			else{
				echo "<div class='error'>Directory ".WP_MONSTERPARTS_DIR." must be <a href='http://codex.wordpress.org/Changing_File_Permissions'>writeable</a> to use white legs and arms.</div>";
				$monsterID_options['legs']=0;
			}
		}
		if ($_POST['autoadd'] == 0) $monsterID_options['autoadd']=0;
		elseif ($_POST['autoadd'] == 1) $monsterID_options['autoadd']=1;
		if ($_POST['gravatar'] == 0) $monsterID_options['gravatar']=0;
		elseif ($_POST['gravatar'] == 1) $monsterID_options['gravatar']=1;
		update_option('monsterID', $monsterID_options);
		echo "<div class='updated'><p>Options updated (you may need to clear the monster cache to see any effect).</p></div>";
	}elseif (isset($_POST['clear'])){ //clear the monsterid cache
		$dir=WP_MONSTERID_DIR_INTERNAL;
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (is_file($dir.$file) and preg_match('/^.*\.png$/',$file)){
					unlink($dir.$file);
				}
			}
			closedir($dh);
			echo "<div class='updated'><p>Cache cleared.</p></div>";		
		}
	}
	$monsterID_options=monsterid_get_options();
	//count file
	$monsterID_count=0;
	$dir=WP_MONSTERID_DIR_INTERNAL;
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (is_file($dir.$file) and preg_match('/^.*\.png$/',$file)){
				$monsterID_count++;
			}
		}
	}
	//make sure white legs/arms exist
	$dir=WP_MONSTERPARTS_DIR;
	$changed="";
	if ($dh = opendir($dir)&&$monsterID_options['legs']&&is_writable(WP_MONSTERPARTS_DIR)) {
		while (($file = readdir($dh)) !== false) {
			if (is_file($dir.$file) and preg_match('/^(arms|legs|body|hair)_.*\.png$/',$file)){
				if (!file_exists($dir.'w'.$file)){
					$original=imagecreatefrompng($dir.$file);
					$x = imagesx($original);
					$y = imagesy($original);
					$white=imageColorAllocate($original,230,230,230);
					for($i=0; $i<$y; $i++) {
						for($j=0; $j<$x; $j++) {
							$pos = imagecolorat($original, $j, $i);
							if ($pos==0) imagesetpixel($original, $j, $i, $white);
						}
					}
					imageSaveAlpha($original,true);
					imagepng($original,$dir.'w'.$file);
					$changed.='w'.$file.' ';
				}
			}
		}
		closedir($dh);
		if ($changed) echo "<div class='updated'><p>White part files generated: $changed created.</p></div>";
	}
	
	

	?>
	<div><h3>This is the MonsterID options page.</h3>
	<p>You currently have <?php echo $monsterID_count;?> monsters on your website.</p>
	</div>
	<div class='wrap'>
	<p>Set options here:</p>
	<form method="post" action="options-general.php?page=wp_monsterid.php">
		<ul style="list-style-type: none">
	<li><strong>MonsterID Size</strong> in Pixels (Default: 65):<br /> <input type="text" name="monstersize" value="<?php echo $monsterID_options['size'];?>"/></li>
	<li><strong>Background Colors</strong> (enter single value or range Default: 220-255,220-255,220-255):<br/>
	Enter 0-0,0-0,0-0 for transparent background (but note that transparent background may turn grey in IE6):<br/>
	R:<input type="text" name="backr" value="<?php echo implode($monsterID_options['backr'],'-');?>"/>G:<input type="text" name="backg" value="<?php echo implode($monsterID_options['backg'],'-');?>"/>B:<input type="text" name="backb" value="<?php echo implode($monsterID_options['backb'],'-');?>"/></li>
	<li><strong>Arm/Leg Color</strong> (change legs and arms to white if on dark background) (default: black)<br /> <input type="radio" name="legs" value="0" <?php if (!$monsterID_options['legs']) echo 'checked="checked"';?>> Black <input type="radio" name="legs" value="1" <?php if ($monsterID_options['legs']) echo 'checked="checked"';?>> White <br />(Please make sure the folder <code>wp-content/plugins/monsterid/parts/</code> is writeable before changing to White)</li>
	<li><strong>Automatically Add MonsterID to Comments</strong> (adds a MonsterID icon automatically beside commenter names or disable it and edit theme file manually) (default: Auto)<br /> <input type="radio" name="autoadd" value="0" <?php if (!$monsterID_options['autoadd']) echo 'checked="checked"';?>> I'll Do It Myself <input type="radio" name="autoadd" value="1" <?php if ($monsterID_options['autoadd']) echo 'checked="checked"';?>> Add Monsters For Me</li>
	<li><strong>Gravatar Support</strong> (If a commenter has a gravatar use it, otherwise use MonsterID) (default: MonsterID Only)<br /> <input type="radio" name="gravatar" value="0" <?php if (!$monsterID_options['gravatar']) echo 'checked="checked"';?>> MonsterID Only <input type="radio" name="gravatar" value="1" <?php if ($monsterID_options['gravatar']) echo 'checked="checked"';?>> Gravatar + MonsterID</li>
	<li><input type="submit" name="submit" value="Set Options"/></li>
	</ul>
	</form>
	<form method="post" action="options-general.php?page=wp_monsterid.php">
	<ul style="list-style-type: none"><li>Clear the MonsterID Image Cache: <input type="submit" name="clear" value="Clear Cache"/></li></ul>
	</form>
	</div>
	<div class='wrap'><h4>To use MonsterID:</h3>
	<p>Make sure sure the folder <code>wp-content/plugins/monsterid</code> is <a href="http://codex.wordpress.org/Changing_File_Permissions">writeable</a>. Monsters should automatically be added beside your commentors names after that. Enjoy.</p>	
	<?php if (!is_writable(WP_MONSTERID_DIR_INTERNAL)){echo "<div class='error'><p>MonsterID needs ".WP_MONSTERID_DIR_INTERNAL." to be <a href='http://codex.wordpress.org/Changing_File_Permissions'>writable</a>.</p></div>";}
	 if (!function_exists("gd_info")){echo "<div class='error'><p>GD Image library not found. MonsterID needs this library.</p></div>";}?>
	<h4>Testing:</h4>
	<p>A test monster should be here:<?php echo monsterid_build_monster('This is a test','Test');?> and the source URL for this image is 
	<a href="<?php echo monsterid_build_monster('This is a test','Test',false);?>">here</a>.</p>
	<p>If there is no monster above or there are any other problems, concerns or suggestions please let me know <a href="http://scott.sherrillmix.com/blog/blogger/wp_monsterid/">here</a>. Enjoy your monsters.</p></div>
	<div class="wrap"><h4>For Advanced Users:</h4>
	<p>If you want more control of where MonsterID's appear change the Automatically Add option above and add:<br /> <code><?php echo htmlspecialchars('<?php if (function_exists("monsterid_build_monster")) {echo monsterid_build_monster($comment->comment_author_email, $comment->comment_author); } ?>');?></code><br/> in your comments.php. Or if you're more confident and just want the img URL use:<br />
	<code><?php echo htmlspecialchars('<?php if (function_exists("monsterid_build_monster")) {echo monsterid_build_monster($comment->comment_author_email, $comment->comment_author,false); } ?>');?></code></p></div>


	<div><p>The monster generation code and the original images are by <a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a> and the underlying idea came from <a href="http://www.docuverse.com/blog/donpark/2007/01/18/visual-security-9-block-ip-identification">Don Park</a>.</p></div>
	</div>
	<?php	
}


function monsterid_build_monster($seed='',$altImgText='',$img=true,$size='',$write=true){
	if (function_exists("gd_info")&&is_writable(WP_MONSTERID_DIR_INTERNAL)){
		// init random seed
		$id=substr(sha1($seed),0,8);
		//use admin email as salt. should be safe
		$filename=substr(sha1($id.substr(get_option('admin_email'),0,5)),0,15).'.png';
		$monsterID_options=monsterid_get_options();	
		if (!file_exists(WP_MONSTERID_DIR_INTERNAL.$filename)){
			//check if transparent
			if (array_sum($monsterID_options['backr'])+array_sum($monsterID_options['backg'])+array_sum($monsterID_options['backb'])>0) $transparent=false;
			else $transparent=true;
			
			//conditional here for black white
			if ($monsterID_options['legs']==1) $parts_array=array('wlegs' => array(),'whair' => array(),'warms' => array(),'wbody' => array(),'eyes' => array(),'mouth' => array());
			else $parts_array=array('legs' => array(),'hair' => array(),'arms' => array(),'body' => array(),'eyes' => array(),'mouth' => array());
			$parts_order=array_keys($parts_array);
			if ($size==''){
				$size=$monsterID_options['size'];
			}
			//get possible parts files
			$parts_array=monsterid_findparts($parts_array);
			//$partsarray=array('wlegs' => array(),'whair' => array(),'warms' => array(),'wbody' => array(),'eyes' => array(),'mouth' => array());

			if(!$parts_array) return false;
			//set randomness
			$twister=new mid_mersenne_twister(hexdec($id));
			// throw the dice for body parts
			foreach ($parts_order as $part){
				$parts_array[$part]=$parts_array[$part][$twister->array_rand($parts_array[$part])];
			}
			// create backgound
			$file=WP_MONSTERPARTS_DIR.'back.png';
			$monster =  @imagecreatefrompng($file);
			if(!$monster) return false;//something went wrong but don't want to mess up blog layout
			//pick body color
			$lightness=0;
			$count=0;
			while($lightness<0.15||$lightness>.95&&$count<5){
				$rgb_color=array($twister->rand(0,255),$twister->rand(0,255),$twister->rand(0,255));
				$lightness=(max($rgb_color)+min($rgb_color))/2/255;
				$count++;
			}
			$body = imagecolorallocate($monster, $rgb_color[0], $rgb_color[1], $rgb_color[2]); 
			if (!$transparent){
				$back = imagecolorallocate($monster, $twister->rand($monsterID_options['backr'][0],$monsterID_options['backr'][1]), $twister->rand($monsterID_options['backg'][0],$monsterID_options['backg'][1]), $twister->rand($monsterID_options['backb'][0],$monsterID_options['backb'][1]));
				imagefill($monster,0,0,$back);
			}

			// add parts
			foreach($parts_order as $part){
				$file=$parts_array[$part];	
				$file=WP_MONSTERPARTS_DIR.$file;
				$im = @imagecreatefrompng($file);
				if(!$im) return false; //something went wrong but don't want to mess up blog layout
				imageSaveAlpha($im, true);
				imagecopy($monster,$im,0,0,0,0,120,120);
				imagedestroy($im);
				//randomly color body
				if($part == 'body'||$part == 'wbody'){
					imagefill($monster,60,60,$body);
				}
			}

			// going to resize always for now
			$out = @imagecreatetruecolor($size,$size); 
			if (!$out) return false;//something went wrong but don't want to mess up blog layout
			if ($transparent){
				imageSaveAlpha($out,true);
				imageAlphaBlending($out, false);
			}
			imagecopyresampled($out,$monster,0,0,0,0,$size,$size,120,120);
			imagedestroy($monster);

			if ($write){
					$wrote=imagepng($out,WP_MONSTERID_DIR_INTERNAL.$filename);
					if(!$wrote) return false; //something went wrong but don't want to mess up blog layout
			}else{
				header ("Content-type: image/png");
				imagepng($out);    
			}
			imagedestroy($out);
		}
		$filename=get_option('siteurl').'/'.WP_MONSTERID_DIR.$filename;
		if($monsterID_options['gravatar'])
        $filename = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($seed)."&amp;&;size=$size&amp;default=$filename";
		if ($img){
			$filename='<img class="monsterid" src="'.$filename.'" alt="'.str_replace('"',"'",$altImgText).' MonsterID Icon"/>';
		}
		return $filename;
	} else { //php GD image manipulation is required
		return false; //php GD image isn't installed or file isn't writeable but don't want to mess up blog layout
	}
}

function monsterid_comment_author($output){
	global $comment;
	$monsterid_options=monsterid_get_options();
	if((is_page () || is_single ()) && $monsterid_options['autoadd'] && $comment->comment_type!="pingback"&&$comment->comment_type!="trackback") $output=monsterid_build_monster($comment->comment_author_email,$comment->comment_author).' '.$output; 
	return $output;
}


//Hooks
add_action('admin_menu', 'monsterid_menu');
add_filter('get_comment_author','monsterid_comment_author');


class mid_mersenne_twister{
//Copied from wikipedia pseudocode
//Don't call over 600 times (without recalling the constructor)
// Create a length 624 array to store the state of the generator
 var $MT;
 var $i;
 // Initialise the generator from a seed
 function mid_mersenne_twister ($seed=123456) {
     $this->MT[0] = $seed;
		 $this->i=1;
     for ($i=1;$i<624;$i++) { // loop over each other element
         $this->MT[$i] = $this->mysql_math('(1812433253 * ('.$this->MT[$i-1].' ^ ('.$this->MT[$i-1]." >> 30)) + $i) & 0xffffffff");
     }
		 $this->generateNumbers();
 }

	//(some) PHP integers don't have enough bits for Mersenne Twister so use mysql
	function mysql_math($equation){
		global $wpdb;
		$query="SELECT ".$equation;
		$answer=$wpdb->get_var($query);
		return $answer;
	}

 // Generate an array of 624 untempered numbers
 function generateNumbers() {
     for ($i=0;$i<624;$i++) {
         $y = $this->mysql_math('('.$this->MT[$i].' & 0x7fffffff) + ('.$this->MT[($i+1)%624].' & 0xfffffffe)');
				 $even=$this->mysql_math($y.' ^ 0x00000001');
         if ($even) {
             $this->MT[$i] = $this->mysql_math($this->MT[($i + 397) % 624]." ^ ($y >> 1)");
         } else {
             $this->MT[$i] = $this->mysql_math($this->MT[($i + 397) % 624]." ^ ($y >>1) ^ (2567483615)"); // 0x9908b0df
         }
     }
 }
 
 // Extract a tempered pseudorandom number based on the i-th value
 // generateNumbers() will have to be called again once the array of 624 numbers is exhausted
 function extractNumber() {
     $y = $this->MT[$this->i];
     $y = $this->mysql_math("$y ^ ($y >>11) ^ (($y << 7) & 2636928640) ^ (($y << 15) & 4022730752) ^ ($y >>18)");
		 $this->i++;
     return $y/0xffffffff;
 }

	function rand($low,$high){
		$pick=floor($low+($high-$low+1)*$this->extractNumber());
		return ($pick);
	}
	function array_rand($array){
		return($this->rand(0,count($array)-1));
	}
}

?>