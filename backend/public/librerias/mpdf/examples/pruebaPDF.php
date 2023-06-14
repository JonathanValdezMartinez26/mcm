<?php



//==============================================================
$lorem = "<p>Nulla felis erat, imperdiet eu, ullamcorper non, nonummy quis, elit. Suspendisse potenti. Ut a eros at ligula vehicula pretium. Maecenas feugiat pede vel risus. Nulla et lectus. Fusce eleifend neque sit amet erat. Integer consectetuer nulla non orci. Morbi feugiat pulvinar dolor. Cras odio. Donec mattis, nisi id euismod auctor, neque metus pellentesque risus, at eleifend lacus sapien et risus. Phasellus metus. Phasellus feugiat, lectus ac aliquam molestie, leo lacus tincidunt turpis, vel aliquam quam odio et sapien. Mauris ante pede, auctor ac, suscipit quis, malesuada sed, nulla. Integer sit amet odio sit amet lectus luctus euismod. Donec et nulla. Sed quis orci. </p><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin vel sem at odio varius pretium. Maecenas sed orci. Maecenas varius. Ut magna ipsum, tempus in, condimentum at, rutrum et, nisl. Vestibulum interdum luctus sapien. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Maecenas consectetuer eros quis massa. Mauris semper velit vehicula purus. Duis lacus. Aenean pretium consectetuer mauris. Ut purus sem, consequat ut, fermentum sit amet, ornare sit amet, ipsum. Donec non nunc. Maecenas fringilla. Curabitur libero. In dui massa, malesuada sit amet, hendrerit vitae, viverra nec, tortor. Donec varius. Ut ut dolor et tellus adipiscing adipiscing. </p><p>Proin aliquet lorem id felis. Curabitur vel libero at mauris nonummy tincidunt. Donec imperdiet. Vestibulum sem sem, lacinia vel, molestie et, laoreet eget, urna. Curabitur viverra faucibus pede. Morbi lobortis. Donec dapibus. Donec tempus. Ut arcu enim, rhoncus ac, venenatis eu, porttitor mollis, dui. Sed vitae risus. In elementum sem placerat dui. Nam tristique eros in nisl. Nulla cursus sapien non quam porta porttitor. Quisque dictum ipsum ornare tortor. Fusce ornare tempus enim. </p><p>Maecenas arcu justo, malesuada eu, dapibus ac, adipiscing vitae, turpis. Fusce mollis. Aliquam egestas. In purus dolor, facilisis at, fermentum nec, molestie et, metus. Vestibulum feugiat, orci at imperdiet tincidunt, mauris erat facilisis urna, sagittis ultricies dui nisl et lectus. Sed lacinia, lectus vitae dictum sodales, elit ipsum ultrices orci, non euismod arcu diam non metus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In suscipit turpis vitae odio. Integer convallis dui at metus. Fusce magna. Sed sed lectus vitae enim tempor cursus. Cras eu erat vel libero sodales congue. Sed erat est, interdum nec, elementum eleifend, pretium at, nibh. Praesent massa diam, adipiscing id, mollis sed, posuere et, urna. Quisque ut leo. Aliquam interdum hendrerit tortor. Vestibulum elit. Vestibulum et arcu at diam mattis commodo. Nam ipsum sem, ultricies at, rutrum sit amet, posuere nec, velit. Sed molestie mollis dui. </p>";
//==============================================================
//==============================================================
//==============================================================

$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$mes = $meses[date('n')-1];

$anio = DATE(Y);

$p = array('h','j','k','l','op');

