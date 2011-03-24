/*

Correctly handle PNG transparency in Win IE 5.5 & 6.
http://homepage.ntlworld.com/bobosola. Updated 18-Jan-2006.

Use in <HEAD> with DEFER keyword wrapped in conditional comments:
<!--[if lt IE 7]>
<script defer type="text/javascript" src="pngfix.js"></script>
<![endif]-->

*/

if (is_ie5up && !is_ie7up && document.body.filters)
{
	// Find the relevant images to act upon.
	for (var i = 0; i < document.images.length; i++)
	{
		var img = document.images[i];
		var imgName = img.src.toLowerCase();
		if (img.className && img.className.indexOf("png_fix") != -1 && imgName.substr(imgName.length - 3, imgName.length) == "png")
		{
			var newImageHTML = '<span';
			newImageHTML += img.id ? ' id="' + img.id + '"' : '';
			newImageHTML += img.title ? ' title="' + img.title + '"' : (img.alt ? ' title="' + img.alt + '"' : '');
			newImageHTML += img.className ? ' class="' + img.className + '"' : '';
			var ImgStyle = 'display:inline-block;' + img.style.cssText + ';';
			ImgStyle += img.parentElement.href ? 'cursor:hand;' : '';
			if (img.width)
				ImgStyle += 'width:' + img.width + 'px;height:' + img.height + 'px;';
			ImgStyle += 'filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + img.src + '\', sizingMethod=\'scale\');';

			newImageHTML += 'style="' + ImgStyle + '"';
			newImageHTML += '></span>';
			img.outerHTML = newImageHTML;

			i--;
		}
	}
}