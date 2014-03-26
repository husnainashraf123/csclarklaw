<?php
global $data;
$customfont = '';
$default = array(
    'arial',
    'verdana',
    'trebuchet',
    'georgia',
    'times',
    'tahoma',
    'helvetica',
    );

$googlefonts = array(
    $data['font_body']['face'],
    $data['font_h1']['face'],
    $data['font_h2']['face'],
    $data['font_h3']['face'],
    $data['font_h4']['face'],
    $data['font_h5']['face'],
    $data['font_h6']['face']
);
foreach ($googlefonts as $getfonts) {
    if (!in_array($getfonts, $default)) {
        $customfont = str_replace(' ', '+', $getfonts) . ':400,400italic,700,700italic|' . $customfont;
    }
}
if ($customfont != '') {
    echo "<link href='http://fonts.googleapis.com/css?family=" . substr_replace($customfont, "", -1) . "&amp;subset=latin,latin-ext,cyrillic,cyrillic-ext,greek-ext,greek,vietnamese' rel='stylesheet' type='text/css'>";
}
?>