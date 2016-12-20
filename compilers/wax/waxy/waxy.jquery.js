// Sealed Wax® · Waxy JS
// http://code.SealedWax.com/
// Unisus® the Victor Antago, god of gravity
// Love to The Highest
(function( $ ){
	$.fn.waxyattr = function( options ) {
		// Set default settings
		var settings = $.extend({
			// variables
			attribute:			'class',
			value:				'*',
			trigger:			'auto',
			// rebel selector vars
			parent: 			null,
			zigzag:				null
		}, options );
		// Process each rebellious child
		return this.each(
			function(){
				// Find the target
				var target = $(this);
				if ( settings.parent!==null ) target = $(target).parents(settings.parent);
				if ( settings.zigzag!==null ) target = $(target).find(settings.zigzag);
				// Check its attributes
				var attr		= $(target).attr(settings.attribute);
				var values	= (typeof attr!==typeof undefined && attr!==false) ? attr.split(" ") : [];
				var exists	= values.indexOf(settings.value);
				if ( settings.trigger=="auto" ) settings.trigger = exists==-1 ? "on" : "off";
				switch ( settings.trigger ) {
					case "on":
						var filtered = values;
						if ( exists==-1 ) filtered.push( settings.value );
						break;
					case "off":
						/*if ( exists!=-1 ) {
							var filtered = [];
							for ( i=0; i<values.length; i++ )
								if ( values[i]!=settings.value ) filtered.push( settings.value );
						}
						else var filtered = values;*/
						var filtered = exists==-1 ? values : values.filter(function(v){return v!=settings.value;});
						break;
					case "replace":
						var filtered = [ settings.value ];
						break;
				}
				filtered = filtered.join(" ");
				$(target).attr(settings.attribute,filtered);
			}
		);
	};
})(jQuery);
