<?php 
/**
 * SMOF Options Machine Class
 *
 * @package     WordPress
 * @subpackage  SMOF
 * @since       1.0.0
 * @author      Syamil MJ
 */

class Options_Machine {

	/**
	 * PHP5 contructor
	 *
	 * @since 1.0.0
	 */
	function __construct($options) {
		
		$return = $this->optionsframework_machine($options);
		
		$this->Inputs = $return[0];
		$this->Menu = $return[1];
		$this->Defaults = $return[2];
		
	}


	/**
	 * Process options data and build option fields
	 *
	 * @uses get_option()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function optionsframework_machine($options) {
	
	    $data = get_option(OPTIONS);
		
		$defaults = array();   
	    $counter = 0;
		$menu = '';
		$output = '';
		
		foreach ($options as $value) {
		
			$counter++;
			$val = '';
			
			//create array of defaults		
			if ($value['type'] == 'multicheck'){
				if (is_array($value['std'])){
					foreach($value['std'] as $i=>$key){
						$defaults[$value['id']][$key] = true;
					}
				} else {
						$defaults[$value['id']][$value['std']] = true;
				}
			} else {
				if (isset($value['id'])) $defaults[$value['id']] = $value['std'];
			}
			
			//Start Heading
			 if ( $value['type'] != "heading" )
			 {
			 	$class = ''; if(isset( $value['class'] )) { $class = $value['class']; }
				
				//hide items in checkbox group
				$fold='';
				if (array_key_exists("fold",$value)) {
					if ($data[$value['fold']]) {
						$fold="f_".$value['fold']." ";
					} else {
						$fold="f_".$value['fold']." temphide ";
					}
				}
	
				$output .= '<div id="section-'.$value['id'].'" class="'.$fold.'section section-'.$value['type'].' '. $class .'">'."\n";
				
				//only show header if 'name' value exists
				if($value['name']) $output .= '<h3 class="heading">'. $value['name'] .'</h3>'."\n";
				
				$output .= '<div class="option">'."\n" . '<div class="controls">'."\n";
	
			 } 
			 //End Heading
			
			//switch statement to handle various options type                              
			switch ( $value['type'] ) {
			
				//text input
				case 'text':
					$t_value = '';
					$t_value = stripslashes($data[$value['id']]);
					
					$mini ='';
					if(!isset($value['mod'])) $value['mod'] = '';
					if($value['mod'] == 'mini') { $mini = 'mini';}
					
					$output .= '<input class="of-input '.$mini.'" name="'.$value['id'].'" id="'. $value['id'] .'" type="'. $value['type'] .'" value="'. $t_value .'" />';
				break;
				
				//select option
				case 'select':
					$mini ='';
					if(!isset($value['mod'])) $value['mod'] = '';
					if($value['mod'] == 'mini') { $mini = 'mini';}
					$output .= '<div class="select_wrapper ' . $mini . '">';
					$output .= '<select class="select of-input" name="'.$value['id'].'" id="'. $value['id'] .'">';
					foreach ($value['options'] as $select_ID => $option) {			
						$output .= '<option id="' . $select_ID . '" value="'.$option.'" ' . selected($data[$value['id']], $option, false) . ' />'.$option.'</option>';	 
					 } 
					$output .= '</select></div>';
				break;
				
				//textarea option
				case 'textarea':	
					$cols = '8';
					$ta_value = '';
					
					if(isset($value['options'])){
							$ta_options = $value['options'];
							if(isset($ta_options['cols'])){
							$cols = $ta_options['cols'];
							} 
						}
						
						$ta_value = stripslashes($data[$value['id']]);			
						$output .= '<textarea class="of-input" name="'.$value['id'].'" id="'. $value['id'] .'" cols="'. $cols .'" rows="8">'.$ta_value.'</textarea>';		
				break;
				
				//radiobox option
				case "radio":
					
					 foreach($value['options'] as $option=>$name) {
						$output .= '<input class="of-input of-radio" name="'.$value['id'].'" type="radio" value="'.$option.'" ' . checked($data[$value['id']], $option, false) . ' /><label class="radio">'.$name.'</label><br/>';				
					}
				break;
				
				//checkbox option
				case 'checkbox':
					if (!isset($data[$value['id']])) {
						$data[$value['id']] = 0;
					}
					
					$fold = '';
					if (array_key_exists("folds",$value)) $fold="fld ";
		
					$output .= '<input type="hidden" class="'.$fold.'checkbox aq-input" name="'.$value['id'].'" id="'. $value['id'] .'" value="0"/>';
					$output .= '<input type="checkbox" class="'.$fold.'checkbox of-input" name="'.$value['id'].'" id="'. $value['id'] .'" value="1" '. checked($data[$value['id']], 1, false) .' />';
				break;
				
				//multiple checkbox option
				case 'multicheck': 			
					//$multi_stored = $data[$value['id']];
					(isset($data[$value['id']]))? $multi_stored = $data[$value['id']] : $multi_stored="";
								
					foreach ($value['options'] as $key => $option) {
						if (!isset($multi_stored[$key])) {$multi_stored[$key] = '';}
						$of_key_string = $value['id'] . '_' . $key;
						$output .= '<input type="checkbox" class="checkbox of-input" name="'.$value['id'].'['.$key.']'.'" id="'. $of_key_string .'" value="1" '. checked($multi_stored[$key], 1, false) .' /><label class="multicheck" for="'. $of_key_string .'">'. $option .'</label><br />';								
					}			 
				break;
				
				//ajax image upload option
				case 'upload':
					if(!isset($value['mod'])) $value['mod'] = '';
					$output .= Options_Machine::optionsframework_uploader_function($value['id'],$value['std'],$value['mod']);			
				break;
				
				// native media library uploader - @uses optionsframework_media_uploader_function()
				case 'media':
					$_id = strip_tags( strtolower($value['id']) );
					$int = '';
					$int = optionsframework_mlu_get_silentpost( $_id );
					if(!isset($value['mod'])) $value['mod'] = '';
					$output .= Options_Machine::optionsframework_media_uploader_function( $value['id'], $value['std'], $int, $value['mod'] ); // New AJAX Uploader using Media Library			
				break;
				
				//colorpicker option
				case 'color':		
					$output .= '<div id="' . $value['id'] . '_picker" class="colorSelector"><div style="background-color: '.$data[$value['id']].'"></div></div>';
					$output .= '<input class="of-color" name="'.$value['id'].'" id="'. $value['id'] .'" type="text" value="'. $data[$value['id']] .'" />';
				break;
				
				//typography option	
				case 'typography':
				
					$typography_stored = isset($data[$value['id']]) ? $data[$value['id']] : $value['std'];
					
					/* Font Size */
					
