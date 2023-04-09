<?php


/**
 * 
 * IMPORTAÇÃO DE DADOS DE SISTEMA LEGADO - acesso ao insight
 * 
 * os textos do site antigo estão em arquivos php
 * o escript a seguir lê a lista de suttas no arquivo files_to_migrate
 * e importa o conteúdo deles já formatado para um banco de dados no formato WordPress
 * como posts
 * 
 */

require './database.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * 
 * =======================================================
 * 
 * CONFIGURAÇÕES
 * 
 * =======================================================
 */




$dir    = './files_to_migrate';


$_POSTS_TABLE = 'w1_posts';


/**
 * 
 * =======================================================
 */


$listFiles = getClearFileList($dir);

debug($listFiles);


//--------lista arquivos e um por um extrai o conteúdo e insere no BD
foreach($listFiles as $file)
{
    insertSuttaAsPost($dir ,$file);
}

echo 'Success.';


echo getAllPosts();


die();

/**
 * THE END
 * =======================================================
 */

/**----------------------------------------------------
 * 
 * =======================================================
 * 
 * FUNCTIONS:
 * 
 * 
 */




function insertSuttaAsPost($dir, $fileName)
{

    $fileName = $dir.'/'.$fileName;

    $fileText = getFileContent($fileName);
    
    /**
     * Funções comentadas para serem usadas mais tarde,
     * alguns requisitos ainda estão para mudar
     */

    //echo $fileText;
    
    //echo getSuttaCollection($fileName);
    
    //br();
    
    // echo getSuttaIndex($fileName);
    
    // echo getIndex($fileText );
    
    // echo getTitle($fileText );
    
    // echo getTranslatedTitle($fileText );
    
    // echo getSuttaBody($fileText );

    $post = [   'text'=> getSuttaBody($fileText ),
                'title'=> getSuttaIndex($fileName).' - '.getTranslatedTitle($fileText ),
                'name'=>'name'];

    insertPost($post);
    
}

/**
 * 
 * =======================================================
 * 
 * 
 * FUNÇÕES DE ACESSO À TABELA DE POSTS
 * 
 * 
 * 
 * =======================================================
 */



function getAllPosts()
{
   
		$conexao = $GLOBALS['conn'];
		$sql = "SELECT * FROM ".$GLOBALS['_POSTS_TABLE'];
		$result = $conexao->query($sql);
    

    $data = [];
		if ($result->num_rows > 0) {
		  // output data of each row
		  while($row = $result->fetch_assoc()) {
      $data[] = $row;
			
		  }
		} else {
		  //echo "0 results";
		}

    return(json_encode($data));
}


function insertPost($data)
{
		$conexao = $GLOBALS['conn'];
		
        $data = formatDataToinsertDB($data);
        
		$sql = "INSERT INTO `".$GLOBALS['_POSTS_TABLE']."` (`post_author`, `post_date`, `post_date_gmt`, `post_content`,
                             `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, 
                             `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, 
                             `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`,
                              `menu_order`, `post_type`, `post_mime_type`, `comment_count`) 
                              
                              VALUES ( '1', '2023-04-08 05:55:12', '2023-04-08 05:55:12', '".$data['text']."', '".$data['title']."', '', 'publish', 'open', 'open', '', '".$data['name']."', '', '', '2023-04-08 05:55:12', '2023-04-08 05:55:12', '', '0', '', '0', 'post', '', '0'); ";
		
        //debug($sql);
		$result = $conexao->query($sql);
		
		if ($result) return ($conexao->insert_id);
		 
		return null;		
}



/**
 * 
 * =======================================================
 * 
 * 
 * FUNÇÕES PARA EXTRAÇÃO E FORMATAÇÃO DOS DADOS
 * 
 * 
 * 
 * =======================================================
 */

function formatDataToinsertDB($data)
{
    $data['title'] = cutHtmlFromTitle( $data['title']);
    $data['name'] =  formatTextQuotesToSQL( $data['name']);
    $data['text'] =  formatTextQuotesToSQL( $data['text']);

    return $data;
}



function getSuttaCollection($fileName)
{
    $index = getSuttaIndex($fileName);
    $colection = substr($index, 0, 2); 
    
    return $colection;
}

function getSuttaIndex($fileName)
{

    $name = str_replace('.php','',$fileName);
    $name = str_replace('./files_to_migrate/','',$name);

    return $name;
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


    $str =  str_replace('<hr size=2 width="100%" align=center>','',$match[0]);
    $str = str_replace('<hr size=2 width','',$str);

    return $str;    
    
    //return($match[0]);
}


function cutHtmlFromTitle($title)
{
   // $text = str_replace("'","\'",$title);

    $title = str_replace("<p class=Tit1 align=center style='text-align:center'> ","",$title);
    $title = str_replace(" </Tit1>","",$title);  

    return $title;

}

function formatTextQuotesToSQL($text)
{
    $text = str_replace("'","\'",$text);
   // $text =  str_replace('"','\"',$text);

    return $text;
}

/**
 * 
 * =======================================================
 * 
 * 
 * FUNÇÕES PARA MANIPULAÇÃO DOS ARQUIVOS
 * 
 * 
 * 
 * =======================================================
 */


function correctEncoding($text)
{
    return mb_convert_encoding($text, 'UTF-8', mb_list_encodings());
}


function getClearFileList($dir)
{
    $listFiles = array_diff(scandir($dir), array('..', '.'));
    return $listFiles;
}


/**
 * 
 * =======================================================
 * 
 * 
 * FUNÇÕES AUXILIARES
 * 
 * 
 * 
 * =======================================================
 */



function debug($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function br()
{
    echo '<br/><br/>';
}

