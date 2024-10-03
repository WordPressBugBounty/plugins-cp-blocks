
window[ 'IS_BLOCK_INSERTED' ] = function( block )
{
    try {
	    contents = tinymce.activeEditor.selection.getNode();
	    contentsHTML = contents.innerHTML;
    } catch (e) 
    {
        contentsHTML = document.getElementById("content").value;
    }
	return (new RegExp('<\\!\\-\\-\\s*'+block.id+'\\s*\\-\\->', 'i')).test(contentsHTML);
}; // End IS_BLOCK_INSERTED

window['INSERT_BLOCK'] = function( block )
{
	if(!/^\s*$/.test(block.code))
	{
		var code = '<!--'+block.id+'-->'+block.code+'<!--END_'+block.id+'-->';
	    send_to_editor(code)
	}
}; // End INSERT_BLOCK
