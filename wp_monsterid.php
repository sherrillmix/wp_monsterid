<?php
/*
Plugin Name: WP_MonsterID
Version: 2.12
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
define('DEFAULT_MONSTERID_RECENTCOMMENTS_CSS',
'ul#monsterid_recentcomments{list-style:none;}
ul#monsterid_recentcomments img.monsterid{float:left;margin: 0 3px 0 0;}
ul#monsterid_recentcomments{overflow:auto;}
li#recent-comments-with-monsterids ul#monsterid_recentcomments li{clear:left;padding-bottom:5px;}
ul#monsterid_recentcomments li.recentcomments:before{content:"";} 
.recentcomments a{display:inline !important;padding: 0 !important;margin: 0 !important;}'
);
define('DEFAULT_MONSTERID_CSS',
'img.monsterid{float:left;margin: 1px;}'
);

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
	//Generated from find_parts_dimensions
	var $partOptimization=array('legs_1.png' => array(array(17,99),array(58,119)), 'legs_2.png' => array(array(25,94),array(54,119)), 'legs_3.png' => array(array(34,99),array(48,117)), 'legs_4.png' => array(array(999999,0),array(999999,0)), 'legs_5.png' => array(array(28,91),array(64,119)), 'legs_S1.png' => array(array(17,105),array(53,118)), 'legs_S10.png' => array(array(42,88),array(54,118)), 'legs_S11.png' => array(array(999999,0),array(999999,0)), 'legs_S12.png' => array(array(15,107),array(60,115)), 'legs_S13.png' => array(array(8,106),array(69,119)), 'legs_S2.png' => array(array(23,99),array(56,117)), 'legs_S3.png' => array(array(30,114),array(53,118)), 'legs_S4.png' => array(array(12,100),array(50,116)), 'legs_S5.png' => array(array(17,109),array(63,118)), 'legs_S6.png' => array(array(10,100),array(56,119)), 'legs_S7.png' => array(array(33,78),array(73,114)), 'legs_S8.png' => array(array(33,95),array(102,116)), 'legs_S9.png' => array(array(42,75),array(72,116)), 'hair_1.png' => array(array(999999,0),array(999999,0)), 'hair_2.png' => array(array(999999,0),array(999999,0)), 'hair_3.png' => array(array(999999,0),array(999999,0)), 'hair_4.png' => array(array(34,84),array(0,41)), 'hair_5.png' => array(array(999999,0),array(999999,0)), 'hair_S1.png' => array(array(25,96),array(2,58)), 'hair_S2.png' => array(array(45,86),array(3,51)), 'hair_S3.png' => array(array(15,105),array(4,48)), 'hair_S4.png' => array(array(15,102),array(1,51)), 'hair_S5.png' => array(array(16,95),array(4,65)), 'hair_S6.png' => array(array(28,88),array(1,48)), 'hair_S7.png' => array(array(51,67),array(6,49)), 'arms_1.png' => array(array(999999,0),array(999999,0)), 'arms_2.png' => array(array(999999,0),array(999999,0)), 'arms_3.png' => array(array(2,119),array(20,72)), 'arms_4.png' => array(array(2,115),array(14,98)), 'arms_5.png' => array(array(5,119),array(17,90)), 'arms_S1.png' => array(array(0,117),array(23,109)), 'arms_S2.png' => array(array(2,118),array(8,75)), 'arms_S3.png' => array(array(2,116),array(17,93)), 'arms_S4.png' => array(array(999999,0),array(999999,0)), 'arms_S5.png' => array(array(1,115),array(6,40)), 'arms_S6.png' => array(array(3,117),array(7,90)), 'arms_S7.png' => array(array(1,116),array(21,67)), 'arms_S8.png' => array(array(2,119),array(18,98)), 'arms_S9.png' => array(array(8,110),array(18,65)), 'body_1.png' => array(array(22,99),array(17,90)), 'body_10.png' => array(array(37,85),array(22,98)), 'body_11.png' => array(array(23,108),array(10,106)), 'body_12.png' => array(array(9,113),array(6,112)), 'body_13.png' => array(array(29,98),array(26,97)), 'body_14.png' => array(array(31,93),array(25,94)), 'body_15.png' => array(array(23,100),array(20,97)), 'body_2.png' => array(array(14,104),array(16,89)), 'body_3.png' => array(array(22,102),array(22,93)), 'body_4.png' => array(array(18,107),array(22,103)), 'body_5.png' => array(array(22,101),array(12,99)), 'body_6.png' => array(array(24,103),array(10,92)), 'body_7.png' => array(array(22,99),array(7,92)), 'body_8.png' => array(array(21,103),array(12,95)), 'body_9.png' => array(array(20,99),array(9,91)), 'body_S1.png' => array(array(22,102),array(25,96)), 'body_S2.png' => array(array(35,94),array(17,96)), 'body_S3.png' => array(array(30,100),array(20,102)), 'body_S4.png' => array(array(26,104),array(14,92)), 'body_S5.png' => array(array(26,100),array(16,97)), 'eyes_1.png' => array(array(43,76),array(31,48)), 'eyes_10.png' => array(array(40,80),array(32,50)), 'eyes_11.png' => array(array(41,82),array(31,54)), 'eyes_12.png' => array(array(45,78),array(30,50)), 'eyes_13.png' => array(array(10,111),array(10,34)), 'eyes_14.png' => array(array(40,79),array(21,56)), 'eyes_15.png' => array(array(49,72),array(38,43)), 'eyes_2.png' => array(array(37,72),array(36,53)), 'eyes_3.png' => array(array(47,75),array(31,53)), 'eyes_4.png' => array(array(999999,0),array(999999,0)), 'eyes_5.png' => array(array(44,77),array(43,52)), 'eyes_6.png' => array(array(43,57),array(35,49)), 'eyes_7.png' => array(array(62,76),array(35,49)), 'eyes_8.png' => array(array(45,72),array(23,51)), 'eyes_9.png' => array(array(999999,0),array(999999,0)), 'eyes_S1.png' => array(array(41,82),array(29,52)), 'eyes_S2.png' => array(array(999999,0),array(999999,0)), 'eyes_S3.png' => array(array(34,88),array(39,52)), 'eyes_S4.png' => array(array(47,74),array(39,51)), 'eyes_S5.png' => array(array(41,76),array(36,51)), 'mouth_1.png' => array(array(999999,0),array(999999,0)), 'mouth_10.png' => array(array(40,84),array(56,89)), 'mouth_2.png' => array(array(57,65),array(56,61)), 'mouth_3.png' => array(array(38,85),array(54,72)), 'mouth_4.png' => array(array(44,77),array(56,81)), 'mouth_5.png' => array(array(53,72),array(59,76)), 'mouth_6.png' => array(array(48,74),array(56,77)), 'mouth_7.png' => array(array(51,70),array(57,80)), 'mouth_8.png' => array(array(44,81),array(64,78)), 'mouth_9.png' => array(array(49,75),array(52,103)), 'mouth_S1.png' => array(array(47,82),array(57,73)), 'mouth_S2.png' => array(array(45,71),array(65,84)), 'mouth_S3.png' => array(array(48,77),array(56,86)), 'mouth_S4.png' => array(array(46,77),array(56,73)), 'mouth_S5.png' => array(array(55,69),array(55,98)), 'mouth_S6.png' => array(array(40,79),array(56,72)), 'mouth_S7.png' => array(array(999999,0),array(999999,0)));
	var $startTime;
	var $monsterid_options;
	function monsterid(){
		//get the options
		$this->monsterid_options=$this->get_options();
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
				$default_array=array('size'=>65,'backr'=>array(220,255),'backg'=>array(220,255),'backb'=>array(220,255),'legs'=>0,'autoadd'=>1,'gravatar'=>0,'artistic'=>0,'greyscale'=>1,'css'=>DEFAULT_MONSTERID_CSS);
				add_option('monsterID',$default_array,'Options used by MonsterID',false);
				$monsterID_array=$default_array;
			}
			$this->monsterid_options=$monsterID_array;
		}
		return $this->monsterid_options;
	}

	function find_parts_dimensions($text=false){
		$parts_array=array('legs' => array(),'hair' => array(),'arms' => array(),'body' => array(),'eyes' => array(),'mouth' => array());
		$parts=$this->findparts($parts_array);
		$bounds=array();
		foreach($parts as $key => $value){
			foreach($value as $part){
				$file=WP_MONSTERPARTS_DIR.$part;
				$im=imagecreatefrompng($file);
				$imgw = imagesx($im);
				$imgh = imagesy($im);
				$xbounds=array(999999,0);
				$ybounds=array(999999,0);
				for($i=0;$i<$imgw;$i++){
					for($j=0;$j<$imgh;$j++){
						$rgb=ImageColorAt($im, $i, $j);
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;
						$alpha = ($rgb & 0x7F000000) >> 24;
						$lightness=($r+$g+$b)/3/255;
						if($lightness>.1&&$lightness<.99&&$alpha<115){
							$xbounds[0]=min($xbounds[0],$i);
							$xbounds[1]=max($xbounds[1],$i);
							$ybounds[0]=min($ybounds[0],$j);
							$ybounds[1]=max($ybounds[1],$j);
						}
					}
				}
				$text.="'$part' => array(array(${xbounds[0]},${xbounds[1]}),array(${ybounds[0]},${ybounds[1]})), ";
				$bounds[$part]=array($xbounds,$ybounds);
			}
		}
		if($text) return $text;
		else return $bounds;
	}

	function build_monster($seed='',$altImgText='',$img=true,$size='',$write=true,$displaySize='',$gravataron=true){
		if (function_exists("gd_info")&&is_writable(WP_MONSTERID_DIR_INTERNAL)){
			// init random seed
			$id=substr(sha1($seed),0,8);
			//use admin email as salt. should be safe
			$filename=substr(sha1($id.substr(get_option('admin_email'),0,5)),0,15).'.png';
			$monsterID_options=$this->get_options();	
			if ($size=='') $size=$monsterID_options['size'];
			if($displaySize=='') $displaySize=$size;
			if (!file_exists(WP_MONSTERID_DIR_INTERNAL.$filename)){
				if(!isset($this->startTime))$this->startTime=time();
				#make sure nobody waits more than 5 seconds
				if(time()-$this->startTime>WP_MONSTERID_MAXWAIT){
					$user=wp_get_current_user();
					#Let it go longer if the user is an admin
					if($user->user_level < 8||time()-$this->startTime>14) return false;
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
							$this->image_colorize($im,$hue,$saturation,$parts_array[$part]);
						}elseif(in_array($parts_array[$part],$this->sameColorParts)){
							$this->image_colorize($im,$hue,$saturation,$parts_array[$part]);
						}elseif(in_array($parts_array[$part],$this->randomColorParts)){
							$this->image_colorize($im,$twister->extractNumber(),$twister->rand(25000,100000)/100000,$parts_array[$part]);
						}elseif(array_key_exists($parts_array[$part],$this->specificColorParts)){
							$low=$this->specificColorParts[$parts_array[$part]][0]*10000;
							$high=$this->specificColorParts[$parts_array[$part]][1]*10000;
							$this->image_colorize($im,$twister->rand($low,$high)/10000,$twister->rand(25000,100000)/100000,$parts_array[$part]);
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
						$wrote=@imagepng($out,WP_MONSTERID_DIR_INTERNAL.$filename);
						if(!$wrote) return false; //something went wrong but don't want to mess up blog layout
				}else{
					header ("Content-type: image/png");
					imagepng($out);    
				}
				imagedestroy($out);
			}
			$filename=get_option('siteurl').WP_MONSTERID_DIR.$filename;
			if($monsterID_options['gravatar']&&$gravataron)
					$filename = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($seed)."&amp;&;size=$size&amp;default=$filename";
			if ($img){
				$filename='<img class="monsterid" src="'.$filename.'" alt="'.str_replace('"',"'",$altImgText).' MonsterID Icon" height="'.$displaySize.'" width="'.$displaySize.'"/>';
			}
			return $filename;
		} else { //php GD image manipulation is required
			return false; //php GD image isn't installed or file isn't writeable but don't want to mess up blog layout
		}
	}

	function image_colorize(&$im,$hue=1,$saturation=1,$part=''){
		$imgw = imagesx($im);
		$imgh = imagesy($im);
		/*//DOESN'T PRESERVE ALPHA SO DOESN'T WORK
		imagetruecolortopalette($im,true,1000);
		$numColors=imagecolorstotal($im);
		for($i=0;$i<$numColors;$i++){
			$color=imagecolorsforindex($im,$i);
			$lightness=($color['red']+$color['green']+$color['blue'])/3/255;
			var_dump($color);
			if($color['alpha']!=0){
				var_dump("|||||||||||||||||||||||");
			}
			if($lightness>.1&&$lightness<.99&&$color['alpha']<115){
				$newrgb=$this->HSL2hex(array($hue,$saturation,$lightness));
				imagecolorset ($im, $i, $newrgb[0],$newrgb[1],$newrgb[2]);
			}
		}*/
		imagealphablending($im,FALSE);
		if($optimize=$this->partOptimization[$part]){
			$xmin=$optimize[0][0];
			$xmax=$optimize[0][1];
			$ymin=$optimize[1][0];
			$ymax=$optimize[1][1];
		}else{
			$xmin=0;
			$xmax=$imgw-1;
			$ymin=0;
			$ymax=$imgh-1;
		}
		for($i=$xmin;$i<=$xmax;$i++){
			for($j=$ymin;$j<=$ymax;$j++){
				$rgb=ImageColorAt($im, $i, $j);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$alpha = ($rgb & 0x7F000000) >> 24;
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
global $monsterid;
$monsterid=new monsterid();



function monsterid_subpanel() {
	global $monsterid;
	echo "<div class='wrap'>";
	if (isset($_POST['submit'])) { //update the monster size option
		$monsterID_options=$monsterid->get_options();
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
			if(is_writable(WP_MONSTERPARTS_DIR)||$monsterID_options['artistic'])
				$monsterID_options['legs']=1;
			else{
				echo "<div class='error'>Directory ".WP_MONSTERPARTS_DIR." must be <a href='http://codex.wordpress.org/Changing_File_Permissions'>writeable</a> to use white legs and arms.</div>";
				$monsterID_options['legs']=0;
			}
		}
		if ($_POST['autoadd'] == 0) $monsterID_options['autoadd']=0;
		elseif ($_POST['autoadd'] == 1) $monsterID_options['autoadd']=1;
		elseif ($_POST['autoadd'] == 2) $monsterID_options['autoadd']=2;
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
	}elseif(isset($_POST['cssreset'])){//reset monsterid css to default
		$monsterID_options=$monsterid->get_options();
		$monsterID_options['css']=DEFAULT_MONSTERID_CSS;
		update_option('monsterID', $monsterID_options);
	}elseif(isset($_POST['csssubmit'])){
		$monsterID_options=$monsterid->get_options();
		$monsterID_options['css']=$_POST['monsterid_css'];
		update_option('monsterID', $monsterID_options);
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
	<li><strong>Background Colors</strong> (enter single value or range Default: 220-255,220-255,220-255):<br/>
	Enter 0-0,0-0,0-0 for transparent background (but note that transparent background may turn grey in IE6):<br/>
	R:<input type="text" name="backr" value="<?php echo implode($monsterID_options['backr'],'-');?>"/>G:<input type="text" name="backg" value="<?php echo implode($monsterID_options['backg'],'-');?>"/>B:<input type="text" name="backb" value="<?php echo implode($monsterID_options['backb'],'-');?>"/></li>
	<li><strong>Arm/Leg Color</strong> (change legs and arms to white if on dark background) (default: black)<br /> <input type="radio" name="legs" value="0" <?php if (!$monsterID_options['legs']) echo 'checked="checked"';?>> Black <input type="radio" name="legs" value="1" <?php if ($monsterID_options['legs']) echo 'checked="checked"';?>> White <br />(Please make sure the folder <code>wp-content/plugins/monsterid/parts/</code> is writeable before changing to White)</li>
	<li><strong>Automatically Add MonsterID to Comments</strong> (adds a MonsterID icon automatically beside commenter names or disable it and edit theme file manually) (default: Auto)<br /> <input type="radio" name="autoadd" value="0" <?php if (!$monsterID_options['autoadd']) echo 'checked="checked"';?>> I'll Do It Myself <input type="radio" name="autoadd" value="1" <?php if ($monsterID_options['autoadd']==1) echo 'checked="checked"';?>> Add Monsters For Me <input type="radio" name="autoadd" value="2" <?php if ($monsterID_options['autoadd']==2) echo 'checked="checked"';?>/> My Theme Has Builtin WP2.5+ Avatars</li></li>
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
	<p>If you use the Recent Comments Widget in your sidebar, this plugin also provides a replacement Recent Comments (with MonsterIDs) Widget to add MonsterIDs to the sidebar comments (just set it in the Widgets Control Panel)</p>
	<?php if (!is_writable(WP_MONSTERID_DIR_INTERNAL)){echo "<div class='error'><p>MonsterID needs ".WP_MONSTERID_DIR_INTERNAL." to be <a href='http://codex.wordpress.org/Changing_File_Permissions'>writable</a>.</p></div>";}
	 if (!function_exists("gd_info")){echo "<div class='error'><p>GD Image library not found. MonsterID needs this library.</p></div>";}?>
	<h4>Testing:</h4>
	<p>A test monster should be here:<?php echo monsterid_build_monster('This is a test','Test');?> and the source URL for this image is 
	<a href="<?php echo monsterid_build_monster('This is a test','Test',false);?>">here</a>.</p>
	<p>If there is no monster above or there are any other problems, concerns or suggestions please let me know <a href="http://scott.sherrillmix.com/blog/blogger/wp_monsterid/">here</a>. Enjoy your monsters.</p></div>
	<div class="wrap"><h4>For Advanced Users:</h4>
	<p>If you want more control of where MonsterID's appear change the Automatically Add option above and add:<br /> <code><?php echo htmlspecialchars('<?php if (function_exists("monsterid_build_monster")) {echo monsterid_build_monster($comment->comment_author_email, $comment->comment_author); } ?>');?></code><br/> in your comments.php. Or if you're more confident and just want the img URL use:<br />
	<code><?php echo htmlspecialchars('<?php if (function_exists("monsterid_build_monster")) {echo monsterid_build_monster($comment->comment_author_email, $comment->comment_author,false); } ?>');?></code></p>
	<p>You can also add any custom css you would like here:</p>
	<form method="post" action="options-general.php?page=wp_monsterid.php">
	<ul style="list-style-type: none">
	<li><textarea name="monsterid_css" rows="5" cols="70"><?php echo $monsterID_options['css'];?></textarea></li>
	<li><input type="submit" name="csssubmit" value="Adjust CSS"/><input type="submit" name="cssreset" value="Return to Default"/></li></ul>
	</form>
	</div>


	<div><p>The monster generation code and the original images are by <a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a>, the updated artistic images came from <a href=" http://rocketworm.com/">Lemm</a> and the underlying idea came from <a href="http://www.docuverse.com/blog/donpark/2007/01/18/visual-security-9-block-ip-identification">Don Park</a>.</p></div>
	</div>
	<?php	
}


function monsterid_build_monster($seed='',$altImgText='',$img=true,$size='',$write=true,$displaySize='',$gravataron=true){
	global $monsterid;
	if (!isset($monsterid))$monsterid=new monsterid();
	if(isset($monsterid)){
		return $monsterid->build_monster($seed,$altImgText,$img,$size,$write,$displaySize,$gravataron);
	}else return false;
}

function monsterid_comment_author($output){
	global $comment;
	global $monsterid;
	if(!isset($monsterid)) return $output;
	$monsterid_options=$monsterid->get_options();
	if((is_page () || is_single ()) && $monsterid_options['autoadd']==1 && $comment->comment_type!="pingback" && $comment->comment_type!="trackback" &&  isset($comment->comment_karma)) //assuming sidebar widgets won't check comment karma (and single page comments will))
	  $output=monsterid_build_monster($comment->comment_author_email,$comment->comment_author).' '.$output; 
	return $output;
}

function monsterid_get_avatar($avatar, $id_or_email, $size, $default){
	global $monsterid;
	if(!isset($monsterid)) return $avatar;
	$email = '';
	if ( is_numeric($id_or_email) ) {
		$id = (int) $id_or_email;
		$user = get_userdata($id);
		if ( $user )
			$email = $user->user_email;
	} elseif ( is_object($id_or_email) ) {
		if ( !empty($id_or_email->user_id) ) {
			$id = (int) $id_or_email->user_id;
			$user = get_userdata($id);
			if ( $user)
				$email = $user->user_email;
		} elseif ( !empty($id_or_email->comment_author_email) ) {
			$email = $id_or_email->comment_author_email;
		}
	} else {
		$email = $id_or_email;
	}

	if(!$avatar) return monsterid_build($email,'','',true,$size);
	if(!$monsterid->monsterid_options['gravatar']){
		$monsteridurl=monsterid_build_monster($email,'',false);
		$newavatar=preg_replace('@src=(["\'])http://[^"\']+["\']@','src=\1'.$monsteridurl.'\1',$avatar);
		$avatar=$newavatar;
	}elseif($monsterid->monsterid_options['gravatar']==1){
		$monsteridurl=monsterid_build_monster($email,'',false,'',true,$size,false);
		if(strpos($avatar,'default=http://')!==false){
			$newavatar=preg_replace('@default=http://[^&\'"]+([&\'"])@','default='.urlencode($monsteridurl).'\1',$avatar);
		}else{
			$newavatar=preg_replace('@(src=(["\'])http://[^?]+\?)@','\1default='.urlencode($monsteridurl).'&amp;',$avatar);
		}
		$avatar=$newavatar;
	}
	return($avatar);
}



function monsterid_style() {
	global $monsterid;
	$options = $monsterid->get_options();
	if($css = $options['css']){
?>
<style type="text/css">
<?php echo $css; ?>
</style>
<?php
	}
}

//Hooks
add_action('admin_menu', 'monsterid_menu');
add_filter('get_comment_author','monsterid_comment_author');
add_action('wp_head', 'monsterid_style');
if($wp_version>=2.5&&$monsterid->monsterid_options['autoadd']==2){
	add_filter('get_avatar','monsterid_get_avatar',5,4);
}

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

//Widget stuff 
//Wordpress's default widget doesn't get commenter email so we can't use it for monsterids
//Copying their widget with some search and replace with monsterid
function monsterid_recent_comments($args) {
	global $wpdb, $comments, $comment, $monsterid;
	extract($args, EXTR_SKIP);
	$options = get_option('widget_monsterid_recent_comments');
	$title = empty($options['title']) ? __('Recent Comments') : $options['title'];
	if ( !$number = (int) $options['number'] )
		$number = 5;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 15 )
		$number = 15;
	if ( !$size = (int) $options['monsterid_size'] )
		$size = 30;
	else if ( $size < 5 )
		$size=5;
	else if($size > 80)
		$size=80;
	if ( !$comments = wp_cache_get( 'monsterid_recent_comments', 'widget' ) ) {
		$comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID, comment_author_email, comment_type FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $number");
		wp_cache_add( 'monsterid_recent_comments', $comments, 'widget' );
	}
?>

		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul id="monsterid_recentcomments"><?php
			if ( $comments ) : foreach ($comments as $comment) :
				echo  '<li class="recentcomments">';
				if($comment->comment_type!="pingback"&&$comment->comment_type!="trackback")
					echo monsterid_build_monster($comment->comment_author_email,$comment->comment_author,TRUE,'',TRUE,$size);
				echo sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
			endforeach; endif;?></ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_delete_monsterid_recent_comments_cache() {
	wp_cache_delete( 'monsterid_recent_comments', 'widget' );
}

function monsterid_recent_comments_control() {
	$options = $newoptions = get_option('widget_monsterid_recent_comments');
	if ( $_POST["monsterid_recent-comments-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["monsterid_recent-comments-title"]));
		$newoptions['number'] = (int) $_POST["monsterid_recent-comments-number"];
		$newoptions['monsterid_size'] = (int) $_POST["monsterid_size"];
		$newoptions['monsterid_css'] =  $_POST["monsterid_css"];
	}
	if($_POST["monsterid_css_reset"])
		$newoptions['monsterid_css'] = DEFAULT_MONSTERID_RECENTCOMMENTS_CSS;
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_monsterid_recent_comments', $options);
		wp_delete_monsterid_recent_comments_cache();
	}
	$title = attribute_escape($options['title']);
	if ( !$number = (int) $options['number'] )
		$number = 5;
	if ( !$size = (int) $options['monsterid_size'] )
		$size = 25;
	if(!$css = stripslashes($options['monsterid_css']))
		$css = DEFAULT_MONSTERID_RECENTCOMMENTS_CSS;
?>
			<p><label for="monsterid_recent-comments-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="monsterid_recent-comments-title" name="monsterid_recent-comments-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label for="monsterid_recent-comments-number"><?php _e('Number of comments to show:'); ?> <input style="width: 25px; text-align: center;" id="monsterid_recent-comments-number" name="monsterid_recent-comments-number" type="text" value="<?php echo $number; ?>" /></label> <?php _e('(at most 15)'); ?></p>
			<p><label for="monsterid_size"><?php _e('Size of Widget MonsterIDs (pixels):'); ?> <input style="width: 25px; text-align: center;" id="monsterid_size" name="monsterid_size" type="text" value="<?php echo $size; ?>" /></label></p>
			<p><label for="monsterid_css"><?php _e('CSS for Widget:'); ?><textarea id="monsterid_css" name="monsterid_css" rows="3" cols="55" /><?php echo $css;?></textarea></label></p>
			<p><label for="monsterid_css_reset"><?php _e('Reset CSS to Default:'); ?> <input id="monsterid_css_reset" name="monsterid_css_reset" type="submit" value="Reset CSS" /></label></p>
			<input type="hidden" id="monsterid_recent-comments-submit" name="monsterid_recent-comments-submit" value="1" />
<?php
}

function monsterid_recent_comments_style() {
	$options = get_option('widget_monsterid_recent_comments');
	if(!$css = stripslashes($options['monsterid_css']))
		$css = DEFAULT_MONSTERID_RECENTCOMMENTS_CSS;
?>
<style type="text/css">
<?php echo $css; ?>
</style>
<?php
}

function monsterid_recent_comments_widget_init(){
	register_sidebar_widget('Recent Comments (with MonsterIDs)', 'monsterid_recent_comments');
	register_widget_control('Recent Comments (with MonsterIDs)', 'monsterid_recent_comments_control', 400, 250);
	if ( is_active_widget('monsterid_recent_comments') )
		add_action('wp_head', 'monsterid_recent_comments_style');
	add_action( 'comment_post', 'wp_delete_monsterid_recent_comments_cache' );
	add_action( 'wp_set_comment_status', 'wp_delete_monsterid_recent_comments_cache' );
}

add_action('widgets_init', 'monsterid_recent_comments_widget_init');

?>