<?php
	
	/*
	Plugin Name: WP_MonsterID
	Version: 3.1
	Plugin URI: http://scott.sherrillmix.com/blog/blogger/WP_MonsterID
	Description: This plugin generates email specific monster icons for each user based on code and images by <a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a> and images by <a href=" http://rocketworm.com/">Lemm</a>.
	Author: Scott Sherrill-Mix
	Author URI: http://scott.sherrillmix.com/blog/
	Text Domain: wp-monsterid
	Domain Path: /languages
	*/
	
	//Deal with either wp-content/plugins/monsterid/ or wp-content/plugins/somename/monsterid
	//Assuming monsterid.php is one directory below /monsterid
	define( 'WP_MONSTERID_DIR', str_replace( '\\', '/', preg_replace( '@.*([\\\\/]wp-content[\\\\/].*)@', '\1', dirname( __FILE__) ) . '/monsterid/' ) );
	define( 'WP_MONSTERID_DIR_INTERNAL', dirname( __FILE__ ) . '/monsterid/' );
	define( 'WP_MONSTERPARTS_DIR', WP_MONSTERID_DIR_INTERNAL . 'parts/' );
	define( 'WP_MONSTERID_MAXWAIT', 5 );
	
	define( 'DEFAULT_MONSTERID_RECENTCOMMENTS_CSS', 
	'ul#monsterid_recentcomments{ list-style: none; }
ul#monsterid_recentcomments img.monsterid{ float: left; margin: 0 3px 0 0; }
ul#monsterid_recentcomments{ overflow: auto; }
li#recent-comments-with-monsterids ul#monsterid_recentcomments li{ clear: left; padding-bottom: 5px; }
ul#monsterid_recentcomments li.recentcomments:before{ content: ""; }
.recentcomments a{ display: inline !important; padding: 0 !important; margin: 0 !important; }'
	);
	
	define( 'DEFAULT_MONSTERID_CSS',
	'img.monsterid{ float:left; margin: 1px; }'
	);
	
	//Wp_MonsterID menu slug used for the options page
	define( 'WP_MONSTERID_MENU_SLUG', basename(__FILE__) );
	
	require_once( 'wp_monsterid_admin.php' );
	
	class monsterid{
		
		var $whiteParts =  array( 'arms_1.png', 'arms_2.png', 'arms_S4.png', 'arms_S5.png', 'eye_13.png', 'hair_1.png', 'hair_2.png', 'hair_3.png', 'hair_5.png', 'legs_4.png', 'legs_S11.png' );
		
		var $sameColorParts = array( 'arms_S8.png', 'legs_S5.png', 'legs_S13.png', 'mouth_S5.png', 'mouth_S4.png' );
		var $specificColorParts = array(
				'hair_S4.png' => array( .6, .75 ),
				'arms_S2.png' => array( -.05, .05 ),
				'hair_S6.png' => array( -.05, .05 ),
				'mouth_9.png' => array( -.05, .05 ),
				'mouth_6.png' => array( -.05, .05 ),
				'mouth_S2.png' => array( -.05, .05 )
			);
		
		var $randomColorParts = array( 'arms_3.png', 'arms_4.png', 'arms_5.png', 'arms_S1.png', 'arms_S3.png', 'arms_S5.png', 'arms_S6.png', 'arms_S7.png', 'arms_S9.png', 'hair_S1.png', 'hair_S2.png', 'hair_S3.png', 'hair_S5.png', 'legs_1.png', 'legs_2.png', 'legs_3.png', 'legs_5.png', 'legs_S1.png', 'legs_S2.png', 'legs_S3.png', 'legs_S4.png', 'legs_S6.png', 'legs_S7.png', 'legs_S10.png', 'legs_S12.png', 'mouth_3.png', 'mouth_4.png', 'mouth_7.png', 'mouth_10.png', 'mouth_S6.png' );
		
		//Generated from find_parts_dimensions
		var $partOptimization = array(
				'legs_1.png'	=>	array( array( 17, 99 ),		array( 58, 119 )	 ),
				'legs_2.png'	=>	array( array( 25, 94 ),		array( 54, 119 )	 ),
				'legs_3.png'	=>	array( array( 34, 99 ),		array( 48, 117 )	 ),
				'legs_4.png'	=>	array( array( 999999, 0 ),	array( 999999, 0 ) ),
				'legs_5.png'	=>	array( array( 28, 91 ),		array( 64, 119 )	 ),
				'legs_S1.png'	=>	array( array( 17, 105 ),	array( 53, 118 )	 ),
				'legs_S10.png'	=>	array( array( 42, 88 ),		array( 54, 118 )	 ),
				'legs_S11.png'	=>	array( array( 999999, 0),	array( 999999, 0 ) ),
				'legs_S12.png'	=>	array( array( 15, 107 ),	array( 60, 115 )	 ),
				'legs_S13.png'	=>	array( array( 8, 106 ),		array( 69, 119 )	 ),
				'legs_S2.png'	=>	array( array( 23, 99 ),		array( 56, 117 )	 ),
				'legs_S3.png'	=>	array( array( 30, 114 ),	array( 53, 118 )	 ),
				'legs_S4.png'	=>	array( array( 12, 100 ),	array( 50, 116 )	 ),
				'legs_S5.png'	=>	array( array( 17, 109 ),	array( 63, 118 )	 ),
				'legs_S6.png'	=>	array( array( 10, 100 ),	array( 56, 119 )	 ),
				'legs_S7.png'	=>	array( array( 33, 78 ),		array( 73, 114 )	 ),
				'legs_S8.png'	=>	array( array( 33, 95 ),		array( 102, 116 )	 ),
				'legs_S9.png'	=>	array( array( 42, 75 ),		array( 72, 116 )	 ),
				
				'hair_1.png'	=>	array( array( 999999, 0 ),	array( 999999, 0 ) ),
				'hair_2.png'	=>	array( array( 999999, 0 ),	array( 999999, 0 ) ),
				'hair_3.png'	=>	array( array( 999999, 0 ),	array( 999999, 0 ) ),
				'hair_4.png'	=>	array( array( 34, 84 ),		array( 0, 41 )		 ),
				'hair_5.png'	=>	array( array( 999999, 0 ),	array( 999999, 0 ) ),
				'hair_S1.png'	=>	array( array( 25, 96 ),		array( 2, 58 )		 ),
				'hair_S2.png'	=>	array( array( 45, 86 ),		array( 3, 51 )		 ),
				'hair_S3.png'	=>	array( array( 15, 105 ),	array( 4, 48 )		 ),
				'hair_S4.png'	=>	array( array( 15, 102 ),	array( 1, 51 )		 ),
				'hair_S5.png'	=>	array( array( 16, 95 ),		array( 4, 65 )		 ),
				'hair_S6.png'	=>	array( array( 28, 88 ),		array( 1, 48 )		 ),
				'hair_S7.png'	=>	array( array( 51, 67 ),		array( 6, 49 )		 ),
				
				'arms_1.png'	=>	array( array( 999999, 0 ),	array( 999999, 0)	 ),
				'arms_2.png'	=>	array( array( 999999, 0 ),	array( 999999, 0)	 ),
				'arms_3.png'	=>	array( array( 2, 119 ),		array( 20, 72 )	 ),
				'arms_4.png'	=>	array( array( 2, 115 ),		array( 14, 98 )	 ),
				'arms_5.png'	=>	array( array( 5, 119 ),		array( 17, 90 )	 ),
				'arms_S1.png'	=>	array( array( 0, 117 ),		array( 23, 109 )	 ),
				'arms_S2.png'	=>	array( array( 2, 118 ),		array( 8, 75 )		 ),
				'arms_S3.png'	=>	array( array( 2, 116 ),		array( 17, 93 )	 ),
				'arms_S4.png'	=>	array( array( 999999, 0 ),	array( 999999, 0)	 ),
				'arms_S5.png'	=>	array( array( 1, 115 ),		array( 6, 40 )		 ),
				'arms_S6.png'	=>	array( array( 3, 117 ),		array( 7, 90 )		 ),
				'arms_S7.png'	=>	array( array( 1, 116 ),		array( 21, 67 )	 ),
				'arms_S8.png'	=>	array( array( 2, 119 ),		array( 18, 98 )	 ),
				'arms_S9.png'	=>	array( array( 8, 110 ),		array( 18, 65 )	 ),
				
				'body_1.png'	=>	array( array( 22, 99 ),		array( 17, 90 )	 ),
				'body_10.png'	=>	array( array( 37, 85 ),		array( 22, 98 )	 ),
				'body_11.png'	=>	array( array( 23, 108 ),	array( 10, 106 )	 ),
				'body_12.png'	=>	array( array( 9, 113 ),		array( 6, 112 )	 ),
				'body_13.png'	=>	array( array( 29, 98 ),		array( 26, 97 )	 ),
				'body_14.png'	=>	array( array( 31, 93 ),		array( 25, 94 )	 ),
				'body_15.png'	=>	array( array( 23, 100 ),	array( 20, 97 )	 ),
				'body_2.png'	=>	array( array( 14, 104 ),	array( 16, 89 )	 ),
				'body_3.png'	=>	array( array( 22, 102 ),	array( 22, 93 )	 ),
				'body_4.png'	=>	array( array( 18, 107 ),	array( 22, 103 )	 ),
				'body_5.png'	=>	array( array( 22, 101 ),	array( 12, 99 )	 ),
				'body_6.png'	=>	array( array( 24, 103 ),	array( 10, 92 )	 ),
				'body_7.png'	=>	array( array( 22, 99 ),		array( 7, 92 )		 ),
				'body_8.png'	=>	array( array( 21, 103 ),	array( 12, 95 )	 ),
				'body_9.png'	=>	array( array( 20, 99 ),		array( 9, 91 )		 ),
				'body_S1.png'	=>	array( array( 22, 102 ),	array( 25, 96 )	 ),
				'body_S2.png'	=>	array( array( 35, 94 ),		array( 17, 96 )	 ),
				'body_S3.png'	=>	array( array( 30, 100 ),	array( 20, 102 )	 ),
				'body_S4.png'	=>	array( array( 26, 104 ),	array( 14, 92 )	 ),
				'body_S5.png'	=>	array( array( 26, 100 ),	array( 16, 97 )	 ),
				
				'eyes_1.png'	=>	array( array( 43, 76 ),		array( 31, 48 )	 ),
				'eyes_10.png'	=>	array( array( 40, 80 ),		array( 32, 50 )	 ),
				'eyes_11.png'	=>	array( array( 41, 82 ),		array( 31, 54 )	 ),
				'eyes_12.png'	=>	array( array( 45, 78 ),		array( 30, 50 )	 ),
				'eyes_13.png'	=>	array( array( 10, 111 ),	array( 10, 34 )	 ),
				'eyes_14.png'	=>	array( array( 40, 79 ),		array( 21, 56 )	 ),
				'eyes_15.png'	=>	array( array( 49, 72 ),		array( 38, 43 )	 ),
				'eyes_2.png'	=>	array( array( 37, 72 ),		array( 36, 53 )	 ),
				'eyes_3.png'	=>	array( array( 47, 75 ),		array( 31, 53 )	 ),
				'eyes_4.png'	=>	array( array( 999999, 0),	array( 999999, 0 ) ),
				'eyes_5.png'	=>	array( array( 44, 77 ),		array( 43, 52 )	 ),
				'eyes_6.png'	=>	array( array( 43, 57 ),		array( 35, 49 )	 ),
				'eyes_7.png'	=>	array( array( 62, 76 ),		array( 35, 49 )	 ),
				'eyes_8.png'	=>	array( array( 45, 72 ),		array( 23, 51 )	 ),
				'eyes_9.png'	=>	array( array( 999999, 0),	array( 999999, 0 ) ),
				'eyes_S1.png'	=>	array( array( 41, 82 ),		array( 29, 52 )	 ),
				'eyes_S2.png'	=>	array( array( 999999, 0),	array( 999999, 0 ) ),
				'eyes_S3.png'	=>	array( array( 34, 88 ),		array( 39, 52 )	 ),
				'eyes_S4.png'	=>	array( array( 47, 74 ),		array( 39, 51 )	 ),
				'eyes_S5.png'	=>	array( array( 41, 76 ),		array( 36, 51 )	 ),
				
				'mouth_1.png'	=>	array( array( 999999, 0 ),	array( 999999, 0 ) ),
				'mouth_10.png'	=>	array( array( 40, 84 ),		array( 56, 89 )	 ),
				'mouth_2.png'	=>	array( array( 57, 65 ),		array( 56, 61 )	 ),
				'mouth_3.png'	=>	array( array( 38, 85 ),		array( 54, 72 )	 ),
				'mouth_4.png'	=>	array( array( 44, 77 ),		array( 56, 81 )	 ),
				'mouth_5.png'	=>	array( array( 53, 72 ),		array( 59, 76 )	 ),
				'mouth_6.png'	=>	array( array( 48, 74 ),		array( 56, 77 )	 ),
				'mouth_7.png'	=>	array( array( 51, 70 ),		array( 57, 80 )	 ),
				'mouth_8.png'	=>	array( array( 44, 81 ),		array( 64, 78 )	 ),
				'mouth_9.png'	=>	array( array( 49, 75 ),		array( 52, 103 )	 ),
				'mouth_S1.png'	=>	array( array( 47, 82 ),		array( 57, 73 )	 ),
				'mouth_S2.png'	=>	array( array( 45, 71 ),		array( 65, 84 )	 ),
				'mouth_S3.png'	=>	array( array( 48, 77 ),		array( 56, 86 )	 ),
				'mouth_S4.png'	=>	array( array( 46, 77 ),		array( 56, 73 )	 ),
				'mouth_S5.png'	=>	array( array( 55, 69 ),		array( 55, 98 )	 ),
				'mouth_S6.png'	=>	array( array( 40, 79 ),		array( 56, 72 )	 ),
				'mouth_S7.png'	=>	array( array( 999999, 0 ),	array( 999999, 0 ) )
			);
		var $startTime;
		var $monsterid_options;
		
		//function monsterid(){
		function __construct(){
			//get the options
			$this->monsterid_options = $this->get_options();
		}
		
		function findparts( $partsarray ){
			
			$dir = WP_MONSTERPARTS_DIR;
			$noparts = true;
			
			if( $dh = opendir( $dir ) ){
				
				while( ( $file = readdir( $dh ) ) !== false ){
					
					if( is_file( $dir . $file ) ){
						$partname = explode( '_', $file );
						$partname = $partname[0];
						
						if( array_key_exists( $partname, $partsarray ) ){
							array_push( $partsarray[$partname], $file );
							$noparts = false;
						}
						
					}
					
				}
				
			}
			
			closedir( $dh );
			
			if( $noparts )
				return false;
				
			//sort for consistency across servers
			foreach( $partsarray as $key => $value)
				sort( $partsarray[$key] );
			
			return $partsarray;
			
		}
		
		function get_options( $check = false ){
			
			if( ! isset( $this->monsterid_options ) || $check ){
				
				$monsterID_array = get_option( 'monsterID' );
				
				if( ! isset( $monsterID_array['size'] ) || ! isset( $monsterID_array['backb'] ) ){
					//Set Default Values Here
					$default_array = array(
							'size'		=> 65,
							'backr'		=> array( 220, 255 ),
							'backg'		=> array( 220, 255 ),
							'backb'		=> array( 220, 255 ),
							'legs'		=> 0,
							'autoadd'	=> 1,
							'gravatar'	=> 0,
							'artistic'	=> 0,'
							greyscale'	=> 1,
							'css'			=> DEFAULT_MONSTERID_CSS
						);
					
					add_option( 'monsterID', $default_array, '', false); //deprecated 'description' removed ('Options used by MonsterID)
					
					$monsterID_array = $default_array;
				}
				
				$this->monsterid_options = $monsterID_array;
			}
			
			return $this->monsterid_options;
			
		}
		
		function get_option( $option ){
			
			$options = $this->monsterid_options;
			
			//Check first
			if( ! isset( $this->monsterid_options ) ) $options = $this->get_options();
			
			if( array_key_exists( $option, $options ) ) return $options[$option];
			
			else return false;
			
		}
		
		function monster_count(){
			
			$monsterID_count = 0;
			$dir = WP_MONSTERID_DIR_INTERNAL;
			
			if( $dh = opendir( $dir ) ){
				
				while( ( $file = readdir( $dh ) ) !== false ){
					
					if( is_file( $dir . $file ) and preg_match('/^.*\.png$/', $file ) ){
						$monsterID_count++;
					}
				}
			}
			
			return $monsterID_count;
			
		}
		
		function find_parts_dimensions( $text = false ){
			
			$parts_array = array('legs' => array(), 'hair' => array(), 'arms' => array(), 'body' => array(), 'eyes' => array(), 'mouth' => array() );
			$parts = $this->findparts( $parts_array );
			$bounds = array();
			
			foreach( $parts as $key => $value ){
				foreach( $value as $part ){
					
					$file = WP_MONSTERPARTS_DIR . $part;
					$im = imagecreatefrompng( $file );
					$imgw = imagesx( $im );
					$imgh = imagesy( $im );
					$xbounds = array( 999999, 0 );
					$ybounds = array( 999999, 0 );
					
					for( $i = 0; $i < $imgw; $i++ ){
						for( $j = 0; $j < $imgh; $j++ ){
							
							$rgb = ImageColorAt( $im, $i, $j );
							$r = ( $rgb >> 16 ) & 0xFF;
							$g = ( $rgb >> 8 ) & 0xFF;
							$b = $rgb & 0xFF;
							$alpha = ( $rgb & 0x7F000000 ) >> 24;
							$lightness = ( $r + $g + $b ) / 3 / 255;
							
							if( $lightness > .1 && $lightness < .99 && $alpha < 115 ){
								$xbounds[0] = min( $xbounds[0], $i );
								$xbounds[1] = max( $xbounds[1], $i );
								$ybounds[0] = min( $ybounds[0], $j );
								$ybounds[1] = max( $ybounds[1], $j );
							}
							
						}
					}
					
					$text .= "'$part' => array( array( ${xbounds[0]}, ${xbounds[1]} ), array( ${ybounds[0]}, ${ybounds[1]} ) ), ";
					$bounds[$part] = array( $xbounds, $ybounds );
					
				}
			}
			
			if( $text )
				return $text;
			else
				return $bounds;
			
		}
		
		function build_monster( $seed = '', $altImgText = '', $img = true, $size = '', $write = true, $displaySize = '', $gravataron = true ){
			
			if( function_exists( "gd_info" ) && is_writable( WP_MONSTERID_DIR_INTERNAL ) ){
				
				// init random seed
				$id = substr( sha1( $seed ), 0, 8 );
				
				//use admin email as salt. should be safe
				$filename = substr( sha1( $id . substr( get_option( 'admin_email' ), 0, 5 ) ), 0, 15 ) . '.png';
				$monsterID_options = $this->get_options();	
				
				if( $size == '' ) $size = $monsterID_options['size'];
				
				if( $displaySize == '' ) $displaySize = $size;
				
				if (!file_exists(WP_MONSTERID_DIR_INTERNAL.$filename)){
					
					if( ! isset( $this->startTime ) ) $this->startTime = time();
					
					#make sure nobody waits more than 5 seconds
					if( time() - $this->startTime > WP_MONSTERID_MAXWAIT ){
						$user = wp_get_current_user();
						
						#Let it go longer if the user is an admin
						if( $user->user_level < 8 || time() - $this->startTime > 14 ) return false;
					}
					
					//check if transparent
					if( array_sum( $monsterID_options['backr'] ) + array_sum( $monsterID_options['backg'] ) + array_sum( $monsterID_options['backb'] ) > 0){
						$transparent = false;
						
					}else{
						$transparent = true;
						
					}
					
					if( $monsterID_options['artistic'] ){
						$parts_array = array( 'legs' => array(), 'hair' => array(), 'arms' => array(), 'body' => array(), 'eyes' => array(), 'mouth' => array() );
						
					}elseif( $monsterID_options['legs'] == 1 && ! $monsterID_options['artistic'] ){
						$parts_array = array( 'woldlegs' => array(), 'woldhair' => array(), 'woldarms' => array(), 'woldbody' => array(), 'oldeyes' => array(), 'oldmouth' => array() );
						
					}else{
						$parts_array = array( 'oldlegs' => array(), 'oldhair' => array(), 'oldarms' => array(), 'oldbody' => array(), 'oldeyes' => array(), 'oldmouth' => array() );
						
					}
					
					$parts_order = array_keys( $parts_array );
					
					//get possible parts files
					$parts_array = $this->findparts( $parts_array );
					
					if( ! $parts_array ) return false;
					
					//set randomness
					$twister = new mid_mersenne_twister( hexdec( $id ) );
					
					// throw the dice for body parts
					foreach( $parts_order as $part ){
						$parts_array[$part] = $parts_array[$part][$twister->array_rand( $parts_array[$part] )];
					}
					
					// create backgound
					$file = WP_MONSTERPARTS_DIR . 'back.png';
					$monster =  @imagecreatefrompng($file);
					
					if( ! $monster ) return false; //something went wrong but don't want to mess up blog layout
					
					$hue = $twister->real_halfopen();
					$saturation = $twister->rand( 25000, 100000 ) / 100000;
					
					//Pick a back color even if transparent to preserve random draws across servers		
					$back = imagecolorallocate( $monster, $twister->rand( $monsterID_options['backr'][0], $monsterID_options['backr'][1] ), $twister->rand( $monsterID_options['backg'][0], $monsterID_options['backg'][1] ), $twister->rand( $monsterID_options['backb'][0], $monsterID_options['backb'][1] ) );
					
					$lightness = $twister->rand( 25000, 90000 ) / 100000; //Don't actually use this if artistic but preserves randomness
					
					if( ! $transparent ){
						imagefill( $monster, 0, 0, $back );
					}
					
					// add parts
					foreach( $parts_order as $part ){
						$file = $parts_array[$part];
						$file = WP_MONSTERPARTS_DIR . $file;
						$im = @imagecreatefrompng( $file );
						
						if( ! $im ) return false; //something went wrong but don't want to mess up blog layout
						
						imageSaveAlpha( $im, true );
						
						if( $monsterID_options['artistic'] && $monsterID_options['greyscale'] ){
							
							//randomly color body parts
							if( $monsterID_options['legs'] && in_array( $parts_array[$part], $this->whiteParts ) ){
								$this->image_whitize( $im );
							}
							
							if($part == 'body'||$part == 'wbody'){
								//imagefill($monster,60,60,$body);
								$this->image_colorize( $im, $hue, $saturation, $parts_array[$part] );
								
							}elseif( in_array( $parts_array[$part], $this->sameColorParts ) ){
								$this->image_colorize( $im, $hue, $saturation, $parts_array[$part] );
								
							}elseif( in_array( $parts_array[$part], $this->randomColorParts ) ){
								$this->image_colorize( $im, $twister->real_halfopen(), $twister->rand( 25000, 100000 ) / 100000, $parts_array[$part] );
								
							}elseif( array_key_exists( $parts_array[$part], $this->specificColorParts ) ){
								$low = $this->specificColorParts[$parts_array[$part]][0] * 10000;
								$high = $this->specificColorParts[$parts_array[$part]][1] * 10000;
								$this->image_colorize( $im, $twister->rand( $low, $high ) / 10000, $twister->rand( 25000, 100000 ) / 100000, $parts_array[$part] );
								
							}
							
						}else{
							
							if( $part == 'oldbody' || $part == 'woldbody' ){
								$rgb_color = $this->HSL2hex( array( $hue, $saturation, $lightness ) );
								$body = imagecolorallocate( $im, $rgb_color[0], $rgb_color[1], $rgb_color[2] );
								imagefill( $im, 60, 60, $body );
								
							}
						}
						
						imagecopy( $monster, $im, 0, 0, 0, 0, 120, 120 );
						imagedestroy( $im );
						
					}
					
					// going to resize always for now
					$out = @imagecreatetruecolor( $size, $size );
					
					if( ! $out ) return false; //something went wrong but don't want to mess up blog layout
					
					if( $transparent ){
						imageSaveAlpha( $out, true );
						imageAlphaBlending( $out, false );
						
					}
					
					imagecopyresampled( $out, $monster, 0, 0, 0, 0, $size, $size, 120, 120 );
					imagedestroy( $monster );
					
					if( $write ){
						$wrote = @imagepng( $out, WP_MONSTERID_DIR_INTERNAL . $filename );
						
						if( ! $wrote ) return false; //something went wrong but don't want to mess up blog layout
						
					}else{
						header( "Content-type: image/png" );
						imagepng( $out );
						
					}
					
					imagedestroy( $out );
					
				}
				
				$filename = get_option('siteurl') . WP_MONSTERID_DIR . $filename;
				
				if( $monsterID_options['gravatar'] && $gravataron )
						$filename = "http://www.gravatar.com/avatar.php?gravatar_id=" . md5( strtolower( trim( $seed ) ) ) . "&amp;&;size=$size&amp;default=$filename";
						
				if( $img ){
					$filename = '<img class="monsterid" src="' . $filename . '" alt="' . str_replace( '"', "'", $altImgText ).' MonsterID Icon" height="' . $displaySize . '" width="' . $displaySize . '"/>';
					
				}
				
				return $filename;
				
			}else{ //php GD image manipulation is required
				return false; //php GD image isn't installed or file isn't writeable but don't want to mess up blog layout
			}
			
		}
		
		function image_colorize( &$im, $hue = 1, $saturation = 1, $part = '' ){
			
			$imgw = imagesx( $im );
			$imgh = imagesy( $im );
			
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
			
			imagealphablending( $im, false );
			
			if( $optimize = $this->partOptimization[$part] ){
				$xmin = $optimize[0][0];
				$xmax = $optimize[0][1];
				$ymin = $optimize[1][0];
				$ymax = $optimize[1][1];
				
			}else{
				$xmin = 0;
				$xmax = $imgw - 1;
				$ymin = 0;
				$ymax = $imgh - 1;
				
			}
			
			for( $i = $xmin; $i <= $xmax; $i++ ){
				for( $j = $ymin; $j <= $ymax; $j++ ){
					
					$rgb = ImageColorAt( $im, $i, $j );
					$r = ( $rgb >> 16 ) & 0xFF;
					$g = ( $rgb >> 8 ) & 0xFF;
					$b = $rgb & 0xFF;
					$alpha = ( $rgb & 0x7F000000 ) >> 24;
					$lightness = ( $r + $g + $b ) / 3 / 255;
					
					if( $lightness > .1 && $lightness < .99 && $alpha < 115 ){
						$newrgb = $this->HSL2hex( array( $hue, $saturation, $lightness ) );
						$color = imagecolorallocatealpha( $im, $newrgb[0], $newrgb[1], $newrgb[2], $alpha );
						imagesetpixel( $im, $i, $j, $color );
					}
					
				}
			}
			
			imagealphablending( $im, true);
			return( $im );
			
		}
		
		function image_whitize( &$im ){
			
			$imgw = imagesx( $im );
			$imgh = imagesy( $im );
			imagealphablending( $im, false );
			
			for( $i = 0; $i < $imgh; $i++ ){
				for( $j = 0; $j < $imgw; $j++ ){
					
					$rgb = ImageColorAt( $im, $i, $j );
					$r = ( $rgb >> 16 ) & 0xFF;
					$g = ( $rgb >> 8 ) & 0xFF;
					$b = $rgb & 0xFF;
					$alpha = ( $rgb & 0x7F000000 ) >> 24;
					$lightness = ( $r + $g + $b ) / 3 / 255;
					
					if( $lightness <= .1 && $alpha < 115 ){
						$newrgb = $this->HSL2hex( array( 0, 0, 1 - $lightness ) );
						$color = imagecolorallocatealpha( $im, $newrgb[0], $newrgb[1], $newrgb[2], $alpha );
						imagesetpixel( $im, $i, $j, $color );
						
					}
					
				}
			}
			
			imagealphablending( $im, true );
			imageSaveAlpha( $im, true );
			
			return( $im );
		}
		
		function HSL2hex( $hsl ){
			
			$hue = $hsl[0];
			$saturation = $hsl[1];
			$lightness = $hsl[2];
			
			if( $saturation == 0 ){
				$red = $lightness * 255;
				$green = $lightness * 255;
				$blue = $lightness * 255;
				
			}else{
				
				if( $lightness < 0.5 ){
					$var_2 = $lightness * ( 1 + $saturation );
					
				}else{
					$var_2 = ( $lightness + $saturation ) - ( $saturation * $lightness );
				}
				
				$var_1 = 2 * $lightness - $var_2;
				$red = 255 * $this->hue_2_rgb( $var_1, $var_2, $hue + ( 1 / 3 ) );
				$green = 255 * $this->hue_2_rgb( $var_1, $var_2, $hue - ( 1 / 3 ) );
				$blue = 255 * $this->hue_2_rgb( $var_1, $var_2, $hue );
				
			}
			
			return array( $red, $green, $blue );
			
		}
		
		function hue_2_rgb($v1,$v2,$vh){
			
			if( $vh < 0 ){
				$vh += 1;
				
			}elseif( $vh > 1 ){
				$vh -= 1;
				
			}
			
			if( ( 6 * $vh ) < 1 ){
				$output = $v1 + ( $v2 - $v1 ) * 6 * $vh;
				
			}elseif( ( 2 * $vh ) < 1 ){
				$output = $v2;
				
			}elseif( ( 3 * $vh ) < 2 ){
				$output = $v1 + ( $v2 - $v1 ) * ( ( 2 / 3 - $vh ) * 6 );
				
			}else{
				$output = $v1;
			}
			
			return($output);
			
		}
		
	} //END OF CLASS
	
	#Create a monsterid for later use
	global $monsterid;
	$monsterid = new monsterid();
	
	
	function monsterid_build_monster( $seed = '', $altImgText = '', $img = true, $size = '', $write = true, $displaySize = '', $gravataron = true ){
		
		global $monsterid;
		
		if( ! isset( $monsterid ) ) $monsterid = new monsterid();
		
		if( isset( $monsterid ) ){
			return $monsterid->build_monster( $seed, $altImgText, $img, $size, $write, $displaySize, $gravataron );
			
		}else return false;
		
	}
	
	function monsterid_comment_author( $output ){
		
		global $comment;
		global $monsterid;
		//echo '8' . $output . '8';
		if( ! isset( $monsterid ) ) return $output;

		$monsterid_options = $monsterid->get_options();
		
		if( ( is_page () || is_single () ) && $monsterid_options['autoadd'] == 1 && $comment->comment_type != "pingback" && $comment->comment_type != "trackback" &&  isset( $comment->comment_karma ) ){ //assuming sidebar widgets won't check comment karma (and single page comments will))
			//echo '99';
			//echo $output;
			$output = monsterid_build_monster( $comment->comment_author_email, $comment->comment_author ) . ' ' . $output;
		}
		
		return $output;
		
	}
	
	function monsterid_get_avatar_url( $url, $id_or_email, $args ){
		
		global $monsterid;
		
		if ( is_admin() && function_exists( 'get_current_screen' ) && $screen = get_current_screen() ) {
			$screen = empty( $screen ) ? false : $screen->id;
			if ( in_array( $screen, array( 'dashboard', 'edit-comments' ) ) ) {
					$args['default'] = 'monsterid';
			}
		}
		
		$force_default = isset( $args['force_default'] ) ? $args['force_default'] : false;
		
		if( $force_default && isset( $args['default'] ) && $args['default'] !== 'monsterid' ){
			return $url;
			
		}
		
		if( $args['default'] == 'monsterid' ){
			
			if( $id_or_email instanceof WP_Comment ){
				
				$comment_type = get_comment_type( $id_or_email );
				$is_avatar_comment_type = is_avatar_comment_type( $comment_type );
				
				if ( ! $is_avatar_comment_type ) {
					$args['url'] = false;
					
					return apply_filters( 'get_avatar_data', $args, $id_or_email );
				}
			}
			
			//Use get_option size for getting the gravatar use $size to build url
			$gen_size = $monsterid->get_option( 'size' );
			$size = $args['size'];
			$email = '';
			$gravataron = $monsterid->get_option( 'gravatar' );
			
			if( is_numeric( $id_or_email ) ){
				$id = (int) $id_or_email;
				$user = get_userdata($id);
				
				if( $user )
					$email = $user->user_email;
					
			}elseif( is_object( $id_or_email ) ) {
				
				if( ! empty( $id_or_email->user_id ) ){
					$id = (int) $id_or_email->user_id;
					$user = get_userdata($id);
					
					if( $user)
						$email = $user->user_email;
						
				}elseif( ! empty( $id_or_email->comment_author_email ) ) {
					$email = $id_or_email->comment_author_email;
					
				}
			}else{
				$email = $id_or_email;
				
			}
			
			$monsteridurl = monsterid_build_monster( $email, '', false, '', true, $size, false );
			
			if( $gravataron == 0 ){
				
				$url = $monsteridurl;
				
			}elseif( $gravataron == 1 ){
				
				$url_args = array( 's' => $size, 'd' => $monsteridurl );
				
				$gravatar_url = 'https://secure.gravatar.com/avatar/';
				$gravatar_url .= md5( strtolower( trim( $email ) ) );
				
				$url = esc_url( add_query_arg( rawurlencode_deep( array_filter( $url_args ) ), $gravatar_url ) );
				
			}
			
		}
		
		return $url;
		
	}
	
	function monsterid_style(){
		
		global $monsterid;
		
		$options = $monsterid->get_options();
		
		if( $css = $options['css'] ){
			
?>
<style type="text/css">
<?php
	echo $css;
?>
</style>
<?php

		}
		
	}
	
	
	//Hooks
	//https://developer.wordpress.org/?s=avatar
	add_action( 'admin_menu', 'monsterid_menu' );
	add_action( 'admin_init', 'monsterid_settings' );
	add_action( 'admin_head', 'monsterid_add_js' );
	add_filter( 'get_comment_author','monsterid_comment_author' );
	add_action( 'wp_head', 'monsterid_style' );
	
	add_filter( 'avatar_defaults', 'monsterid_avatar_defaults' );
	//add settings link to plugin list
	
	add_filter( 'get_avatar_url', 'monsterid_get_avatar_url', 5, 3 );
		
	
	class mid_mersenne_twister{
		//MySQL version doesn't work since they shut down integer overflow switching to:
		//https://github.com/ruafozy/php-mersenne-twister/blob/master/src/mersenne_twister.php
		
//		function mid_mersenne_twister( $seed = 123456 ){
		function __construct( $seed = 123456 ){
			
			$this->bits32 = PHP_INT_MAX == 2147483647;
			$this->define_constants();
			$this->init_with_integer( $seed );
			
		}
		
		function define_constants(){
			
			$this->N = 624;
			$this->M = 397;
			$this->MATRIX_A = 0x9908b0df;
			$this->UPPER_MASK = 0x80000000;
			$this->LOWER_MASK = 0x7fffffff;
			
			$this->MASK10 =~ ( ( ~0 ) << 10 ); 
			$this->MASK11 =~ ( ( ~0 ) << 11 ); 
			$this->MASK12 =~ ( ( ~0 ) << 12 ); 
			$this->MASK14 =~ ( ( ~0 ) << 14 ); 
			$this->MASK20 =~ ( ( ~0 ) << 20 ); 
			$this->MASK21 =~ ( ( ~0 ) << 21 ); 
			$this->MASK22 =~ ( ( ~0 ) << 22 ); 
			$this->MASK26 =~ ( ( ~0 ) << 26 ); 
			$this->MASK27 =~ ( ( ~0 ) << 27 ); 
			$this->MASK31 =~ ( ( ~0 ) << 31 ); 
			
			$this->TWO_TO_THE_16 = pow( 2, 16 );
			$this->TWO_TO_THE_31 = pow( 2, 31 );
			$this->TWO_TO_THE_32 = pow( 2, 32 );
			
			$this->MASK32 = $this->MASK31 | ( $this->MASK31 << 1 );
			
		}
		
		function init_with_integer( $integer_seed ){
			
			$integer_seed = $this->force_32_bit_int( $integer_seed );
			
			$mt = &$this->mt;
			$mti = &$this->mti;
			
			$mt = array_fill( 0, $this->N, 0 );
			
			$mt[0] = $integer_seed;
			
			for( $mti = 1; $mti < $this->N; $mti++ ){
				
				$mt[$mti] = $this->add_2( $this->mul( 1812433253, ( $mt[$mti - 1] ^ ( ( $mt[$mti - 1] >> 30 ) & 3 ) ) ), $mti );
			/*
			mt[mti] =
				 (1812433253UL * (mt[mti-1] ^ (mt[mti-1] >> 30)) + mti);
			 */
			}
			
		}
		
		/* generates a random number on [0,1)-real-interval */
		function real_halfopen(){
			
			return $this->signed2unsigned( $this->int32()) * ( 1.0 / 4294967296.0 );
			
		}
		
		function int32(){
			
			$mag01 = array( 0, $this->MATRIX_A );
			
			$mt = &$this->mt;
			$mti = &$this->mti;
			
			if( $mti >= $this->N ){ /* generate N words all at once */
			
				for( $kk = 0; $kk < $this->N - $this->M; $kk++ ){
					$y = ( $mt[$kk] &$this->UPPER_MASK ) | ( $mt[$kk + 1 ] &$this->LOWER_MASK );
					$mt[$kk] = $mt[$kk + $this->M] ^ ( ( $y >> 1 ) &$this->MASK31 ) ^ $mag01[$y & 1];
					
				}
				
				for( ; $kk < $this->N - 1; $kk++ ){
					$y = ( $mt[$kk] &$this->UPPER_MASK ) | ( $mt[$kk + 1] &$this->LOWER_MASK );
					$mt[$kk] = $mt[ $kk + ( $this->M - $this->N )] ^ ( ( $y >> 1)  &$this->MASK31 ) ^ $mag01[$y & 1];
					
				}
				
				$y = ( $mt[$this->N - 1] &$this->UPPER_MASK ) | ( $mt[0] &$this->LOWER_MASK );
				$mt[$this->N - 1] = $mt[$this->M - 1] ^ ( ( $y >> 1 ) &$this->MASK31 ) ^ $mag01[$y & 1];
				
				$mti = 0;
			}
			
			$y = $mt[$mti++];
			
			/* Tempering */
			$y ^= ( $y >> 11 ) &$this->MASK21;
			$y ^= ( $y << 7 ) & ( ( 0x9d2c << 16 ) | 0x5680 );
			$y ^= ( $y << 15 ) & ( 0xefc6 << 16 );
			$y ^= ( $y >> 18 ) &$this->MASK14;
			
			return $y;
			
		}
		
		function signed2unsigned( $signed_integer ){
			## assert(is_integer($signed_integer));
			## assert(($signed_integer & ~$this->MASK32) === 0);
			
			return $signed_integer >= 0? $signed_integer : $this->TWO_TO_THE_32 + $signed_integer;
				
		}
		
		function unsigned2signed( $unsigned_integer ){
			## assert($unsigned_integer >= 0);
			## assert($unsigned_integer < pow(2, 32));
			## assert(floor($unsigned_integer) === floatval($unsigned_integer));
			
			return intval( $unsigned_integer < $this->TWO_TO_THE_31 ? $unsigned_integer : $unsigned_integer - $this->TWO_TO_THE_32 );
			
		}
		
		function force_32_bit_int( $x ){
			/*
				it would be un-PHP-like to require is_integer($x),
				so we have to handle cases like this:
				
					$x === pow(2, 31)
					$x === strval(pow(2, 31))
				
				we are also opting to do something sensible (rather than dying)
				if the seed is outside the range of a 32-bit unsigned integer.
			*/
			
			if( is_integer( $x ) ){
				/* 
					we mask in case we are on a 64-bit machine and at least one
					bit is set between position 32 and position 63.
	  			*/
				return $x &$this->MASK32;
				
			}else{
				$x = floatval( $x );
				
				$x = $x < 0? ceil( $x ) : floor( $x );
				
				$x = fmod( $x, $this->TWO_TO_THE_32 );
				
				if( $x < 0 )
					$x += $this->TWO_TO_THE_32;
					
				return $this->unsigned2signed($x);
				
			}
			
		}
		
		/*
			takes 2 integers, treats them as unsigned 32-bit integers,
			and adds them.
			
			it works by splitting each integer into
			2 "half-integers", then adding the high and low half-integers
			separately.
			
			a slight complication is that the sum of the low half-integers
			may not fit into 16 bits; any "overspill" is added to the sum
			of the high half-integers.
		*/ 
		function add_2( $n1, $n2 ){
			
			$x = ( $n1 & 0xffff ) + ( $n2 & 0xffff );
			
			return ( ( ( ( $n1 >> 16 ) & 0xffff ) + ( ( $n2 >> 16 ) & 0xffff ) + ( $x >> 16 ) ) << 16 ) | ( $x & 0xffff );
			
		}
		
		function mul($a, $b) {
			/*
				a and b, considered as unsigned integers, can be expressed as follows:
				
				a = 2**16 * a1 + a2,
				
				b = 2**16 * b1 + b2,
				
				where
				
				0 <= a2 < 2**16,
				0 <= b2 < 2**16.
				
				given those 2 equations, what this function essentially does is to
				use the following identity:
				
				a * b = 2**32 * a1 * b1 + 2**16 * a1 * b2 + 2**16 * b1 * a2 + a2 * b2
				
				note that the first term, i.e. 2**32 * a1 * b1, is unnecessary here,
				so we don't compute it.
				
				we could make the following code clearer by using intermediate
				variables, but that would probably hurt performance.
			*/
			
			return
				$this->unsigned2signed(
					fmod(
						$this->TWO_TO_THE_16 *
	  /*
		 the next line of code calculates a1 * b2,
		 the line after that calculates b1 * a2, 
		 and the line after that calculates a2 * b2.
		*/
						( ( ( $a >> 16 ) & 0xffff ) * ( $b & 0xffff ) +
						( ( $b >> 16 ) & 0xffff ) * ( $a & 0xffff ) ) +
						( $a & 0xffff ) * ( $b & 0xffff ),

						$this->TWO_TO_THE_32
					)
				);
			
		}
		
		function rand( $low, $high ){
			
			$pick = floor( $low + ( $high - $low + 1 ) * $this->real_halfopen() );
			
			return ( $pick );
			
		}
		
		function array_rand( $array ){
			
			return ( $this->rand( 0, count( $array ) - 1 ) );
			
		}
		
	}
	
	
	//Widget stuff 
	//Wordpress's default widget doesn't get commenter email so we can't use it for monsterids
	//Copying their widget with some search and replace with monsterid
	function monsterid_recent_comments( $args ){
		
		global $wpdb, $comments, $comment, $monsterid;
		
		extract($args, EXTR_SKIP);
		
		$options = get_option( 'widget_monsterid_recent_comments' );
		$title = empty( $options['title'] ) ? __( 'Recent Comments', 'wp-monsterid' ) : $options['title'];
		
		if( ! $number = (int) $options['number'] )
			$number = 5;
		elseif( $number < 1 )
			$number = 1;
		elseif( $number > 15 )
			$number = 15;
			
		if( !$size = (int) $options['monsterid_size'] )
			$size = 30;
		elseif( $size < 5 )
			$size = 5;
		elseif( $size > 80 )
			$size = 80;
			
		if ( ! $comments = wp_cache_get( 'monsterid_recent_comments', 'widget' ) ) {
			$comments = $wpdb->get_results( "SELECT comment_author, comment_author_url, comment_ID, comment_post_ID, comment_author_email, comment_type FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $number" );
			wp_cache_add( 'monsterid_recent_comments', $comments, 'widget' );
			
		}
?>

		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul id="monsterid_recentcomments"><?php
			if ( $comments ) : foreach ( $comments as $comment ) :
				echo  '<li class="recentcomments">';
				if( $comment->comment_type != "pingback" && $comment->comment_type != "trackback" )
					echo monsterid_build_monster( $comment->comment_author_email, $comment->comment_author, true, '', true, $size );
				echo sprintf( __( '%1$s on %2$s' ), get_comment_author_link(), '<a href="' . get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID . '">' . get_the_title( $comment->comment_post_ID ) . '</a>' ) . '</li>';
			endforeach; endif;?></ul>
		<?php echo $after_widget; ?>
<?php
	}
	
	function wp_delete_monsterid_recent_comments_cache(){
		
		wp_cache_delete( 'monsterid_recent_comments', 'widget' );
		
	}
	
	function monsterid_recent_comments_control(){
		
		$options = $newoptions = get_option( 'widget_monsterid_recent_comments' );
		
		if( $_POST["monsterid_recent-comments-submit"] ){
			$newoptions['title'] = strip_tags(stripslashes( $_POST["monsterid_recent-comments-title"] ) );
			$newoptions['number'] = (int) $_POST["monsterid_recent-comments-number"];
			$newoptions['monsterid_size'] = (int) $_POST["monsterid_size"];
			$newoptions['monsterid_css'] =  $_POST["monsterid_css"];
			
		}
		
		if( $_POST["monsterid_css_reset"] )
			$newoptions['monsterid_css'] = DEFAULT_MONSTERID_RECENTCOMMENTS_CSS;
			
		if( $options != $newoptions ){
			$options = $newoptions;
			
			update_option( 'widget_monsterid_recent_comments', $options );
			wp_delete_monsterid_recent_comments_cache();
			
		}
		
		$title = attribute_escape( $options['title'] );
		
		if( ! $number = (int) $options['number'] )
			$number = 5;
			
		if( !$size = (int) $options['monsterid_size'] )
			$size = 25;
			
		if( ! $css = stripslashes($options['monsterid_css'] ) )
			$css = DEFAULT_MONSTERID_RECENTCOMMENTS_CSS;
			
?>
			<p><label for="monsterid_recent-comments-title"><?php _e( 'Title:', 'wp-monsterid' ); ?> <input style="width: 250px;" id="monsterid_recent-comments-title" name="monsterid_recent-comments-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label for="monsterid_recent-comments-number"><?php _e( 'Number of comments to show:', 'wp-monsterid' ); ?> <input style="width: 25px; text-align: center;" id="monsterid_recent-comments-number" name="monsterid_recent-comments-number" type="text" value="<?php echo $number; ?>" /></label> <?php _e( '(at most 15)', 'wp-monsterid' ); ?></p>
			<p><label for="monsterid_size"><?php _e( 'Size of Widget MonsterIDs (pixels):', 'wp-monsterid' ); ?> <input style="width: 25px; text-align: center;" id="monsterid_size" name="monsterid_size" type="text" value="<?php echo $size; ?>" /></label></p>
			<p><label for="monsterid_css"><?php _e( 'CSS for Widget:', 'wp-monsterid' ); ?><textarea id="monsterid_css" name="monsterid_css" rows="3" cols="55" /><?php echo $css;?></textarea></label></p>
			<p><label for="monsterid_css_reset"><?php _e( 'Reset CSS to Default:', 'wp-monsterid' ); ?> <input id="monsterid_css_reset" name="monsterid_css_reset" type="submit" value="Reset CSS" /></label></p>
			<input type="hidden" id="monsterid_recent-comments-submit" name="monsterid_recent-comments-submit" value="1" />
<?php
	}
	
	function monsterid_recent_comments_style(){
		
		$options = get_option( 'widget_monsterid_recent_comments' );
		
		if( ! $css = stripslashes($options['monsterid_css'] ) )
			$css = DEFAULT_MONSTERID_RECENTCOMMENTS_CSS;
			
?>
<style type="text/css">
<?php echo $css; ?>
</style>
<?php

	}
	
	function monsterid_recent_comments_widget_init(){
		
//		wp_register_sidebar_widget( id, name, call_back, )		
		wp_register_sidebar_widget( 'monsterid_recent_comments', 'Recent Comments (with MonsterIDs)', 'monsterid_recent_comments' );
		wp_register_widget_control( 'monsterid_recent_comments', 'Recent Comments (with MonsterIDs)', 'monsterid_recent_comments_control', 400, 250 );
		
		if ( is_active_widget( 'monsterid_recent_comments' ) )
			add_action( 'wp_head', 'monsterid_recent_comments_style' );
			
		add_action( 'comment_post', 'wp_delete_monsterid_recent_comments_cache' );
		add_action( 'wp_set_comment_status', 'wp_delete_monsterid_recent_comments_cache' );
		
	}
	
	add_action( 'widgets_init', 'monsterid_recent_comments_widget_init' );
	
?>
