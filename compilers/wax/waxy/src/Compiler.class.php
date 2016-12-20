<?
	// ----------------------------------
	// Waxy Compiler by Sealed Wax®
	// ----------------------------------
	// http://code.SealedWax.com
	// Unisus® · Fincon (epic poem)
	// the Victor Antago, god of gravity
	// ----------------------------------
	// Love to The Highest
	// ----------------------------------
	namespace Wax\Scss\Waxy;
	class Compiler {
		/**************************************/
		/**************************************/
		/**                                  **/
		/**                                  **/
		/**           DECLARATIONS           **/
		/**                                  **/
		/**                                  **/
		/**************************************/
		/**************************************/
		public
			$engineer	= 'Sealed Wax',
			$version	= '1.0',
			$css		= NULL,
			$js			= array( 'include'=>NULL, 'script'=>NULL ),
			$pe			= array( 'blur', 'focus', 'focusin', 'focusout', 'load', 'resize', 'scroll', 'unload', 'click', 'dblclick', 'mousedown', 'mouseup', '', 'mousemove', 'mouseover', 'mouseout', 'mouseenter', 'mouseleave', 'change', 'select', 'submit', 'keydown', 'keypress', 'keyup', 'error' );

		function __construct() {
			// Include the JS functions
			ob_start();
			include_once __DIR__ . '/../waxy.jquery.js';
			$this->js['include'] = ob_get_clean();
			/*error_log( $this->engineer.' - Waxy Compiler v'.$this->version.' loaded' );*/
		}

		/**************************************/
		/**************************************/
		/**                                  **/
		/**                                  **/
		/**         GLOBAL FUNCTIONS         **/
		/**                                  **/
		/**                                  **/
		/**************************************/
		/**************************************/
		function compile( $code ) {
			// Isolate style properties from their selector(s)
			$code 		= preg_replace('#/\*.+?\*/#s','',$code);
			$code 		= explode( '}', $code );
			array_pop( $code ); // Remove ghost
			$styles		= array();
			$items		= array();
			// Handle styles
			foreach ( $code as $i=>$style ) {
				$style		= explode( '{', $style );
				$style[0]	= explode( ',', $style[0] ); // selectors
				$style[1] = explode( ';', trim($style[1]) ); // properties
				foreach ( $style[1] as $j=>&$property ) {
					$property		= trim( $property );
					$property		= explode( ':', $property );
					$property[0] = trim( $property[0] );
					if ( empty($property[0]) ) {
						unset( $style[1][$j] );
						continue;
					}
					if ( !isset($property[1]) ) $property[1] = trim( $property[1] );
				}
				// Break the selectors up & retrieve rebel instances
				foreach ( $style[0] as $j=>&$selector ) {
					$selector	= trim( $selector ); // Trim whitespace
					$item		=
						array(
							'selector'		=> NULL,
							'properties'	=> NULL,
							'event'			=> NULL,		// Pseudo-Event
							'parent'		=> NULL,		// Parent
							'commands'		=> array()		// New Commands
							);
					
					// Get the properties
					$item['properties'] = $style[1];
					
					// Check if the path rebels or is straight-forward
					$carrot		= strpos( $selector, '^' );
					$path		= $carrot!==FALSE ? explode(' ^',$selector) : array($selector);
					
					// Isolate the selector from its Pseudo-Event type
					$item['selector'] = explode( ':', $path[0] );
					
					// Check for Pseudo-Class, then see if it is a Pseudo-Event
					if ( sizeof($item['selector'])>1 ) {
						if ( in_array(end($item['selector']),$this->pe) ) // Check if Pseudo-Event
							$item['event'] = array_pop( $item['selector'] );
						// Convert the selector to its original name
						$item['selector'] = trim( implode(':',$item['selector']) );
					}
					else $item['selector'] = $item['selector'][0];
					
					// Check for rebel selector
					if ( sizeof($path)>1 ) // Rebel selector goes up, or zigzags once
						$item['parent'] = array_slice( $path, 1 );
					
					// Check for new commands
					foreach ( $item['properties'] as $i=>$property ) {
						$command = trim( $property[0] );
						if ( method_exists($this,'command_'.$command) ) 
							$item['commands'][] = $command;
					}
					
					// Store item
					if ( !empty($item['event']) || !empty($item['parent']) || !empty($item['commands']) )
						$items[] = $item;
					// Do not compile Pseudo-Event & Rebel selectors as native CSS
					if ( !empty($item['event']) || !empty($item['parent']) ) {
						unset( $style[0][$j] );
						continue;
					}
				}
				// Store normal style
				if ( sizeof($style[0])>0 ) $styles[] = $style;
			}

			// Start a string
			$css = '';
			// Rebuild the styles
			foreach ( $styles as $style ) {
				$css .= implode(',',$style[0]).'{';
					foreach ( $style[1] as $property )
						$css .= $property[0].':'.$property[1].';';
				$css .= '}';
			}
			//foreach ( $items as $item )
				//$$css .= $item['parent'].'{'.$item['properties'].'}';

			// Build the jQuery event(s)
			if ( !empty($items) ) {
				$script	 = NULL;
				foreach ( $items as $item ) {
					if ( !empty($item['properties']) ) {
						$commands		= array();
						$new_commands	= array();
						foreach ( $item['properties'] as $i=>$property ) {
							$command	= trim( $property[0] );
							if ( method_exists($this,'command_'.$command) ) {
								$new_command = $this->{'command_'.$command}( $item, $property[0], $property[1] );
								if ( $new_command===NULL ) continue;
								$new_commands[] = $new_command;
								continue;
							}
							$commands[]	= '"'.$command.'":"'.trim($property[1]).'"';
						}
					}
					if ( sizeof($commands)===0 && sizeof($new_commands)===0 ) continue;
					
					// Begin code; check if special JS required for New CSS commands (eg. 'attribute')
					$code = sizeof($new_commands)>0 ? implode('',$new_commands) : '';
					// Look for usual CSS commands
					if ( sizeof($commands)>0 ) {
						$code .= '$('.(!empty($item['event'])?'this':'"'.$item['selector'].'"').')';
						if ( isset($item['parent']) && !empty($item['parent']) ) {
							$code .= '.parents("'.trim($item['parent'][0]).'")';
							// Check for zigzag
							if ( sizeof($item['parent'])>1 )
								$code .= '.find("'.$item['parent'][1].'")';
						}
						$code .= '.css({'.implode(',',$commands).'});';
					}
					//$script .= 'wax_scss_rebel(event,"'.$item['parent'].'","'.$item['selector'].'");';
					// Check for Pseudo-Event
					if ( !empty($item['event']) ) {
						$script .= '$("'.$item['selector'].'").bind("'.$item['event'].'",function(event){';
							$script .= $code;
						$script .= '});';
					}
					else $script .= $code;
				}
			}
			// Build javascript
			$this->js['script'] .= $script!==NULL ? '$(document).ready(function(){'.$script.'});' : '';
			
			// Return the css code
			return $css;
		}
		
		//function to handle pertinent new CSS commands
		function command_attribute( $item, $command, $data ) {
			// Validate the property supplied
			$data	= preg_replace( '!\s+!', ' ', trim($data) );
			if ( !preg_match('/[^a-z_\-\*0-9]/i',$data) ) return NULL;
			$data	= explode( ' ', $data );
			// Set default trigger to 'auto'
			if ( sizeof($data)==2 ) $data[] = 'auto';
			// Begin code
			$code = '';
			foreach ( $data as $key=>$var ) {
				switch ( $key ) {
					case 0: // attribute name
						$attribute	 = $var;
						break;
					case 1: // attribute value
						$value		 = $var;
						break;
					case 2: // attribute trigger
						$trigger	= $var;
						switch ( $trigger ) {
							case 'auto':	// auto toggle
								break;
							case 'on':		// turn value on
								if ( $value=='*' ) return NULL;
								break;
							case 'off':		// turn value off
								break;
							case 'replace':	// replace all values with value
								break;
							default: return NULL;
						}
						break;
				}
			}
			// Return the attribute handler code
			$out  = '';
			$out .= '$('.(!empty($item['event'])?'this':'"'.$item['selector'].'"').')';
			$out .= '.waxyattr({
				attribute:		"'.$attribute.'",
				value:			"'.$value.'",
				trigger:		"'.$trigger.'"';
				if ( !empty($item['parent']) ) {
					$out .= ',parent:"'.trim($item['parent'][0]).'"';
					if ( !sizeof($item['parent'])>1)
						$out .= ',zigzag:"'.trim($item['parent'][1]).'"';
				}
			$out .= '});';
			return $out;
		}
	}
?>