					if(isset($typography_stored['size'])) {
						$output .= '<div class="select_wrapper typography-size" original-title="Font size">';
						$output .= '<select class="of-typography of-typography-size select" name="'.$value['id'].'[size]" id="'. $value['id'].'_size">';
							for ($i = 9; $i < 47; $i++){ 
								$test = $i.'px';
								$output .= '<option value="'. $i .'px" ' . selected($typography_stored['size'], $test, false) . '>'. $i .'px</option>'; 
								}
				
						$output .= '</select></div>';
					
					}
					
					/* Line Height */
					if(isset($typography_stored['height'])) {
					
						$output .= '<div class="select_wrapper typography-height" original-title="Line height">';
						$output .= '<select class="of-typography of-typography-height select" name="'.$value['id'].'[height]" id="'. $value['id'].'_height">';
							for ($i = 20; $i < 38; $i++){ 
								$test = $i.'px';
								$output .= '<option value="'. $i .'px" ' . selected($typography_stored['height'], $test, false) . '>'. $i .'px</option>'; 
								}
				
						$output .= '</select></div>';
					
					}
						
					/* Font Face */
					if(isset($typography_stored['face'])) {
					
						$output .= '<div class="select_wrapper typography-face" original-title="Font family">';
						$output .= '<select class="of-typography of-typography-face select" name="'.$value['id'].'[face]" id="'. $value['id'].'_face">';
						
						$faces = array('arial'=>'Arial',
										'verdana'=>'Verdana, Geneva',
										'Trebuchet MS'=>'Trebuchet MS',
										'georgia' =>'Georgia',
										'times'=>'Times New Roman',
										'tahoma'=>'Tahoma, Geneva',
										'helvetica'=>'Helvetica',
										'ABeeZee' => 'ABeeZee',
										'Abel' => 'Abel',
										'Abril Fatface' => 'Abril Fatface',
										'Aclonica' => 'Aclonica',
										'Acme' => 'Acme',
										'Actor' => 'Actor',
										'Adamina' => 'Adamina',
										'Advent Pro' => 'Advent Pro',
										'Aguafina Script' => 'Aguafina Script',
										'Aladin' => 'Aladin',
										'Aldrich' => 'Aldrich',
										'Alegreya' => 'Alegreya',
										'Alegreya SC' => 'Alegreya SC',
										'Alex Brush' => 'Alex Brush',
										'Alfa Slab One' => 'Alfa Slab One',
										'Alice' => 'Alice',
										'Alike' => 'Alike',
										'Alike Angular' => 'Alike Angular',
										'Allan' => 'Allan',
										'Allerta' => 'Allerta',
										'Allerta Stencil' => 'Allerta Stencil',
										'Allura' => 'Allura',
										'Almendra' => 'Almendra',
										'Almendra SC' => 'Almendra SC',
										'Amaranth' => 'Amaranth',
										'Amatic SC' => 'Amatic SC',
										'Amethysta' => 'Amethysta',
										'Andada' => 'Andada',
										'Andika' => 'Andika',
										'Angkor' => 'Angkor',
										'Annie Use Your Telescope' => 'Annie Use Your Telescope',
										'Anonymous Pro' => 'Anonymous Pro',
										'Antic' => 'Antic',
										'Antic Didone' => 'Antic Didone',
										'Antic Slab' => 'Antic Slab',
										'Anton' => 'Anton',
										'Arapey' => 'Arapey',
										'Arbutus' => 'Arbutus',
										'Architects Daughter' => 'Architects Daughter',
										'Arimo' => 'Arimo',
										'Arizonia' => 'Arizonia',
										'Armata' => 'Armata',
										'Artifika' => 'Artifika',
										'Arvo' => 'Arvo',
										'Asap' => 'Asap',
										'Asset' => 'Asset',
										'Astloch' => 'Astloch',
										'Asul' => 'Asul',
										'Atomic Age' => 'Atomic Age',
										'Aubrey' => 'Aubrey',
										'Audiowide' => 'Audiowide',
										'Average' => 'Average',
										'Averia Gruesa Libre' => 'Averia Gruesa Libre',
										'Averia Libre' => 'Averia Libre',
										'Averia Sans Libre' => 'Averia Sans Libre',
										'Averia Serif Libre' => 'Averia Serif Libre',
										'Bad Script' => 'Bad Script',
										'Balthazar' => 'Balthazar',
										'Bangers' => 'Bangers',
										'Basic' => 'Basic',
										'Battambang' => 'Battambang',
										'Baumans' => 'Baumans',
										'Bayon' => 'Bayon',
										'Belgrano' => 'Belgrano',
										'Belleza' => 'Belleza',
										'Bentham' => 'Bentham',
										'Berkshire Swash' => 'Berkshire Swash',
										'Bevan' => 'Bevan',
										'Bigshot One' => 'Bigshot One',
										'Bilbo' => 'Bilbo',
										'Bilbo Swash Caps' => 'Bilbo Swash Caps',
										'Bitter' => 'Bitter',
										'Black Ops One' => 'Black Ops One',
										'Bokor' => 'Bokor',
										'Bonbon' => 'Bonbon',
										'Boogaloo' => 'Boogaloo',
										'Bowlby One' => 'Bowlby One',
										'Bowlby One SC' => 'Bowlby One SC',
										'Brawler' => 'Brawler',
										'Bree Serif' => 'Bree Serif',
										'Bubblegum Sans' => 'Bubblegum Sans',
										'Buda' => 'Buda',
										'Buenard' => 'Buenard',
										'Butcherman' => 'Butcherman',
										'Butterfly Kids' => 'Butterfly Kids',
										'Cabin' => 'Cabin',
										'Cabin Condensed' => 'Cabin Condensed',
										'Cabin Sketch' => 'Cabin Sketch',
										'Caesar Dressing' => 'Caesar Dressing',
										'Cagliostro' => 'Cagliostro',
										'Calligraffitti' => 'Calligraffitti',
										'Cambo' => 'Cambo',
										'Candal' => 'Candal',
										'Cantarell' => 'Cantarell',
										'Cantata One' => 'Cantata One',
										'Cardo' => 'Cardo',
										'Carme' => 'Carme',
										'Carter One' => 'Carter One',
										'Caudex' => 'Caudex',
										'Cedarville Cursive' => 'Cedarville Cursive',
										'Ceviche One' => 'Ceviche One',
										'Changa One' => 'Changa One',
										'Chango' => 'Chango',
										'Chau Philomene One' => 'Chau Philomene One',
										'Chelsea Market' => 'Chelsea Market',
										'Chenla' => 'Chenla',
										'Cherry Cream Soda' => 'Cherry Cream Soda',
										'Chewy' => 'Chewy',
										'Chicle' => 'Chicle',
										'Chivo' => 'Chivo',
										'Coda' => 'Coda',
										'Coda Caption' => 'Coda Caption',
										'Codystar' => 'Codystar',
										'Comfortaa' => 'Comfortaa',
										'Coming Soon' => 'Coming Soon',
										'Concert One' => 'Concert One',
										'Condiment' => 'Condiment',
										'Content' => 'Content',
										'Contrail One' => 'Contrail One',
										'Convergence' => 'Convergence',
										'Cookie' => 'Cookie',
										'Copse' => 'Copse',
										'Corben' => 'Corben',
										'Cousine' => 'Cousine',
										'Coustard' => 'Coustard',
										'Covered By Your Grace' => 'Covered By Your Grace',
										'Crafty Girls' => 'Crafty Girls',
										'Creepster' => 'Creepster',
										'Crete Round' => 'Crete Round',
										'Crimson Text' => 'Crimson Text',
										'Crushed' => 'Crushed',
										'Cuprum' => 'Cuprum',
										'Cutive' => 'Cutive',
										'Damion' => 'Damion',
										'Dancing Script' => 'Dancing Script',
										'Dangrek' => 'Dangrek',
										'Dawning of a New Day' => 'Dawning of a New Day',
										'Days One' => 'Days One',
										'Delius' => 'Delius',
										'Delius Swash Caps' => 'Delius Swash Caps',
										'Delius Unicase' => 'Delius Unicase',
										'Della Respira' => 'Della Respira',
										'Devonshire' => 'Devonshire',
										'Didact Gothic' => 'Didact Gothic',
										'Diplomata' => 'Diplomata',
										'Diplomata SC' => 'Diplomata SC',
										'Doppio One' => 'Doppio One',
										'Dorsa' => 'Dorsa',
										'Dosis' => 'Dosis',
										'Dr Sugiyama' => 'Dr Sugiyama',
										'Droid Sans' => 'Droid Sans',
										'Droid Sans Mono' => 'Droid Sans Mono',
										'Droid Serif' => 'Droid Serif',
										'Duru Sans' => 'Duru Sans',
										'Dynalight' => 'Dynalight',
										'EB Garamond' => 'EB Garamond',
										'Eater' => 'Eater',
										'Economica' => 'Economica',
										'Electrolize' => 'Electrolize',
										'Emblema One' => 'Emblema One',
										'Emilys Candy' => 'Emilys Candy',
										'Engagement' => 'Engagement',
										'Enriqueta' => 'Enriqueta',
										'Erica One' => 'Erica One',
										'Esteban' => 'Esteban',
										'Euphoria Script' => 'Euphoria Script',
										'Ewert' => 'Ewert',
										'Exo' => 'Exo',
										'Expletus Sans' => 'Expletus Sans',
										'Fanwood Text' => 'Fanwood Text',
										'Fascinate' => 'Fascinate',
										'Fascinate Inline' => 'Fascinate Inline',
										'Federant' => 'Federant',
										'Federo' => 'Federo',
										'Felipa' => 'Felipa',
										'Fjord One' => 'Fjord One',
										'Flamenco' => 'Flamenco',
										'Flavors' => 'Flavors',
										'Fondamento' => 'Fondamento',
										'Fontdiner Swanky' => 'Fontdiner Swanky',
										'Forum' => 'Forum',
										'Francois One' => 'Francois One',
										'Fredericka the Great' => 'Fredericka the Great',
										'Fredoka One' => 'Fredoka One',
										'Freehand' => 'Freehand',
										'Fresca' => 'Fresca',
										'Frijole' => 'Frijole',
										'Fugaz One' => 'Fugaz One',
										'GFS Didot' => 'GFS Didot',
										'GFS Neohellenic' => 'GFS Neohellenic',
										'Galdeano' => 'Galdeano',
										'Gentium Basic' => 'Gentium Basic',
										'Gentium Book Basic' => 'Gentium Book Basic',
										'Geo' => 'Geo',
										'Geostar' => 'Geostar',
										'Geostar Fill' => 'Geostar Fill',
										'Germania One' => 'Germania One',
										'Give You Glory' => 'Give You Glory',
										'Glass Antiqua' => 'Glass Antiqua',
										'Glegoo' => 'Glegoo',
										'Gloria Hallelujah' => 'Gloria Hallelujah',
										'Goblin One' => 'Goblin One',
										'Gochi Hand' => 'Gochi Hand',
										'Gorditas' => 'Gorditas',
										'Goudy Bookletter 1911' => 'Goudy Bookletter 1911',
										'Graduate' => 'Graduate',
										'Gravitas One' => 'Gravitas One',
										'Great Vibes' => 'Great Vibes',
										'Gruppo' => 'Gruppo',
										'Gudea' => 'Gudea',
										'Habibi' => 'Habibi',
										'Hammersmith One' => 'Hammersmith One',
										'Handlee' => 'Handlee',
										'Hanuman' => 'Hanuman',
										'Happy Monkey' => 'Happy Monkey',
										'Henny Penny' => 'Henny Penny',
										'Herr Von Muellerhoff' => 'Herr Von Muellerhoff',
										'Holtwood One SC' => 'Holtwood One SC',
										'Homemade Apple' => 'Homemade Apple',
										'Homenaje' => 'Homenaje',
										'IM Fell DW Pica' => 'IM Fell DW Pica',
										'IM Fell DW Pica SC' => 'IM Fell DW Pica SC',
										'IM Fell Double Pica' => 'IM Fell Double Pica',
										'IM Fell Double Pica SC' => 'IM Fell Double Pica SC',
										'IM Fell English' => 'IM Fell English',
										'IM Fell English SC' => 'IM Fell English SC',
										'IM Fell French Canon' => 'IM Fell French Canon',
										'IM Fell French Canon SC' => 'IM Fell French Canon SC',
										'IM Fell Great Primer' => 'IM Fell Great Primer',
										'IM Fell Great Primer SC' => 'IM Fell Great Primer SC',
										'Iceberg' => 'Iceberg',
										'Iceland' => 'Iceland',
										'Imprima' => 'Imprima',
										'Inconsolata' => 'Inconsolata',
										'Inder' => 'Inder',
										'Indie Flower' => 'Indie Flower',
										'Inika' => 'Inika',
										'Irish Grover' => 'Irish Grover',
										'Istok Web' => 'Istok Web',
										'Italiana' => 'Italiana',
										'Italianno' => 'Italianno',
										'Jim Nightshade' => 'Jim Nightshade',
										'Jockey One' => 'Jockey One',
										'Jolly Lodger' => 'Jolly Lodger',
										'Josefin Sans' => 'Josefin Sans',
										'Josefin Slab' => 'Josefin Slab',
										'Judson' => 'Judson',
										'Julee' => 'Julee',
										'Junge' => 'Junge',
										'Jura' => 'Jura',
										'Just Another Hand' => 'Just Another Hand',
										'Just Me Again Down Here' => 'Just Me Again Down Here',
										'Kameron' => 'Kameron',
										'Karla' => 'Karla',
										'Kaushan Script' => 'Kaushan Script',
										'Kelly Slab' => 'Kelly Slab',
										'Kenia' => 'Kenia',
										'Khmer' => 'Khmer',
										'Knewave' => 'Knewave',
										'Kotta One' => 'Kotta One',
										'Koulen' => 'Koulen',
										'Kranky' => 'Kranky',
										'Kreon' => 'Kreon',
										'Kristi' => 'Kristi',
										'Krona One' => 'Krona One',
										'La Belle Aurore' => 'La Belle Aurore',
										'Lancelot' => 'Lancelot',
										'Lato' => 'Lato',
										'League Script' => 'League Script',
										'Leckerli One' => 'Leckerli One',
										'Ledger' => 'Ledger',
										'Lekton' => 'Lekton',
										'Lemon' => 'Lemon',
										'Lilita One' => 'Lilita One',
										'Limelight' => 'Limelight',
										'Linden Hill' => 'Linden Hill',
										'Lobster' => 'Lobster',
										'Lobster Two' => 'Lobster Two',
										'Londrina Outline' => 'Londrina Outline',
										'Londrina Shadow' => 'Londrina Shadow',
										'Londrina Sketch' => 'Londrina Sketch',
										'Londrina Solid' => 'Londrina Solid',
										'Lora' => 'Lora',
										'Love Ya Like A Sister' => 'Love Ya Like A Sister',
										'Loved by the King' => 'Loved by the King',
										'Lovers Quarrel' => 'Lovers Quarrel',
										'Luckiest Guy' => 'Luckiest Guy',
										'Lusitana' => 'Lusitana',
										'Lustria' => 'Lustria',
										'Macondo' => 'Macondo',
										'Macondo Swash Caps' => 'Macondo Swash Caps',
										'Magra' => 'Magra',
										'Maiden Orange' => 'Maiden Orange',
										'Mako' => 'Mako',
										'Marck Script' => 'Marck Script',
										'Marko One' => 'Marko One',
										'Marmelad' => 'Marmelad',
										'Marvel' => 'Marvel',
										'Mate' => 'Mate',
										'Mate SC' => 'Mate SC',
										'Maven Pro' => 'Maven Pro',
										'Meddon' => 'Meddon',
										'MedievalSharp' => 'MedievalSharp',
										'Medula One' => 'Medula One',
										'Megrim' => 'Megrim',
										'Merienda One' => 'Merienda One',
										'Merriweather' => 'Merriweather',
										'Metal' => 'Metal',
										'Metamorphous' => 'Metamorphous',
										'Metrophobic' => 'Metrophobic',
										'Michroma' => 'Michroma',
										'Miltonian' => 'Miltonian',
										'Miltonian Tattoo' => 'Miltonian Tattoo',
										'Miniver' => 'Miniver',
										'Miss Fajardose' => 'Miss Fajardose',
										'Modern Antiqua' => 'Modern Antiqua',
										'Molengo' => 'Molengo',
										'Monofett' => 'Monofett',
										'Monoton' => 'Monoton',
										'Monsieur La Doulaise' => 'Monsieur La Doulaise',
										'Montaga' => 'Montaga',
										'Montez' => 'Montez',
										'Montserrat' => 'Montserrat',
										'Moul' => 'Moul',
										'Moulpali' => 'Moulpali',
										'Mountains of Christmas' => 'Mountains of Christmas',
										'Mr Bedfort' => 'Mr Bedfort',
										'Mr Dafoe' => 'Mr Dafoe',
										'Mr De Haviland' => 'Mr De Haviland',
										'Mrs Saint Delafield' => 'Mrs Saint Delafield',
										'Mrs Sheppards' => 'Mrs Sheppards',
										'Muli' => 'Muli',
										'Mystery Quest' => 'Mystery Quest',
										'Neucha' => 'Neucha',
										'Neuton' => 'Neuton',
										'News Cycle' => 'News Cycle',
										'Niconne' => 'Niconne',
										'Nixie One' => 'Nixie One',
										'Nobile' => 'Nobile',
										'Nokora' => 'Nokora',
										'Norican' => 'Norican',
										'Nosifer' => 'Nosifer',
										'Nothing You Could Do' => 'Nothing You Could Do',
										'Noticia Text' => 'Noticia Text',
										'Nova Cut' => 'Nova Cut',
										'Nova Flat' => 'Nova Flat',
										'Nova Mono' => 'Nova Mono',
										'Nova Oval' => 'Nova Oval',
										'Nova Round' => 'Nova Round',
										'Nova Script' => 'Nova Script',
										'Nova Slim' => 'Nova Slim',
										'Nova Square' => 'Nova Square',
										'Numans' => 'Numans',
										'Nunito' => 'Nunito',
										'Odor Mean Chey' => 'Odor Mean Chey',
										'Old Standard TT' => 'Old Standard TT',
										'Oldenburg' => 'Oldenburg',
										'Oleo Script' => 'Oleo Script',
										'Open Sans' => 'Open Sans',
										'Open Sans Condensed' => 'Open Sans Condensed',
										'Orbitron' => 'Orbitron',
										'Original Surfer' => 'Original Surfer',
										'Oswald' => 'Oswald',
										'Over the Rainbow' => 'Over the Rainbow',
										'Overlock' => 'Overlock',
										'Overlock SC' => 'Overlock SC',
										'Ovo' => 'Ovo',
										'Oxygen' => 'Oxygen',
										'PT Mono' => 'PT Mono',
										'PT Sans' => 'PT Sans',
										'PT Sans Caption' => 'PT Sans Caption',
										'PT Sans Narrow' => 'PT Sans Narrow',
										'PT Serif' => 'PT Serif',
										'PT Serif Caption' => 'PT Serif Caption',
										'Pacifico' => 'Pacifico',
										'Parisienne' => 'Parisienne',
										'Passero One' => 'Passero One',
										'Passion One' => 'Passion One',
										'Patrick Hand' => 'Patrick Hand',
										'Patua One' => 'Patua One',
										'Paytone One' => 'Paytone One',
										'Permanent Marker' => 'Permanent Marker',
										'Petrona' => 'Petrona',
										'Philosopher' => 'Philosopher',
										'Piedra' => 'Piedra',
										'Pinyon Script' => 'Pinyon Script',
										'Plaster' => 'Plaster',
										'Play' => 'Play',
										'Playball' => 'Playball',
										'Playfair Display' => 'Playfair Display',
										'Podkova' => 'Podkova',
										'Poiret One' => 'Poiret One',
										'Poller One' => 'Poller One',
										'Poly' => 'Poly',
										'Pompiere' => 'Pompiere',
										'Pontano Sans' => 'Pontano Sans',
										'Port Lligat Sans' => 'Port Lligat Sans',
										'Port Lligat Slab' => 'Port Lligat Slab',
										'Prata' => 'Prata',
										'Preahvihear' => 'Preahvihear',
										'Press Start 2P' => 'Press Start 2P',
										'Princess Sofia' => 'Princess Sofia',
										'Prociono' => 'Prociono',
										'Prosto One' => 'Prosto One',
										'Puritan' => 'Puritan',
										'Quantico' => 'Quantico',
										'Quattrocento' => 'Quattrocento',
										'Quattrocento Sans' => 'Quattrocento Sans',
										'Questrial' => 'Questrial',
										'Quicksand' => 'Quicksand',
										'Qwigley' => 'Qwigley',
										'Radley' => 'Radley',
										'Raleway' => 'Raleway',
										'Rammetto One' => 'Rammetto One',
										'Rancho' => 'Rancho',
										'Rationale' => 'Rationale',
										'Redressed' => 'Redressed',
										'Reenie Beanie' => 'Reenie Beanie',
										'Revalia' => 'Revalia',
										'Ribeye' => 'Ribeye',
										'Ribeye Marrow' => 'Ribeye Marrow',
										'Righteous' => 'Righteous',
										'Roboto' => 'Roboto',
										'Roboto Condensed' => 'Roboto Condensed',
										'Rochester' => 'Rochester',
										'Rock Salt' => 'Rock Salt',
										'Rokkitt' => 'Rokkitt',
										'Ropa Sans' => 'Ropa Sans',
										'Rosario' => 'Rosario',
										'Rosarivo' => 'Rosarivo',
										'Rouge Script' => 'Rouge Script',
										'Ruda' => 'Ruda',
										'Ruge Boogie' => 'Ruge Boogie',
										'Ruluko' => 'Ruluko',
										'Ruslan Display' => 'Ruslan Display',
										'Russo One' => 'Russo One',
										'Ruthie' => 'Ruthie',
										'Sail' => 'Sail',
										'Salsa' => 'Salsa',
										'Sanchez' => 'Sanchez',
										'Sancreek' => 'Sancreek',
										'Sansita One' => 'Sansita One',
										'Sarina' => 'Sarina',
										'Satisfy' => 'Satisfy',
										'Schoolbell' => 'Schoolbell',
										'Seaweed Script' => 'Seaweed Script',
										'Sevillana' => 'Sevillana',
										'Shadows Into Light' => 'Shadows Into Light',
										'Shadows Into Light Two' => 'Shadows Into Light Two',
										'Shanti' => 'Shanti',
										'Share' => 'Share',
										'Shojumaru' => 'Shojumaru',
										'Short Stack' => 'Short Stack',
										'Siemreap' => 'Siemreap',
										'Sigmar One' => 'Sigmar One',
										'Signika' => 'Signika',
										'Signika Negative' => 'Signika Negative',
										'Simonetta' => 'Simonetta',
										'Sirin Stencil' => 'Sirin Stencil',
										'Six Caps' => 'Six Caps',
										'Slackey' => 'Slackey',
										'Smokum' => 'Smokum',
										'Smythe' => 'Smythe',
										'Sniglet' => 'Sniglet',
										'Snippet' => 'Snippet',
										'Sofia' => 'Sofia',
										'Sonsie One' => 'Sonsie One',
										'Sorts Mill Goudy' => 'Sorts Mill Goudy',
										'Special Elite' => 'Special Elite',
										'Spicy Rice' => 'Spicy Rice',
										'Spinnaker' => 'Spinnaker',
										'Spirax' => 'Spirax',
										'Squada One' => 'Squada One',
										'Stardos Stencil' => 'Stardos Stencil',
										'Stint Ultra Condensed' => 'Stint Ultra Condensed',
										'Stint Ultra Expanded' => 'Stint Ultra Expanded',
										'Stoke' => 'Stoke',
										'Sue Ellen Francisco' => 'Sue Ellen Francisco',
										'Sunshiney' => 'Sunshiney',
										'Supermercado One' => 'Supermercado One',
										'Suwannaphum' => 'Suwannaphum',
										'Swanky and Moo Moo' => 'Swanky and Moo Moo',
										'Syncopate' => 'Syncopate',
										'Tangerine' => 'Tangerine',
										'Taprom' => 'Taprom',
										'Telex' => 'Telex',
										'Tenor Sans' => 'Tenor Sans',
										'The Girl Next Door' => 'The Girl Next Door',
										'Tienne' => 'Tienne',
										'Tinos' => 'Tinos',
										'Titan One' => 'Titan One',
										'Trade Winds' => 'Trade Winds',
										'Trocchi' => 'Trocchi',
										'Trochut' => 'Trochut',
										'Trykker' => 'Trykker',
										'Tulpen One' => 'Tulpen One',
										'Ubuntu' => 'Ubuntu',
										'Ubuntu Condensed' => 'Ubuntu Condensed',
										'Ubuntu Mono' => 'Ubuntu Mono',
										'Ultra' => 'Ultra',
										'Uncial Antiqua' => 'Uncial Antiqua',
										'UnifrakturCook' => 'UnifrakturCook',
										'UnifrakturMaguntia' => 'UnifrakturMaguntia',
										'Unkempt' => 'Unkempt',
										'Unlock' => 'Unlock',
										'Unna' => 'Unna',
										'VT323' => 'VT323',
										'Varela' => 'Varela',
										'Varela Round' => 'Varela Round',
										'Vast Shadow' => 'Vast Shadow',
										'Vibur' => 'Vibur',
										'Vidaloka' => 'Vidaloka',
										'Viga' => 'Viga',
										'Voces' => 'Voces',
										'Volkhov' => 'Volkhov',
										'Vollkorn' => 'Vollkorn',
										'Voltaire' => 'Voltaire',
										'Waiting for the Sunrise' => 'Waiting for the Sunrise',
										'Wallpoet' => 'Wallpoet',
										'Walter Turncoat' => 'Walter Turncoat',
										'Wellfleet' => 'Wellfleet',
										'Wire One' => 'Wire One',
										'Yanone Kaffeesatz' => 'Yanone Kaffeesatz',
										'Yellowtail' => 'Yellowtail',
										'Yeseva One' => 'Yeseva One',
										'Yesteryear' => 'Yesteryear',
										'Zeyada' => 'Zeyada'
									);		
										
						foreach ($faces as $i=>$face) {
							$output .= '<option value="'. $i .'" ' . selected($typography_stored['face'], $i, false) . '>'. $face .'</option>';
						}			
										
						$output .= '</select></div>';
					
					}
					
