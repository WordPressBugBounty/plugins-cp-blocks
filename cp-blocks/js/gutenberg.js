( function( blocks, element ) {
	var el = element.createElement;

	/* Plugin Category */
	blocks.getCategories().push({slug: 'cpblocks', title: 'CP Blocks'});

	/* ICONS */
	const iconCPBLOCKS = el('img', { width: 20, height: 20, src:  "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAyZJREFUOI0101uIlGUcx/Hv87zPO/O+M7MzO3tqlXUSrbzYjA66gRVIiiS5hm5FJV4UYggSqVSSIEQFkTcdvDM6WFpdSB46oLutBXsjoVGwQRaboLVrzbqH2Zl55533ef5d7Pq5/fG7+vFTIiLOWgC05wHQiCJGBof48sgHhJkMTz2/k76HHsQzBgBrLUoptNYomYdSin/Hxzn31UkGT57ir8t/4BmDOAfAHXf28sjjA6zr30ShWORmRzWclUs/XWTw2HEuDJ+nXC4TZjIEYYiIgFIooF6tEkUNunp6WLthPVsHNrP07ntR/3x4VN57/Q2GkxgvDCmkUmgRrHMIC5QCz2Bcgpu9QcV5TDzwJIfefBXTFse8Vuxke0uO05UZfoyqTDlLFoWvNInW6GYDU50hbr2F8fU7+HvNAFP5EjXtY1xLC1XnWCqwL1dgIJPj27jO91GNyUZEsV4j6VzClVX9XF21mdm2Hkxch9o0eb8Lo30f7Rz1KKIaN+m2ll1O2KIMpwvtvNO3k7G7+pFsBzSqUC5jlQbPI+0pjPg+KpPBW7wI3d4GHe1Ei7opzVXYdP4CB9Zu54VlAbf5TcJ0nthBT8bjrdEKTScYrCW1cQPZg/txE9eRWh0JAtzICJWzI6TrFR7uzFL0DZdnEraUAgJPse/iDPmUxnidHdS/O4e/+j7Myl7m9rxC/POvhMU8fneJmigeG5qEapMXV7eyotWwdXiScuxoT2s06RRYy9xLB4iOHiP/6RHCXTsgSVBKLcwIn2/s4ullGR4dKvNf7MinNQYwpFJIrY4q5Im+OEHz0i9k3z2Et/xW5P2PiUVxZl0HsbXc//V1TEoD4GJH4Cm0WdlLZv9eVBiA1tg/x5jd9hx27ArNMMPiFAyORzzxwyTKaJKGo5T1OLymSClnUOKcoBRueprok+M0TpzC3pgiwHFtyXL6tr1NJTHgHG1Zj5d7c+xekSPrawTmz4S1oDUohRufoPbRZ6gz33A1leOeZw9jghS7bw/Z29tCZ+DhBATwFCA3OSeSJCLOiRMRGR2V3/YclGfOXpPfK1ZERKwTaTqZzxf8D1wTgF/GZm2CAAAAAElFTkSuQmCC" } );

	/* CP Blocks Code */
	blocks.registerBlockType( 'cpblocks/block', {
		title: 'Insert Blocks',
		icon: iconCPBLOCKS,
		category: 'cpblocks',
		supports: {
			customClassName: false,
			className: false
		},
		attributes : {
			block : {
				type : 'string',
				default : ''
			}
		},
		edit: function( props )
		{
			var focus 	  = props.isSelected,
				$ = jQuery,
				d = '<!-- The Block has been inserted previously -->',
				g = false,
				b;

			window['IS_BLOCK_INSERTED'] = function(block)
			{
				if('onetime' in block && block.onetime*1)
				{
					var e = $('#editor'),
						h = (e.length) ? e.html() : '',
						x = new RegExp('<\\!\\-\\-\\s*'+block.id+'\\s*\\-\\->', 'i');

					if(x.test(h)) g = true;
					else e.find('*').each(function(){
						var e = $(this);
						if(
							e.text() &&
							x.test(e.text()))
						{
							g = true;
							return false;
						}
					});
				}
				return g;
			}; // End IS_BLOCK_INSERTED

			window['INSERT_BLOCK'] = function( block )
			{
				var code = d;
				if(!/^\s*$/.test(block.code) && !g)
				{
					code = '<!--'+block.id+'-->'+block.code+'<!--END_'+block.id+'-->';
				}
				cpblocksSetBlock(code,true);
			}; // End INSERT_BLOCK

			function cpblocksSetBlock(newBlock, add)
			{
				var b = props.attributes.block;
				if(add) b += newBlock;
				else b = newBlock;
				props.setAttributes({block: b});
			};

			if(
				!!focus &&
				(
					typeof props.attributes.block == 'undefined' ||
					props.attributes.block == ''
				) &&
				(
					$('.blocks_module').length == 0 ||
					!$('.blocks_module').is(':visible')
				)
			)
			{
				$(document).trigger('load_blocks_module','wpcf7');
			}

			return el(
				'textarea',
				{
					key		 : 'cp-blocks-container',
					style	 : {width:'100%'},
					onChange : function(evt){ cpblocksSetBlock( evt.target.value ); },
					value	 : props.attributes.block
				}
			);
		},

		save: function( props ) {
			return el(element.RawHTML, null, props.attributes.block);
		}
	});
} )(
	window.wp.blocks,
	window.wp.element
);