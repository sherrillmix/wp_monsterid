<?php
/*
Plugin Name: WP_MonsterID
Version: 2.02
Plugin URI: http://scott.sherrillmix.com/blog/blogger/WP_MonsterID
Description: This plugin generates email specific monster icons for each user based on code and images by <a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a> and images by <a href=" http://rocketworm.com/">Lemm</a>.
Author: Scott Sherrill-Mix
Author URI: http://scott.sherrillmix.com/blog/
*/

//Deal with either wp-content/plugins/monsterid/ or wp-content/plugins/somename/monsterid
//Assuming monsterid.php is one directory below /monsterid
define('WP_MONSTERID_DIR', str_replace('\\','/',preg_replace('@.*([\\\\/]wp-content[\\\\/].*)@','\1',dirname(__FILE__)).'/monsterid/'));
define('WP_MONSTERID_DIR_INTERNAL', dirname(__FILE__).'/monsterid/');
define('WP_MONSTERPARTS_DIR', WP_MONSTERID_DIR_INTERNAL.'parts/');
define('WP_MONSTERID_MAXWAIT', 5);

function monsterid_menu() {
	if (function_exists('add_options_page')) {
		add_options_page('MonsterID Control Panel', 'MonsterID', 1, basename(__FILE__), 'monsterid_subpanel');
	}
}
class monsterid{
	var $whiteParts =  array('arms_1.png','arms_2.png','arms_S4.png','arms_S5.png','eye_13.png','hair_1.png','hair_2.png','hair_3.png','hair_5.png','legs_4.png','legs_S11.png'); 
	var $sameColorParts = array('arms_S8.png','legs_S5.png','legs_S13.png','mouth_S5.png','mouth_S4.png');
	var $specificColorParts = array('hair_S4.png'=>array(.6,.75),'arms_S2.png'=>array(-.05,.05),'hair_S6.png'=>array(-.05,.05),'mouth_9.png'=>array(-.05,.05),'mouth_6.png'=>array(-.05,.05),'mouth_S2.png'=>array(-.05,.05));
	var $randomColorParts = array('arms_3.png','arms_4.png','arms_5.png','arms_S1.png','arms_S3.png','arms_S5.png','arms_S6.png','arms_S7.png','arms_S9.png','hair_S1.png','hair_S2.png','hair_S3.png','hair_S5.png','legs_1.png','legs_2.png','legs_3.png','legs_5.png','legs_S1.png','legs_S2.png','legs_S3.png','legs_S4.png','legs_S6.png','legs_S7.png','legs_S10.png','legs_S12.png','mouth_3.png','mouth_4.png','mouth_7.png','mouth_10.png','mouth_S6.png');
	var $startTime;
	var $monsterid_options;
	function monsterid(){
		//nothing for now
	}

	function findparts($partsarray){
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
		closedir($dh);
		if ($noparts) return false;
		//sort for consistency across servers
		foreach($partsarray as $key => $value) sort($partsarray[$key]);
		return $partsarray;
	}

	function get_options($check=FALSE){
		if(!isset($this->monsterid_options)||$check){
			$monsterID_array=get_option('monsterID');
			if (!isset($monsterID_array['size'])||!isset($monsterID_array['backb'])){
				//Set Default Values Here
				$default_array=array('size'=>65,'backr'=>array(220,255),'backg'=>array(220,255),'backb'=>array(220,255),'legs'=>0,'autoadd'=>1,'gravatar'=>0,'artistic'=>0,'greyscale'=>1,'nonpostsize'=>0);
				add_option('monsterID',$default_array,'Options used by MonsterID',false);
				$monsterID_array=$default_array;
			}
			$this->monsterid_options=$monsterID_array;
		}
		return $this->monsterid_options;
	}