					/* Font Weight */
					if(isset($typography_stored['style'])) {
					
						$output .= '<div class="select_wrapper typography-style" original-title="Font style">';
						$output .= '<select class="of-typography of-typography-style select" name="'.$value['id'].'[style]" id="'. $value['id'].'_style">';
						$styles = array('normal'=>'Normal',
										'bold'=>'Bold');
										
						foreach ($styles as $i=>$style){
						
							$output .= '<option value="'. $i .'" ' . selected($typography_stored['style'], $i, false) . '>'. $style .'</option>';		
						}
						$output .= '</select></div>';
					
					}
					
					/* Font Color */
					if(isset($typography_stored['color'])) {
					
						$output .= '<div id="' . $value['id'] . '_color_picker" class="colorSelector typography-color"><div style="background-color: '.$typography_stored['color'].'"></div></div>';
						$output .= '<input class="of-color of-typography of-typography-color" original-title="Font color" name="'.$value['id'].'[color]" id="'. $value['id'] .'_color" type="text" value="'. $typography_stored['color'] .'" />';
					
					}
					
				break;
				
				//border option
				case 'border':
						
					/* Border Width */
					$border_stored = $data[$value['id']];
					
					$output .= '<div class="select_wrapper border-width">';
					$output .= '<select class="of-border of-border-width select" name="'.$value['id'].'[width]" id="'. $value['id'].'_width">';
						for ($i = 1; $i < 21; $i++){ 
						$output .= '<option value="'. $i .'" ' . selected($border_stored['width'], $i, false) . '>'. $i .'</option>';				 }
					$output .= '</select></div>';
					
