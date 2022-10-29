<?php
	
	function monsterid_menu(){
		
		if( function_exists( 'add_options_page' ) ){
			//User capability was 1 (level_1) - using manage_options
			add_options_page( __( 'MonsterID Control Panel', 'wp-monsterid' ), 'MonsterID', 'manage_options', WP_MONSTERID_MENU_SLUG, 'monsterid_subpanel' ); //basename(__FILE__)
		}
		
	}
	
	add_action( 'admin_notices', 'monsterid_settings_notices' );
	
	function monsterid_settings_notices(){
		
		if( get_current_screen()->id == 'settings_page_wp_monsterid' ){
			
			$url = 'https://wordpress.org/support/article/changing-file-permissions/';
			
			//Add errors for page load
				if(!  is_writable( WP_MONSTERID_DIR_INTERNAL ) ){
					
					add_settings_error(
						'wp_monsterid_page_load', 'file-permission',
						sprintf(
							/* translators: 1: path to a folder 2: url for link */
							__( 'MonsterID needs %1$s to be <a href="%2$s">writable</a>', 'wp-monsterid' ),
							'<code>' . WP_MONSTERID_DIR_INTERNAL . '</code>',
							$url
						)
					);
					
				}
				
	 			if(!  function_exists( "gd_info" ) ){
	 				
					add_settings_error(
						'wp_monsterid_page_load', 'gd-library',
						__( 'GD Image library not found. MonsterID needs this library.', 'wp-monsterid' ),
					);
					
	 			}

		}else{
			return;
			
		}
		
	}
	
	function monsterid_settings(){
		
		register_setting( 'wp_monsterid_settings', 'monsterID', array( 'sanitize_callback' => 'monsterid_settings_sanitize' ) );
		
		add_settings_section( 'wp_monsterid_section', '', 'monsterid_settings_section', 'wp_monsterid_settings' );
		
		add_settings_field( 'size', 'MonsterID Size', 'monsterid_settings_field_monstersize', 'wp_monsterid_settings', 'wp_monsterid_section' );
		
		add_settings_field( 'background', __( 'Background Colors', 'wp-monsterid' ), 'monsterid_settings_field_background', 'wp_monsterid_settings', 'wp_monsterid_section' );
/*		add_settings_field( 'backr', 'Red', 'monsterid_settings_field_backgroundcolor', 'wp_monsterid_settings', 'wp_monsterid_section', array( 'backr') );
		add_settings_field( 'backg', 'Green', 'monsterid_settings_field_backgroundcolor', 'wp_monsterid_settings', 'wp_monsterid_section', array( 'backg') );
		add_settings_field( 'backb', 'Blue', 'monsterid_settings_field_backgroundcolor', 'wp_monsterid_settings', 'wp_monsterid_section', array( 'backb') );*/
		
		add_settings_field( 'legs', __( 'Arm/Leg Color', 'wp-monsterid' ), 'monsterid_settings_field_legs', 'wp_monsterid_settings', 'wp_monsterid_section' );
		
//		add_settings_field( 'autoadd', __( 'Automatically Add MonsterID to Comments', 'wp-monsterid' ), 'monsterid_settings_field_autoadd', 'wp_monsterid_settings', 'wp_monsterid_section' );
		
		add_settings_field( 'gravatar', __( 'Gravatar Support', 'wp-monsterid' ), 'monsterid_settings_field_gravatar', 'wp_monsterid_settings', 'wp_monsterid_section' );
		
		add_settings_field( 'artistic', __( 'Artistic Monsters', 'wp-monsterid' ), 'monsterid_settings_field_artistic', 'wp_monsterid_settings', 'wp_monsterid_section' );
		
		add_settings_field( 'greyscale', __( 'Grey Scale Monsters', 'wp-monsterid' ), 'monsterid_settings_field_greyscale', 'wp_monsterid_settings', 'wp_monsterid_section' );
		
	}
	
	function monsterid_settings_section(){}
	
	function monsterid_settings_field_greyscale(){
		
		global $monsterid;
		$option_greyscale = $monsterid->get_option( 'greyscale' );
		
		?>

			<fieldset>
				<legend class="screen-reader-text"><span><?php _e( 'Greyscale Monsters', 'wp-monsterid' ); ?></span></legend>
				<label>
					<input type="radio" name="monsterID[greyscale]" value="0"<?php if( $option_greyscale == 0 ) echo ' checked="checked"'; ?>> <?php _e( 'Greyscale', 'wp-monsterid' ); ?>

				</label>
				<br>
				<label>
					<input type="radio" name="monsterID[greyscale]" value="1"<?php if( $option_greyscale == 1 ) echo ' checked="checked"'; ?>> <?php _e( 'Color', 'wp-monsterid' ); ?>

				</label>
				<p class="description"><?php _e( 'Greyscale artistic require less processing (Default: Color)', 'wp-monsterid' ); ?></p>
			</fieldset>
		<?php
		
	}
	
	function monsterid_settings_field_artistic(){
		
		global $monsterid;
		$option_artistic = $monsterid->get_option( 'artistic' );
		
		?>

			<fieldset>
				<legend class="screen-reader-text"><span><?php _e( 'Artistic Monsters', 'wp-monsterid' ); ?></span></legend>
				<label>
					<input type="radio" name="monsterID[artistic]" value="1"<?php if( $option_artistic == 1 ) echo ' checked="checked"'; ?>> <?php _e( 'Artistic', 'wp-monsterid' ); ?>

				</label>
				<br>
				<label>
					<input type="radio" name="monsterID[artistic]" value="0"<?php if( $option_artistic == 0 ) echo ' checked="checked"'; ?>> <?php _e( 'Original', 'wp-monsterid' ); ?>

				</label>
				<p class="description"><?php _e( 'Artistic monsters require more processing (Default: Artistic)', 'wp-monsterid' ); ?></p>
			</fieldset>
		<?php
		
	}
	
	function monsterid_settings_field_gravatar(){
		
		global $monsterid;
		$option_gravatar = $monsterid->get_option( 'gravatar' );
		
		?>

			<fieldset>
				<legend class="screen-reader-text"><span><?php _e( 'Gravatar Support', 'wp-monsterid' ); ?></span></legend>
				<label>
					<input type="radio" name="monsterID[gravatar]" value="0"<?php if( $option_gravatar == 0 ) echo ' checked="checked"'; ?>> <?php _e( 'MonsterID Only', 'wp-monsterid' ); ?>

				</label>
				<br>
				<label>
					<input type="radio" name="monsterID[gravatar]" value="1"<?php if( $option_gravatar == 1 ) echo ' checked="checked"'; ?>> <?php _e( 'Gravatar and MonsterID', 'wp-monsterid' ); ?>

				</label>
				<p class="description"><?php _e( 'If a commenter has a gravatar use it, otherwise use MonsterID (Default: MonsterID Only)', 'wp-monsterid' ); ?></p> 
			</fieldset>
		<?php
		
	}
	
	function monsterid_settings_field_autoadd(){
		
		global $monsterid;
		$option_autoadd = $monsterid->get_option( 'autoadd' );
		
		?>

			<fieldset>
				<legend class="screen-reader-text"><span><?php _e( 'Automatically add MonsterID to comments', 'wp-monsterid' ); ?></span></legend>
				<label>
					<input type="radio" name="monsterID[autoadd]" value="0"<?php if( $option_autoadd == 0 ) echo ' checked="checked"'; ?>> <?php _e( 'I\'ll Do It Myself', 'wp-monsterid' ); ?>

				</label>
				<br>
				<label>
					<input type="radio" name="monsterID[autoadd]" value="1"<?php if( $option_autoadd == 1 ) echo ' checked="checked"'; ?>> <?php _e( 'Add Monsters For Me', 'wp-monsterid' ); ?>

				</label>
				<br>
				<label>
					<input type="radio" name="monsterID[autoadd]" value="2"<?php if( $option_autoadd == 2 ) echo ' checked="checked"'; ?>> <?php _e( 'My Theme Has Builtin WP2.5+ Avatars', 'wp-monsterid' ); ?>

				</label>
				<p class="description"><?php _e( 'Adds a MonsterID icon automatically beside commenter names or disable it and edit theme file manually (Default: Auto)', 'wp-monsterid' ); ?></p> 
			</fieldset>
		<?php
		
	}
	
	function monsterid_settings_field_legs(){

		global $monsterid;
		$option_legs = $monsterid->get_option( 'legs' );
		
		?>

			<fieldset>
				<legend class="screen-reader-text"><span>Arm and leg colour</span></legend>
				<label>
					<input type="radio" name="monsterID[legs]" value="0"<?php if( $option_legs == 0 ) echo ' checked="checked"'; ?>> <?php _e( 'Black', 'wp-monsterid' ); ?>

				</label>
				<br>
				<label>
					<input type="radio" name="monsterID[legs]" value="1"<?php if( $option_legs == 1 ) echo ' checked="checked"'; ?>> <?php _e( 'White', 'wp-monsterid' ); ?>

				</label>
				<p class="description"><?php _e( 'Change legs and arms to white if on dark background (Default: Black)', 'wp-monsterid' ); ?></p>
				<p class="description"><?php
				
	printf(
		/* translators: %s: Path to a folder */
		__( 'Please make sure the folder %s is writeable before changing to white', 'wp-monsterid' ),
		'<code>wp-content/plugins/monsterid/parts/</code>'
	);
	
				?></p>
			</fieldset>
		<?php
		
	}
	
	function monsterid_settings_field_backgroundcolor( $args ){
		
		global $monsterid;
		
		$colors = array( 'backr' => __( 'Red', 'wp-monsterid' ), 'backg' => __( 'Green', 'wp-monsterid' ), 'backb' => __( 'Blue', 'wp-monsterid' ) );
		
		$color = $args[0];
		
		$option_background = $monsterid->get_option( $color );
		
		?>

					<fieldset>
						<legend class="screen-reader-text"><span><?php echo $colors[$color]; ?></span></legend>
						<span style="display: inline-block; min-width: 5em;"><?php echo $colors[$color]; ?>:</span>
						<input type="number" name="monsterID[<?php echo $color; ?>][0]" min="0" max="255" value="<?php echo $option_background[0]; ?>"> - <input type="number" name="monsterID[<?php echo $color; ?>][1]" min="0" max="255" value="<?php echo $option_background[1]; ?>">
					</fieldset>
		<?php
		
	}
	
	function monsterid_settings_field_background(){
		
		?>

			<ul>
				<li><?php
		
		monsterid_settings_field_backgroundcolor( array( 'backr' ) );
		echo '		</li><li>';
		monsterid_settings_field_backgroundcolor( array( 'backg' ) );
		echo '		</li><li>';
		monsterid_settings_field_backgroundcolor( array( 'backb' ) );
		
		?>
		</li>
			</ul>
			<p class="description"><?php _e( 'Enter single value or range (Default: 220-255, 220-255, 220-255)', 'wp-monsterid' ); ?></p>
			<p class="description"><?php _e( 'Enter 0-0, 0-0, 0-0 for transparent background (Please note that transparent background may turn grey in IE6)', 'wp-monsterid' ); ?></p>		
		<?php
		
	}
	
	function monsterid_settings_field_monstersize(){
		
		global $monsterid;
		$option_size = $monsterid->get_option( 'size' );
		
		?>

			<input type="text" name="monsterID[size]" value="<?php echo $option_size; ?>"/>
			<p class="description"><?php _e( 'Size is in Pixels (Default: 65)', 'wp-monsterid' ); ?></p>
		<?php
		 
	}
	
	function monsterid_clear_cache_field(){
		
		if( current_user_can( 'manage_options' ) ){
			
			echo '<p>
			' . __( 'Clear the MonsterID Image Cache:', 'wp-monsterid' ) . ' ' . get_submit_button( __( 'Clear Cache', 'wp-monsterid' ), 'secondary', 'monsterID[cache]', false ) . '
		</p>
';
		}
		
	}
	
	function monsterid_subpanel(){
		//get_settings_errors( 'wp_monsterid_page_load', false );
		global $monsterid;
		$monsterID_count = $monsterid->monster_count();
		$option_css = $monsterid->get_option( 'css' );
		
		?>

<div class="wrap">
	<h1><?php _e( 'This is the MonsterID options page', 'wp-monsterid' ); ?></h1>
	<p><?php
	/*
	printf(
		/ * translators: %d: Total number of MonsterID avatars saved * /
		__( 'You currently have %d monsters on your website.', 'wp-monsterid' ),
		$monsterID_count
	);
	*/
	printf(
		_n(
			'You currently have %d monster on your website.',
			'You currently have %d monsters on your website.',
			$monsterID_count,
			'wp-monsterid'
		),
		$monsterID_count
	);
	
	?></p>
	<form id="monsterid-settings" method="POST" action="options.php">
		<?php
		
		settings_fields( 'wp_monsterid_settings' );
		
		?>

		<?php
		
		do_settings_sections( 'wp_monsterid_settings' );
		
		?>

		<?php
		
		submit_button( __( 'Set Options', 'wp-monsterid' ) );
		
		?>

		<?php
		
		monsterid_clear_cache_field();
	
		?>
		<h2><?php _e( 'To use MonsterID', 'wp-monsterid' ); ?></h2>
		<p>Make sure sure the folder <code>wp-content/plugins/monsterid</code> is <a href="http://codex.wordpress.org/Changing_File_Permissions">writeable</a>. Monsters should automatically be added beside your commentors names after that. Enjoy.</p>
		<p>If you use the Recent Comments Widget in your sidebar, this plugin also provides a replacement Recent Comments (with MonsterIDs) Widget to add MonsterIDs to the sidebar comments (just set it in the Widgets Control Panel)</p>

		<h2><?php _e( 'Testing', 'wp-monsterid' ); ?></h2>
		<p>A test monster should be here:<?php echo monsterid_build_monster('This is a test','Test');?> and the source URL for this image is <a href="<?php echo monsterid_build_monster( 'This is a test', 'Test', false ); ?>">here</a>.</p>
		<p>If there is no monster above or there are any other problems, concerns or suggestions please let me know <a href="http://scott.sherrillmix.com/blog/blogger/wp_monsterid/">here</a>. Enjoy your monsters.</p>

		<h2><?php _e( 'For Advanced Users', 'wp-monsterid' ); ?></h2>
		<p>If you want more control of where MonsterID's appear change the Automatically Add option above and in your comments.php file add:
			<br><code><?php echo htmlspecialchars( '<?php if( function_exists( "monsterid_build_monster" ) ){ echo monsterid_build_monster( $comment->comment_author_email, $comment->comment_author ); } ?>' ); ?></code>
			<br>Or if you're more confident and just want the img URL use:
			<br><code><?php echo htmlspecialchars( '<?php if( function_exists( "monsterid_build_monster" ) ){ echo monsterid_build_monster( $comment->comment_author_email, $comment->comment_author, false ); } ?>' ); ?></code></p>
		<p>You can also add any custom CSS you would like here:</p>
		<textarea name="monsterID[css]" rows="5" cols="70"><?php
		
		echo $option_css;
		
?></textarea>
		<p class="submit"><?php
		
		submit_button( __( 'Adjust CSS', 'wp-monsterid' ), 'primary', 'submit', false );
		?>

		<?php
		submit_button( __( 'Reset CSS', 'wp-monsterid' ), 'primary', 'monsterID[cssreset]', false );
		
		/*printf(
			/ * translators: 1: Name and link (Andreas Gohr) 2: Name and link (Lemm) 3: Name and link (Don Park) * /
			__( 'The monster generation code and the original images are by %1$s, the updated artistic images came from %2$s and the underlying idea came from %3$s.', 'wp-monsterid' ),
			'<a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a>',
			'<a href="http://rocketworm.com/">Lemm</a>',
			'<a href="http://www.docuverse.com/blog/donpark/2007/01/18/visual-security-9-block-ip-identification">Don Park</a>'
		);*/

		?></p>
	</form>
</div>
		<?php
		
	}
	
	function monsterid_add_js(){
		
	?>
<script type="text/javascript">

	jQuery( function($) {
		var section = $('#monsterid-settings'), 
			artisticMonster = section.find('input:radio[name="monsterID[artistic]"][value="1"]'), /*value: 1=artistic; 0=original*/
			radios = section.find('input:radio[name="monsterID[greyscale]"]'), 
			check_disabled = function(){
				radios.prop( 'disabled', ! artisticMonster.prop('checked') );
			};
		check_disabled();
		section.find( 'input:radio' ).on( 'change', check_disabled );
	} );
	
</script>
	<?php
		
	}
	
	function monsterid_settings_sanitize( $input ){
		
		global $monsterid;
		
		//size
		if( ! ( $input['size'] > 0 & $input['size'] < 400 ) ){
			
			add_settings_error(
				'wp_monsterid_settings', 'monster-size',
				__( 'Please enter an integer for size. Preferably between 30-200.', 'wp-monsterid' )
				);
				
			$input['size'] = $monsterid->get_option( 'size' );
			
		}
		
		//background colours
		foreach( array( 'backr', 'backg', 'backb' ) as $color ){ //update background color options
		
			$colorarray = $input[$color];
			
			if( count( $colorarray ) == 1 ){
				$colorarray[1] = $colorarray[0];
			}
			
			$colorarray[0] = (int) $colorarray[0];
			$colorarray[1] = (int) $colorarray[1];
			
			if(! ( $colorarray[0] >= 0 & $colorarray[0] < 256 & $colorarray[1] >= 0 & $colorarray[1] < 256 ) ){
				
				add_settings_error(
					'wp_monsterid_settings', 'monster-background',
					__( 'Please enter a range between 1 and 255 for the background color (e.g. 230-255). For a single color please enter a single value (e.g. white = 255 for red, green and blue).', 'wp-monsterid' )
				);
				
				$input[$color] = $monsterid->get_option( $color );
				
			}
			
		}
		
		//arms/legs
		if( $input['legs'] == 1 ){
			
			if(! is_writable( WP_MONSTERPARTS_DIR ) ){
				
				add_settings_error(
					'wp_monsterid_settings', 'monster-legs',
					'Directory ' . WP_MONSTERPARTS_DIR . ' must be <a href="http://codex.wordpress.org/Changing_File_Permissions">writeable</a> to use white legs and arms.'
				);
				
				$input['legs'] = 0;
				
			}else{
				
				monsterid_create_whiteparts();
			}
			
		}
		
		
		//reset the css
		if( array_key_exists( 'cssreset', $input ) ){
			
			unset( $input['cssreset'] );
			$input['css'] = DEFAULT_MONSTERID_CSS;
			
			add_settings_error( 'wp_monsterid_settings', 'monster-css', __( 'CSS settings saved.', 'wp-monsterid' ), 'success' );
			
		}
		
		if( array_key_exists( 'cache', $input ) ){
			
			unset( $input['cache'] );
			//Clear the monsterID cache
			
		}
		
		return $input;
		
	}
	
	function monsterid_clear_cache(){
		
		$dir = WP_MONSTERID_DIR_INTERNAL;
		
		if( $dh = opendir($dir) ){
			
			while( ( $file = readdir( $dh ) ) !== false ){
				
				if( is_file( $dir . $file ) and preg_match ('/^.*\.png$/', $file ) ){
					unlink( $dir . $file );
				}
				
			}
			
			closedir( $dh );
			
			add_settings_error( 'wp_monsterid_settings', 'monster-cache', __( 'Cache cleared', 'wp-monsterid' ), 'success' );
			
		}
		
	}
	
	//Creates white arms and legs
	function monsterid_create_whiteparts(){
		
		//make sure white legs/arms exist
		$dir = WP_MONSTERPARTS_DIR;
		$changed = "";
		
		if( $dh = opendir( $dir ) ){
			
			while( ( $file = readdir( $dh ) ) !== false ){
				
				if( is_file( $dir . $file ) and preg_match( '/^(oldarms|oldlegs|oldbody|oldhair)_.*\.png$/', $file ) ){
					
					if( ! file_exists( $dir . 'w' . $file ) ){
						
						$original = imagecreatefrompng( $dir . $file );
						
						$x = imagesx( $original );
						$y = imagesy( $original );
						
						$white = imageColorAllocate( $original, 230, 230, 230 );
						
						for( $i = 0; $i < $y; $i++ ){
							for( $j = 0; $j < $x; $j++ ){
								
								$pos = imagecolorat( $original, $j, $i);
								if( $pos == 0 ) imagesetpixel( $original, $j, $i, $white );
								
							}
						}
						
						imagesavealpha( $original, true );
						imagepng( $original, $dir . 'w' . $file );
						$changed .= 'w' . $file . ' ';
						
					}
				}
			}
			
			closedir( $dh );
			
			if( $changed ){
				add_settings_error( 'wp_monsterid_settings', 'monster-whiteparts',
					sprintf(
						/* translators: %s: List of image files created */
						__( 'White part files generated: %s created.', 'wp-monsterid' ),
						$changed
					), 'info' );
			}
			
		}
		
	}
	
	function monsterid_avatar_defaults( $defaults ){
		
		//Add settings link to monsterid
		$url = esc_url( add_query_arg( 'page', WP_MONSTERID_MENU_SLUG, get_admin_url() . 'options-general.php' ) );
		$settings = sprintf( '<a href="%s" class="">%s</a>', $url, __( 'Settings', 'wp-monsterid' ) );
		
		$text = __( 'MonsterID (Generated)', 'wp-monsterid' ) . ' - ' . $settings;
		
		$defaults['monsterid'] = $text;
		
		return $defaults;
		
	}
	
	add_action( 'admin_footer_text', 'monsterid_footer_text' );
	
	function monsterid_footer_text( $text ){
		
		if( get_current_screen()->id == 'settings_page_wp_monsterid' )
			$text = 	sprintf(
				/* translators: 1: Name and link (Andreas Gohr) 2: Name and link (Lemm) 3: Name and link (Don Park) */
				__( 'The monster generation code and the original images are by %1$s, the updated artistic images came from %2$s and the underlying idea came from %3$s.', 'wp-monsterid' ),
				'<a href="http://www.splitbrain.org/projects/monsterid">Andreas Gohr</a>',
				'<a href="http://rocketworm.com/">Lemm</a>',
				'<a href="http://www.docuverse.com/blog/donpark/2007/01/18/visual-security-9-block-ip-identification">Don Park</a>'
			);

		
		return $text;
		
	}
	
?>