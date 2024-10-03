/*  */
if(jQuery)
{
	jQuery(window).on(
		'load',
		function()
		{
			var $ = jQuery;
			if(window['cff_form'])
			{
			    $("#tabs-1").append('<div style="clear:both;"></div><div>Complementary Blocks</div><hr />');
			    $("#tabs-1").append('<div id="blocks_inserter" class="button itemForm width40" onclick="jQuery(document).trigger(\'load_blocks_module\',\'cfte\');">Insert Blocks</div>');
			    $('#blocks_inserter').button();
            }
			window[ 'IS_BLOCK_INSERTED' ] = function( block )
			{
				if(window['cff_form'])
				{
					var items = cff_form.fBuild.getItems();
					for( var i in items )
					{
						if(
							items[i]['ftype'] &&
							items[i]['ftype'] == 'fhtml' &&
							items[i]['fcontent'] &&
							(new RegExp('<\\!\\-\\-\\s*'+block.id+'\\s*\\-\\->', 'i')).test(items[i]['fcontent'])
						)
						{
							return true;
						}
					}
					return false;
				}
			}; // End IS_BLOCK_INSERTED

			window['INSERT_BLOCK'] = function( block )
			{
				if(window['cff_form'])
				{
					if(!/^\s*$/.test(block.code))
					{
						var code = '<!--'+block.id+'-->'+block.code+'<!--END_'+block.id+'-->',
							fhtml = cff_form.fBuild.addItem('fhtml');
						fhtml.fcontent = code;
						$.fbuilder.reloadItems({field:fhtml});
					}
				}
			}; // End INSERT_BLOCK
		}

	);
}