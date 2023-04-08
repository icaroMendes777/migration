<?php

echo 'ok';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$dir    = './files_to_migrate';
$listFiles = getClearFileList($dir);

debug($listFiles);

$fileText = getFileContent($dir.'/'.$listFiles[2]);

//echo $fileText;

echo getIndex($fileText );

echo getTitle($fileText );

echo getTranslatedTitle($fileText );


echo getSuttaBody($fileText );












class Sutta {

    public $filePath;

    function __construct($file)
    {
        $this->filePath = $file;
    }


    function openFile()
    {

    }

} 


function getFileContent($filePath)
{
    $file = $filePath;
    $orig = file_get_contents($file);
   
    return correctEncoding($orig);
}

function getIndex($html)
{
    preg_match("/<p class=Tit3 align=center style='text-align:center'><b>(.*?)<\/b>/s", $html, $match);

    return($match[0]);
}

function getTitle($html)
{
    preg_match("/<p class=Tit1 align=center style='text-align:center'>(.*?)<\/Tit1>/s", $html, $match);

    return($match[0]);
}


function getTranslatedTitle($html)
{
    preg_match_all("/<p class=Tit1 align=center style='text-align:center'>(.*?)<\/Tit1>/s", $html, $match);

    return($match[0][1]);
}

function getSuttaBody($html)
{
    preg_match('/<hr size=2 width="100%" align=center>(.*?)<hr size=2 width/s', $html, $match);


    return str_replace('<hr size=2 width="100%" align=center>','',$match[0]);
    //return($match[0]);
}


function correctEncoding($text)
{
    return mb_convert_encoding($text, 'UTF-8', mb_list_encodings());
}


function getClearFileList($dir)
{
    $listFiles = array_diff(scandir($dir), array('..', '.'));
    return $listFiles;
}




function debug($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}