	function build_monster($seed='',$altImgText='',$img=true,$size='',$write=true){
		if (function_exists("gd_info")&&is_writable(WP_MONSTERID_DIR_INTERNAL)){
			// init random seed
			$id=substr(sha1($seed),0,8);
			//use admin email as salt. should be safe
			$filename=substr(sha1($id.substr(get_option('admin_email'),0,5)),0,15).'.png';
			$monsterID_options=$this->get_options();	
			if ($size=='') $size=$monsterID_options['size'];
			if (!file_exists(WP_MONSTERID_DIR_INTERNAL.$filename)){
				if(!isset($this->startTime))$this->startTime=time();
				#make sure nobody waits more than 5 seconds
				if(time()-$this->startTime>WP_MONSTERID_MAXWAIT){
					$user=wp_get_current_user();
					#Let it go longer if the user is an admin
					if($user->user_level < 8||time()-$this->startTime>20) return false;
				}

				//check if transparent
				if (array_sum($monsterID_options['backr'])+array_sum($monsterID_options['backg'])+array_sum($monsterID_options['backb'])>0) $transparent=false;
				else $transparent=true;
				
				if($monsterID_options['artistic']) $parts_array=array('legs' => array(),'hair' => array(),'arms' => array(),'body' => array(),'eyes' => array(),'mouth' => array());
				elseif ($monsterID_options['legs']==1&&!$monsterID_options['artistic']) $parts_array=array('woldlegs' => array(),'woldhair' => array(),'woldarms' => array(),'woldbody' => array(),'oldeyes' => array(),'oldmouth' => array());
				else $parts_array=array('oldlegs' => array(),'oldhair' => array(),'oldarms' => array(),'oldbody' => array(),'oldeyes' => array(),'oldmouth' => array());
				
				$parts_order=array_keys($parts_array);
				
				//get possible parts files
				$parts_array=$this->findparts($parts_array);

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
				$hue=$twister->extractNumber();
				$saturation=$twister->rand(25000,100000)/100000;
				//Pick a back color even if transparent to preserve random draws across servers		
				$back = imagecolorallocate($monster, $twister->rand($monsterID_options['backr'][0],$monsterID_options['backr'][1]), $twister->rand($monsterID_options['backg'][0],$monsterID_options['backg'][1]), $twister->rand($monsterID_options['backb'][0],$monsterID_options['backb'][1]));
				$lightness=$twister->rand(25000,90000)/100000; //Don't actually user this if artistic but preserves randomness
				if (!$transparent){
					imagefill($monster,0,0,$back);
				}

				// add parts
				foreach($parts_order as $part){
					$file=$parts_array[$part];	
					$file=WP_MONSTERPARTS_DIR.$file;
					$im = @imagecreatefrompng($file);
					if(!$im) return false; //something went wrong but don't want to mess up blog layout
					imageSaveAlpha($im, true);
					if ($monsterID_options['artistic']&&$monsterID_options['greyscale']){
						//randomly color body parts
						if($monsterID_options['legs']&&in_array($parts_array[$part],$this->whiteParts)){
							$this->image_whitize($im);
						}
						if($part == 'body'||$part == 'wbody'){
							//imagefill($monster,60,60,$body);
							$this->image_colorize($im,$hue,$saturation);
						}elseif(in_array($parts_array[$part],$this->sameColorParts)){
							$this->image_colorize($im,$hue,$saturation);
						}elseif(in_array($parts_array[$part],$this->randomColorParts)){
							$this->image_colorize($im,$twister->extractNumber(),$twister->rand(25000,100000)/100000);
						}elseif(array_key_exists($parts_array[$part],$this->specificColorParts)){
							$low=$this->specificColorParts[$parts_array[$part]][0]*10000;
							$high=$this->specificColorParts[$parts_array[$part]][1]*10000;
							$this->image_colorize($im,$twister->rand($low,$high)/10000,$twister->rand(25000,100000)/100000);
						}
					}else{
						if($part == 'oldbody'||$part == 'woldbody'){
							$rgb_color=$this->HSL2hex(array($hue,$saturation,$lightness));
							$body=imagecolorallocate($im, $rgb_color[0], $rgb_color[1], $rgb_color[2]);
							imagefill($im,60,60,$body);
						}
					}
					imagecopy($monster,$im,0,0,0,0,120,120);
					imagedestroy($im);
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
				$filename='<img class="monsterid" src="'.$filename.'" alt="'.str_replace('"',"'",$altImgText).' MonsterID Icon" height="'.$size.'" width="'.$size.'"/>';
			}
			return $filename;
		} else { //php GD image manipulation is required
			return false; //php GD image isn't installed or file isn't writeable but don't want to mess up blog layout
		}
	}

	function image_colorize(&$im,$hue=1,$saturation=1){
		$imgw = imagesx($im);
		$imgh = imagesy($im);
		imagealphablending($im,FALSE);
		for($i=0;$i<$imgw;$i++){
			for($j=0;$j<$imgh;$j++){
				$rgb=ImageColorAt($im, $i, $j);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$alpha = ($rgb & 0x7F000000) >> 24;###DOES THIS WORK?
				$lightness=($r+$g+$b)/3/255;
				if($lightness>.1&&$lightness<.99&&$alpha<115){
					$newrgb=$this->HSL2hex(array($hue,$saturation,$lightness));
					$color=imagecolorallocatealpha($im, $newrgb[0],$newrgb[1],$newrgb[2],$alpha);
					imagesetpixel($im,$i,$j,$color);
				}
			}
		}
		imagealphablending($im,TRUE);
		return($im);
	}

	function image_whitize(&$im){
		$imgw = imagesx($im);
		$imgh = imagesy($im);
		imagealphablending($im,FALSE);
		for($i=0; $i<$imgh; $i++) {
			for($j=0; $j<$imgw; $j++) {
				$rgb=ImageColorAt($im, $i, $j);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$alpha = ($rgb & 0x7F000000) >> 24;
				$lightness=($r+$g+$b)/3/255;
				if($lightness<=.1&&$alpha<115){
					$newrgb=$this->HSL2hex(array(0,0,1-$lightness));
					$color=imagecolorallocatealpha($im, $newrgb[0],$newrgb[1],$newrgb[2],$alpha);
					imagesetpixel($im,$i,$j,$color);
				}
			}
		}
		imagealphablending($im,TRUE);
		imageSaveAlpha($im,true);
		return($im);
	}

	function HSL2hex($hsl){
		$hue=$hsl[0];
		$saturation=$hsl[1];
		$lightness=$hsl[2];
		if ($saturation == 0){
			$red = $lightness * 255;
			$green = $lightness * 255;
			$blue = $lightness * 255;
		} else {
			if ($lightness < 0.5) $var_2 = $lightness * (1 + $saturation);
			else $var_2 = ($lightness + $saturation) - ($saturation * $lightness);

			$var_1 = 2 * $lightness - $var_2;
			$red = 255 * $this->hue_2_rgb($var_1,$var_2,$hue + (1 / 3));
			$green = 255 * $this->hue_2_rgb($var_1,$var_2,$hue - (1 / 3));
			$blue = 255 * $this->hue_2_rgb($var_1,$var_2,$hue);
		}
		return array($red,$green,$blue);
	}

	function hue_2_rgb($v1,$v2,$vh){
		if ($vh < 0) $vh += 1;
		elseif ($vh > 1) $vh -= 1;
		if ((6 * $vh) < 1) $output=$v1 + ($v2 - $v1) * 6 * $vh;
		elseif ((2 * $vh) < 1) $output=$v2;
		elseif ((3 * $vh) < 2) $output=$v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6);
		else $output=$v1;
		return($output);
	}

}