					/* Border Style */
					$output .= '<div class="select_wrapper border-style">';
					$output .= '<select class="of-border of-border-style select" name="'.$value['id'].'[style]" id="'. $value['id'].'_style">';
					
					$styles = array('solid'=>'Solid',
									'dashed'=>'Dashed',
									'dotted'=>'Dotted',
									'none'=>'None');
									
					foreach ($styles as $i=>$style){
						$output .= '<option value="'. $i .'" ' . selected($border_stored['style'], $i, false) . '>'. $style .'</option>';		
					}
					
					$output .= '</select></div>';
					
					/* Border Color */		
					$output .= '<div id="' . $value['id'] . '_color_picker" class="colorSelector"><div style="background-color: '.$border_stored['color'].'"></div></div>';
					$output .= '<input class="of-color of-border of-border-color" name="'.$value['id'].'[color]" id="'. $value['id'] .'_color" type="text" value="'. $border_stored['color'] .'" />';
					
				break;
				
				//images checkbox - use image as checkboxes
				case 'images':
				
					$i = 0;
					
					$select_value = $data[$value['id']];
					
					foreach ($value['options'] as $key => $option) 
					{ 
					$i++;
			
						$checked = '';
						$selected = '';
						if(NULL!=checked($select_value, $key, false)) {
							$checked = checked($select_value, $key, false);
							$selected = 'of-radio-img-selected';  
						}
						$output .= '<span>';
						$output .= '<input type="radio" id="of-radio-img-' . $value['id'] . $i . '" class="checkbox of-radio-img-radio" value="'.$key.'" name="'.$value['id'].'" '.$checked.' />';
						$output .= '<div class="of-radio-img-label">'. $key .'</div>';
						$output .= '<img src="'.$option.'" alt="" class="of-radio-img-img '. $selected .'" onClick="document.getElementById(\'of-radio-img-'. $value['id'] . $i.'\').checked = true;" />';
						$output .= '</span>';				
					}
					
