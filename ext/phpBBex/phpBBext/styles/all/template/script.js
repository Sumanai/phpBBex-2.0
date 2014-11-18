function get_selected_text()
{
	var sel = '';
	if (window.getSelection && !is_ie)
	{
		sel = window.getSelection().toString();
	}
	else if (document.getSelection && !is_ie)
	{
		sel = document.getSelection();
	}
	else if (document.selection)
	{
		sel = document.selection.createRange().text;
	}
	return jQuery.trim(sel);
}