#Create a monsterid for later use
$monsterid=new monsterid();




function monsterid_subpanel() {
	global $monsterid;
	echo "<div class='wrap'>";
	if (isset($_POST['submit'])) { //update the monster size option
		$monsterID_options=$monsterid->get_options();
		$monstersize=intval($_POST['monstersize']);
		$nonpostsize=intval($_POST['nonpostsize']);
		if ($monstersize > 0 & $monstersize < 400){
			$monsterID_options['size']=$monstersize;
		}else{
			echo "<div class='error'><p>Please enter an integer for size. Preferably between 30-200.</p></div>";		
		}
		if ($nonpostsize > 0 & $nonpostsize < 400){
			$monsterID_options['nonpostsize']=$nonpostsize;
		}else{
			echo "<div class='error'><p>Please enter an integer for size. Preferably between 10-100.</p></div>";		
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
			if(is_writable(WP_MONSTERPARTS_DIR)||$monsterID_options['artistic'])
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
		if ($_POST['artistic'] == 0) $monsterID_options['artistic']=0;
		elseif ($_POST['artistic'] == 1) $monsterID_options['artistic']=1;
		if ($_POST['greyscale'] == 0) $monsterID_options['greyscale']=0;
		elseif ($_POST['greyscale'] == 1) $monsterID_options['greyscale']=1;
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
	$monsterID_options=$monsterid->get_options(TRUE);
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
	if ($monsterID_options['legs']&&is_writable(WP_MONSTERPARTS_DIR)&&!$monsterID_options['artistic']&&$dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (is_file($dir.$file) and preg_match('/^(oldarms|oldlegs|oldbody|oldhair)_.*\.png$/',$file)){
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
	<li><strong>MonsterID Size on Non-Posts</strong> (e.g. Front page Recent Comment Widget) in Pixels (0 for none, Default: 0):<br /> <input type="text" name="nonpostsize" value="<?php echo $monsterID_options['nonpostsize'] ? $monsterID_options['nonpostsize'] : 0;?>"/></li>
	<li><strong>Background Colors</strong> (enter single value or range Default: 220-255,220-255,220-255):<br/>
	Enter 0-0,0-0,0-0 for transparent background (but note that transparent background may turn grey in IE6):<br/>
	R:<input type="text" name="backr" value="<?php echo implode($monsterID_options['backr'],'-');?>"/>G:<input type="text" name="backg" value="<?php echo implode($monsterID_options['backg'],'-');?>"/>B:<input type="text" name="backb" value="<?php echo implode($monsterID_options['backb'],'-');?>"/></li>
	<li><strong>Arm/Leg Color</strong> (change legs and arms to white if on dark background) (default: black)<br /> <input type="radio" name="legs" value="0" <?php if (!$monsterID_options['legs']) echo 'checked="checked"';?>> Black <input type="radio" name="legs" value="1" <?php if ($monsterID_options['legs']) echo 'checked="checked"';?>> White <br />(Please make sure the folder <code>wp-content/plugins/monsterid/parts/</code> is writeable before changing to White)</li>
	<li><strong>Automatically Add MonsterID to Comments</strong> (adds a MonsterID icon automatically beside commenter names or disable it and edit theme file manually) (default: Auto)<br /> <input type="radio" name="autoadd" value="0" <?php if (!$monsterID_options['autoadd']) echo 'checked="checked"';?>> I'll Do It Myself <input type="radio" name="autoadd" value="1" <?php if ($monsterID_options['autoadd']) echo 'checked="checked"';?>> Add Monsters For Me</li>
	<li><strong>Gravatar Support</strong> (If a commenter has a gravatar use it, otherwise use MonsterID) (default: MonsterID Only)<br /> <input type="radio" name="gravatar" value="0" <?php if (!$monsterID_options['gravatar']) echo 'checked="checked"';?>> MonsterID Only <input type="radio" name="gravatar" value="1" <?php if ($monsterID_options['gravatar']) echo 'checked="checked"';?>> Gravatar + MonsterID</li>
	<li><strong>Artistic Monsters</strong> (Artistic monsters require more processing) (default: Artistic)<br /> <input type="radio" name="artistic" value="1" <?php if ($monsterID_options['artistic']) echo 'checked="checked"';?>> Artistic <input type="radio" name="artistic" value="0" <?php if (!$monsterID_options['artistic']) echo 'checked="checked"';?>> Original</li>
	<?php if($monsterID_options['artistic']){?>
		<li><strong>Grey Scale Monsters</strong> (Greyscale artistic require less processing) (default: Color)<br /> <input type="radio" name="greyscale" value="1" <?php if ($monsterID_options['greyscale']) echo 'checked="checked"';?>> Color <input type="radio" name="greyscale" value="0" <?php if (!$monsterID_options['greyscale']) echo 'checked="checked"';?>> Greyscale</li>
	<?php }?>
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


	<div><p>The monster generation code and the original images are by <a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a>, the updated artistic images came from <a href=" http://rocketworm.com/">Lemm</a> and the underlying idea came from <a href="http://www.docuverse.com/blog/donpark/2007/01/18/visual-security-9-block-ip-identification">Don Park</a>.</p></div>
	</div>
	<?php	
}


function monsterid_build_monster($seed='',$altImgText='',$img=true,$size='',$write=true){
	global $monsterid;
	if (!isset($monsterid))$monsterid=new monsterid();
	if(isset($monsterid)){
		return $monsterid->build_monster($seed,$altImgText,$img,$size,$write);
	}else return false;
}

function monsterid_comment_author($output){
	global $comment;
	global $monsterid;
	$monsterid_options=$monsterid->get_options();
	if($monsterid_options['autoadd'] && $comment->comment_type!="pingback"&&$comment->comment_type!="trackback"){
		if(is_page () || is_single ())
			$output=monsterid_build_monster($comment->comment_author_email,$comment->comment_author).' '.$output; 	
		elseif($monsterid_options['nonpostsize']&!is_admin())
			$output='<img class="smallmonsterid monsterid" src="'.monsterid_build_monster($comment->comment_author_email,$comment->comment_author,FALSE).'" alt="MonsterID" width="'.$monsterid_options['nonpostsize'].'" height="'.$monsterid_options['nonpostsize'].'" /> '.$output;
	}
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