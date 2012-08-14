<?php

$fh = fopen('rendered.php', 'r');
$output = "";
while( $line = fgets($fh) )
{
	$output .= $line;
}


        libxml_use_internal_errors(true);


     $dom = new DOMDocument;
        $dom->strictErrorChecking = false;
        $dom->loadHTML($output);

echo "\n\n\n";
echo "Dom loaded.";
    $body_node = $dom->getElementsByTagName('body')->item(0);
        if( ! $body_node )
        {
            error_log("Could not find HTML body in the registration mark replace plugin.");
            return;
        }
        $body = innerHTML($body_node);
        if( empty($body) )
        {
            error_log("Could not process the HTML in the registration mark replace plugin.");
            return;
        }
        $body = replace($body);
        $output = preg_replace('/(<body[^>]*>).+(<\/body>)/sm', '$1'.$body.'$2', $output);

echo $output;
exit;

 function replace( $text )
    {
    // Replace the old style of registration marks.
     $clean_regex = '/<span class="register_mark">(\s*)(®|\&reg;)(\s*)<\/span>/';
     $clean_replace = '$1®$3';

    // Apply a class and style for all registration marks.
     $regex = '/(®|\&reg;)/';
     $replace = '<sup class="superscript-inline">\&reg;</sup>';

     $text = preg_replace($clean_regex, $clean_replace, $text);
     $text = preg_replace($regex, $replace, $text);
return $text;
}
       

function innerHTML($node)
{
	$innerHTML= '';
	$children = $node->childNodes;
	foreach ($children as $child) {
		$innerHTML .= $child->ownerDocument->saveHTML( );
	}

	return $innerHTML;
}