foreach ($p as $value) {

$prueba = '
<table border="0" style="width:100%;">
    <tr>
        <td colspan="2" style="background-color:#B8B8B8;"><strong>'.$p[0].'</strong>  --- <b>'.$p[1].'<b></td>
    </tr>
    <!-- <tr>
        <td colspan="2" style="background-color:#B8B8B8;"><strong>ID{$value[3}</strong> --- {$value[2]}</td>
    </tr>-->
    <tr>
        <td style="background-color:#E4E4E4;">
            <strong>#Exterior:</strong> '.$p[4].'<br />
            <strong>#Interior:</strong > '.$p[0].'
        </td>
        <td style="background-color:#E4E4E4;">
            <strong>Uso de suelo:</strong> '.$p[0].'
        </td>
    </tr>
    <tr>
        <td style="background-color:#E4E4E4;">
            <strong>Entre:</strong> '.$p[1].'
        </td>
        <td style="background-color:#E4E4E4;">
            <strong>Recámaras:</strong> '.$p[2].'
        </td>
    </tr>
    <tr>
        <td style="background-color:#E4E4E4;">
            <strong>Terreno:</strong> '.$p[3].' m&sup2;
        </td>
        <td style="background-color:#E4E4E4;">
            <strong>Ba&ntilde;os:</strong> '.$p[4].'
        </td>
    </tr>
</table>
';
}


//==============================================================
//==============================================================
//==============================================================

$html = '
<style>
div.mpdf_toc_level_0 {
	padding-right: 2em;	/* match the outdent specified for ToC */
}
</style>

<!-- defines the headers/footers -->

<!--mpdf

<htmlpageheader name="myHTMLHeader">
<div style="text-align: right; border-bottom: 1px solid #000000; font-family: serif; font-size: 8pt;">5piso Inmobiliaria H2</div>
</htmlpageheader>

<htmlpagefooter name="myHTMLFooter">
<table width="100%" style="border-top: 1px solid #000000; vertical-align: top; font-family: sans; font-size: 8pt;"><tr>
<td width="33%">{DATE d-m-Y}</td>
<td width="33%" align="center"><span style="font-size:12pt">{PAGENO}</span></td>
<td width="33%" style="text-align: right;">5piso Inmobiliaria F2</td>
</tr></table>
</htmlpagefooter>

<htmlpageheader name="tocHTMLHeader">
<div style="text-align: right; border-bottom: 1px solid #000000; font-family: serif; font-size: 8pt;">5piso Inmobiliaria Contenido H</div>
</htmlpageheader>

<htmlpagefooter name="tocHTMLFooter">
<table width="100%" style="border-top: 1px solid #000000; vertical-align: top; font-family: sans; font-size: 8pt;"><tr>
<td width="33%">{DATE d-m-Y}</td>
<td width="33%" align="center"><span style="font-size:12pt;">{PAGENO}</span></td>
<td width="33%" style="text-align: right;">5piso Inmobiliaria Contenido F</td>
</tr></table>
</htmlpagefooter>

mpdf-->


<h1>mPDF</h1>
<h2>'.$mes.' - '.$anio.'</h2>

<!-- set the headers/footers - they will occur from here on in the document -->
<tocpagebreak paging="on" links="on" toc-odd-header-name="html_tocHTMLHeader" toc-even-header-name="html_tocHTMLHeaderEven" toc-odd-footer-name="html_tocHTMLFooter" toc-even-footer-name="html_tocHTMLFooterEven" toc-odd-header-value="on" toc-even-header-value="on" toc-odd-footer-value="on" toc-even-footer-value="on" toc-preHTML="&lt;h2&gt;Contenido&lt;/h2&gt;" toc-bookmarkText="Content list" resetpagenum="1" pagenumstyle="1" odd-header-name="html_myHTMLHeader" odd-header-value="on" even-header-name="html_myHTMLHeaderEven" even-header-value="ON" odd-footer-name="html_myHTMLFooter" odd-footer-value="on" even-footer-name="html_myHTMLFooterEven" even-footer-value="on" outdent="2em" toc-pagenumstyle="i" />

';

//==============================================================
//include("../mpdf.php");
include("/home/crminmobi/backend/App/controllers/mpdf/mpdf.php");

$mpdf=new mPDF('c','A4','','',32,25,27,25,16,13); 

//$mpdf->mirrorMargins = 1;

$mpdf->defaultPageNumStyle = "1";

//$mpdf->SetDisplayMode('fullpage','two');

// LOAD a stylesheet
$stylesheet = file_get_contents('mpdfstyleA4.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->WriteHTML($html);

//$mpdf->WriteHTML($prueba);


// Alternative ways to mark ToC entries and Bookmarks
// This will automatically generate entries from the <h4> tag
$mpdf->h2toc = array('H4'=>0);
$mpdf->h2bookmarks = array('H4'=>0);

//==============================================================
// CONTENT
for ($j = 1; $j<7; $j++) { 
   for ($x = 1; $x<7; $x++) {


	$mpdf->WriteHTML('<h4>Section '.$j.'.'.$x.'</h4>',2);

	$html = '';
	// Split $lorem into words

	$words = preg_split('/([\s,\.]+)/',$prueba,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($words as $i => $e) {
	   if($i%2==0) {
		$y =  rand(1,10); 	// every tenth word
		// If it is just a word use it as an index entry
		if (preg_match('/^[a-zA-Z]{4,99}$/',$e) && ($y > 8)) {
			$content = trim($e);
			$html .= '<indexentry content="'.$content.'" />';
			$html .= '<i>'.$e . '</i>';
		}
		else { $html .= $e; }
	   }
	   else { $html .= $e; }
	}
	$mpdf->WriteHTML($html);
   }
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$mpdf->WriteHTML($html);
$nombre_archivo = "MPDF_".uniqid().".pdf";/* se genera un nombre unico para el archivo pdf*/
$mpdf->Output("/home/crminmobi/backend/public/carpeta/".$nombre_archivo);/* se genera el pdf en la ruta especificada*/
echo $nombre_archivo;/* se imprime el nombre del archivo para poder retornarlo a CrmCatalogo/index */
//$mpdf->Output();
exit;
//==============================================================
//==============================================================


?>