/*  */
jQuery(window).on(
	'load',
	function()
	{
		var $ = jQuery,
			hadFocus = false,
			codeContainer = $('[id="wpcf7-form"]');

		if( codeContainer.length )
		{
			$("[id='tag-generator-list']").append('<div id="blocks_inserter" class="button button-primary" onclick="jQuery(document).trigger(\'load_blocks_module\',\'wpcf7\');">Insert Blocks</div>');

			codeContainer.on('focus', function(){hadFocus = true;});

			window[ 'IS_BLOCK_INSERTED' ] = function( block )
			{
				return (new RegExp('<\\!\\-\\-\\s*'+block.id+'\\s*\\-\\->', 'i')).test(codeContainer.val());
			}; // End IS_BLOCK_INSERTED

			window['INSERT_BLOCK'] = function( block )
			{
				if(!/^\s*$/.test(block.code))
				{
					var code = '<!--'+block.id+'-->'+block.code+'<!--END_'+block.id+'-->',
						v = codeContainer.val(),
						start = (hadFocus) ? codeContainer.prop('selectionStart') : v.length,
						end   = (hadFocus) ? codeContainer.prop('selectionEnd') : v.length,
						textBefore = v.substring(0, start),
						textAfter  = v.substring(end);
					codeContainer.val( textBefore+ code +textAfter );
				}
			}; // End INSERT_BLOCK
		}
	}

);