				break;
				
				//info (for small intro box etc)
				case "info":
					$info_text = $value['std'];
					$output .= '<div class="of-info">'.$info_text.'</div>';
				break;
				
				//display a single image
				case "image":
					$src = $value['std'];
					$output .= '<img src="'.$src.'">';
				break;
				
				//tab heading
				case 'heading':
					if($counter >= 2){
					   $output .= '</div>'."\n";
					}
					$header_class = str_replace(' ','',strtolower($value['name']));
					$jquery_click_hook = str_replace(' ', '', strtolower($value['name']) );
					$jquery_click_hook = "of-option-" . $jquery_click_hook;
					$menu .= '<li class="'. $header_class .'"><a title="'.  $value['name'] .'" href="#'.  $jquery_click_hook  .'">'.  $value['name'] .'</a></li>';
					$output .= '<div class="group" id="'. $jquery_click_hook  .'"><h2>'.$value['name'].'</h2>'."\n";
				break;
				
				//drag & drop slide manager
				case 'slider':
					$_id = strip_tags( strtolower($value['id']) );
					$int = '';
					$int = optionsframework_mlu_get_silentpost( $_id );
					$output .= '<div class="slider"><ul id="'.$value['id'].'" rel="'.$int.'">';
					$slides = $data[$value['id']];
					$count = count($slides);
					if ($count < 2) {
						$oldorder = 1;
						$order = 1;
						$output .= Options_Machine::optionsframework_slider_function($value['id'],$value['std'],$oldorder,$order,$int);
					} else {
						$i = 0;
						foreach ($slides as $slide) {
							$oldorder = $slide['order'];
							$i++;
							$order = $i;
							$output .= Options_Machine::optionsframework_slider_function($value['id'],$value['std'],$oldorder,$order,$int);
						}
					}			
					$output .= '</ul>';
					$output .= '<a href="#" class="button slide_add_button">Add New Slide</a></div>';
					
