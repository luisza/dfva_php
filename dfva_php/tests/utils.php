<?php
const TIMEWAIT = 6;
const AUTH_WAIT = 0.5;


const AUTHENTICATION_RESPONSE_TABLE = [
  '500000000000'=>     [1, '=', 0, 0],
  '01-1919-2222'=>     [4, '=', 0, 0],
  '01-1919-2020'=>     [5, '=', 0, 0],
  '01-1919-2121'=>     [9, '=', 0, 0],
  '9-0000-0000-000'=>  [10, '=', 0, 0],
  # Con notificacion
  '100000000000'=>     [0, '!', 0, 1],
  '01-1010-2020'=>     [0, '!', 0, 2],
  '01-2020-3030'=>     [0, '!', 0, 3],
  '01-4040-5050'=>     [0, '!', 0, 4],
  '01-6060-7070'=>     [0, '!', 0, 9],
  '01-8080-9090'=>     [0, '!', 0, 10],
  '01-1100-2211'=>     [0, '!', 0, 11],
  '01-3344-5566'=>     [0, '!', 0, 13],
  '01-7788-9900'=>     [0, '!', 0, 14]
];


$DOCUMENT_RESPONSE_TABLE = [
    # cedula    respuesta  comparacion   status   respuesta_notificacion
    "500000000000"=> [1, '!', 0, 0],
    "01-1919-2222"=> [4, '=', 0, 0],
    "01-1919-2020"=> [5, '=', 0, 0],
    "01-1919-2121"=> [9, '=', 0, 0],
    "9-0000-0000-000"=> [10, '=', 0, 0],
    # Con notificación
    '100000000000'=> [0, '!', 0, 1],
    '01-1010-2020'=> [0, '!', 0, 2],
    '01-2020-3030'=> [0, '!', 0, 3],
    '01-4040-5050'=> [0, '!', 0, 4],
    '01-6060-7070'=> [0, '!', 0, 9],
    '01-8080-9090'=> [0, '!', 0, 10],
    '01-1100-2211'=> [0, '!', 0, 11],
    '01-3344-5566'=> [0, '!', 0, 13],
    '01-7788-9900'=> [0, '!', 0, 14]
];

$DOCUMENT_FORMATS = ['xml_cofirma', 'xml_contrafirma',
    'odf', 'msoffice', 'pdf'];


function read_files($format, $doc_path="dfva_testdocument/files",
    $post_read_fn=null, $name='test.')
{
    if($post_read_fn == null){
        $post_read_fn = function($a) {return $a;};
    }
    $defaultpath = dirname(__FILE__).'/'.$doc_path.'/';
    $f = null;
    $fpath = null;
    if(in_array($format, ['xml_cofirma', 'xml_contrafirma'])){
        $fpath = $defaultpath."test.xml";
    }else if('odf' == $format) {
        $fpath = $defaultpath."test.odt";
    }else if('msoffice' == $format){
        $fpath = $defaultpath."test.docx";
    }else if('pdf' == $format){
        $fpath = $defaultpath."test.pdf";
    }else{
        $fpath = $defaultpath.$name.$format;
    }
    $arch = fopen($fpath, 'rb');
    $filesize = filesize($fpath);
    $f = fread($arch, $filesize);
    fclose($arch);
    return $post_read_fn($f);
}