				break;
				
				//drag & drop block manager
				case 'sorter':
				
					$sortlists = isset($data[$value['id']]) && !empty($data[$value['id']]) ? $data[$value['id']] : $value['std'];
					
					$output .= '<div id="'.$value['id'].'" class="sorter">';
					
					
					if ($sortlists) {
					
						foreach ($sortlists as $group=>$sortlist) {
						
							$output .= '<ul id="'.$value['id'].'_'.$group.'" class="sortlist_'.$value['id'].'">';
							$output .= '<h3>'.$group.'</h3>';
							
							foreach ($sortlist as $key => $list) {
							
								$output .= '<input class="sorter-placebo" type="hidden" name="'.$value['id'].'['.$group.'][placebo]" value="placebo">';
									
								if ($key != "placebo") {
								
									$output .= '<li id="'.$key.'" class="sortee">';
									$output .= '<input class="position" type="hidden" name="'.$value['id'].'['.$group.']['.$key.']" value="'.$list.'">';
									$output .= $list;
									$output .= '</li>';
									
								}
								
							}
							
							$output .= '</ul>';
						}
					}
					
					$output .= '</div>';
				break;
				
				//background images option
				case 'tiles':
					
					$i = 0;
					$select_value = isset($data[$value['id']]) && !empty($data[$value['id']]) ? $data[$value['id']] : '';
					
					foreach ($value['options'] as $key => $option) 
					{ 
					$i++;
			
						$checked = '';
						$selected = '';
						if(NULL!=checked($select_value, $option, false)) {
							$checked = checked($select_value, $option, false);
							$selected = 'of-radio-tile-selected';  
						}
						$output .= '<span>';
						$output .= '<input type="radio" id="of-radio-tile-' . $value['id'] . $i . '" class="checkbox of-radio-tile-radio" value="'.$option.'" name="'.$value['id'].'" '.$checked.' />';
						$output .= '<div class="of-radio-tile-img '. $selected .'" style="background: url('.$option.')" onClick="document.getElementById(\'of-radio-tile-'. $value['id'] . $i.'\').checked = true;"></div>';
						$output .= '</span>';				
					}
					
				break;
				
				//backup and restore options data
				case 'backup':
				
					$instructions = $value['desc'];
					$backup = get_option(BACKUPS);
					
					if(!isset($backup['backup_log'])) {
						$log = 'No backups yet';
					} else {
						$log = $backup['backup_log'];
					}
					
					$output .= '<div class="backup-box">';
					$output .= '<div class="instructions">'.$instructions."\n";
					$output .= '<p><strong>'. __('Last Backup : ').'<span class="backup-log">'.$log.'</span></strong></p></div>'."\n";
					$output .= '<a href="#" id="of_backup_button" class="button" title="Backup Options">Backup Options</a>';
					$output .= '<a href="#" id="of_restore_button" class="button" title="Restore Options">Restore Options</a>';
					$output .= '</div>';
				
				break;
				
				//export or import data between different installs
				case 'transfer':
				
					$instructions = $value['desc'];
					$output .= '<textarea id="export_data" rows="8">'.base64_encode(serialize($data)) /* 100% safe - ignore theme check nag */ .'</textarea>'."\n";
					$output .= '<a href="#" id="of_import_button" class="button" title="Restore Options">Import Options</a>';
				
				break;
			
			}
			
			//description of each option
			if ( $value['type'] != 'heading' ) { 
				if(!isset($value['desc'])){ $explain_value = ''; } else{ 
					$explain_value = '<div class="explain">'. $value['desc'] .'</div>'."\n"; 
				} 
				$output .= '</div>'.$explain_value."\n";
				$output .= '<div class="clear"> </div></div></div>'."\n";
				}
		   
		}
		
	    $output .= '</div>';
	    
	    return array($output,$menu,$defaults);
	    
	}


	/**
	 * Ajax image uploader - supports various types of image types
	 *
	 * @uses get_option()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function optionsframework_uploader_function($id,$std,$mod){
	
	    $data =get_option(OPTIONS);
		
		$uploader = '';
	    $upload = $data[$id];
		$hide = '';
		
		if ($mod == "min") {$hide ='hide';}
		
	    if ( $upload != "") { $val = $upload; } else {$val = $std;}
	    
		$uploader .= '<input class="'.$hide.' upload of-input" name="'. $id .'" id="'. $id .'_upload" value="'. $val .'" />';	
		
		$uploader .= '<div class="upload_button_div"><span class="button image_upload_button" id="'.$id.'">'._('Upload').'</span>';
		
		if(!empty($upload)) {$hide = '';} else { $hide = 'hide';}
		$uploader .= '<span class="button image_reset_button '. $hide.'" id="reset_'. $id .'" title="' . $id . '">Remove</span>';
		$uploader .='</div>' . "\n";
	    $uploader .= '<div class="clear"></div>' . "\n";
		if(!empty($upload)){
			$uploader .= '<div class="screenshot">';
	    	$uploader .= '<a class="of-uploaded-image" href="'. $upload . '">';
	    	$uploader .= '<img class="of-option-image" id="image_'.$id.'" src="'.$upload.'" alt="" />';
	    	$uploader .= '</a>';
			$uploader .= '</div>';
			}
		$uploader .= '<div class="clear"></div>' . "\n"; 
	
		return $uploader;
	
	}

	/**
	 * Native media library uploader
	 *
	 * @uses get_option()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function optionsframework_media_uploader_function($id,$std,$int,$mod){
	
	    $data =get_option(OPTIONS);
		
		$uploader = '';
	    $upload = $data[$id];
		$hide = '';
		
		if ($mod == "min") {$hide ='hide';}
		
	    if ( $upload != "") { $val = $upload; } else {$val = $std;}
	    
		$uploader .= '<input class="'.$hide.' upload of-input" name="'. $id .'" id="'. $id .'_upload" value="'. $val .'" />';	
		
		$uploader .= '<div class="upload_button_div"><span class="button media_upload_button" id="'.$id.'" rel="' . $int . '">Upload</span>';
		
		if(!empty($upload)) {$hide = '';} else { $hide = 'hide';}
		$uploader .= '<span class="button mlu_remove_button '. $hide.'" id="reset_'. $id .'" title="' . $id . '">Remove</span>';
		$uploader .='</div>' . "\n";
		$uploader .= '<div class="screenshot">';
		if(!empty($upload)){	
	    	$uploader .= '<a class="of-uploaded-image" href="'. $upload . '">';
	    	$uploader .= '<img class="of-option-image" id="image_'.$id.'" src="'.$upload.'" alt="" />';
	    	$uploader .= '</a>';			
			}
		$uploader .= '</div>';
		$uploader .= '<div class="clear"></div>' . "\n"; 
	
		return $uploader;
		
	}

	/**
	 * Drag and drop slides manager
	 *
	 * @uses get_option()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function optionsframework_slider_function($id,$std,$oldorder,$order,$int){
	
	    $data = get_option(OPTIONS);
		
		$slider = '';
		$slide = array();
	    $slide = $data[$id];
		
	    if (isset($slide[$oldorder])) { $val = $slide[$oldorder]; } else {$val = $std;}
		
		//initialize all vars
		$slidevars = array('title','url','link','description');
		
		foreach ($slidevars as $slidevar) {
			if (!isset($val[$slidevar])) {
				$val[$slidevar] = '';
			}
		}
		
		//begin slider interface	
		if (!empty($val['title'])) {
			$slider .= '<li><div class="slide_header"><strong>'.stripslashes($val['title']).'</strong>';
		} else {
			$slider .= '<li><div class="slide_header"><strong>Slide '.$order.'</strong>';
		}
		
		$slider .= '<input type="hidden" class="slide of-input order" name="'. $id .'['.$order.'][order]" id="'. $id.'_'.$order .'_slide_order" value="'.$order.'" />';
	
		$slider .= '<a class="slide_edit_button" href="#">Edit</a></div>';
		
		$slider .= '<div class="slide_body">';
		
		$slider .= '<label>Title</label>';
		$slider .= '<input class="slide of-input of-slider-title" name="'. $id .'['.$order.'][title]" id="'. $id .'_'.$order .'_slide_title" value="'. stripslashes($val['title']) .'" />';
		
		$slider .= '<label>Image URL</label>';
		$slider .= '<input class="slide of-input" name="'. $id .'['.$order.'][url]" id="'. $id .'_'.$order .'_slide_url" value="'. $val['url'] .'" />';
		
		$slider .= '<div class="upload_button_div"><span class="button media_upload_button" id="'.$id.'_'.$order .'" rel="' . $int . '">Upload</span>';
		
		if(!empty($val['url'])) {$hide = '';} else { $hide = 'hide';}
		$slider .= '<span class="button mlu_remove_button '. $hide.'" id="reset_'. $id .'_'.$order .'" title="' . $id . '_'.$order .'">Remove</span>';
		$slider .='</div>' . "\n";
		$slider .= '<div class="screenshot">';
		if(!empty($val['url'])){
			
	    	$slider .= '<a class="of-uploaded-image" href="'. $val['url'] . '">';
	    	$slider .= '<img class="of-option-image" id="image_'.$id.'_'.$order .'" src="'.$val['url'].'" alt="" />';
	    	$slider .= '</a>';
			
			}
		$slider .= '</div>';	
		$slider .= '<label>Link URL (optional)</label>';
		$slider .= '<input class="slide of-input" name="'. $id .'['.$order.'][link]" id="'. $id .'_'.$order .'_slide_link" value="'. $val['link'] .'" />';
		
		$slider .= '<label>Description (optional)</label>';
		$slider .= '<textarea class="slide of-input" name="'. $id .'['.$order.'][description]" id="'. $id .'_'.$order .'_slide_description" cols="8" rows="8">'.stripslashes($val['description']).'</textarea>';
	
		$slider .= '<a class="slide_delete_button" href="#">Delete</a>';
	    $slider .= '<div class="clear"></div>' . "\n";
	
		$slider .= '</div>';
		$slider .= '</li>';
	
		return $slider;
		
	}
	
}//end Options Machine class

?>