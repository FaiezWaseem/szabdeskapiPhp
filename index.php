<?php
error_reporting(E_ALL & ~E_NOTICE);
include_once("Http.php");
include_once('PhpDom.php');


// Example 1
// $http = new HTTP();
// $html = $http->get('https://www.webscraper.io/test-sites/e-commerce/allinone/computers');

// $document = new PhpDom($html);
// $prices = $document->find('div.card-body h4.price');
// $titles = $document->find('div.card-body h4 a.title');
// $images = $document->find('div.card-body img.image');
// if($prices){
//     for( $i = 0; $i < count($titles); $i++ ){
//         $title = $titles[$i]->nodeValue."<br>";
//         $price = $prices[$i]->nodeValue."<br>";
//         $image = $images[$i]->getAttribute('src');
//         echo "
//           <div>
//           <img  src='https://www.webscraper.io$image' width='200' height='200'/>
//           <h2>
//           $title
//           </h2>
//           <h3>$price</h3>
//           </div>
//         ";
//     }
// }else{
//     echo 'No Price Found';
// }





// Example 2
// $html =  file_get_contents('form.html');
// $document = new PhpDom($html);
// $tables = $document->find('table');
// $trs = $document->findInElement($tables[4] , 'tr');
// $json = [] ;
// for ($i=0; $i < count($trs) ; $i++) { 
//     $tr = $trs[$i];
//     $tds = $document->findInElement($tr , 'td');
//     $row;
//     foreach ($tds as $td) {
//      $row[] = $td->nodeValue;
//     }
//     array_push($json, $row);
// }
// echo json_encode($json);






// Example3 Szabdesk
// $html = file_get_contents('form.html');
// $document = new PhpDom($html);
// $tables = $document->find('#frmCourseOutline table');
// $trs = $document->findInElement($tables[0], 'tr');
// $data = [];
// array_shift($trs);
// array_shift($trs);
// foreach ($trs as $tr) {
//     $coursename = $document->findInElement($tr, 'td:nth-child(2)')[0]->nodeValue;
//     $classWith = $document->findInElement($tr, 'td:nth-child(3)')[0]->nodeValue;
//     $outlineHref = $document->findInElement($tr, 'td:nth-child(4) a')[0];
//     $outlineHref = $outlineHref ? $outlineHref->getAttribute('href') :'';
//     if ($outlineHref) {
//         $jsCode = $outlineHref;
//         $regex = "/'([^']*)'/";
//         $values = [];

//         preg_match_all($regex, $jsCode, $matches);

//         foreach ($matches[1] as $match) {
//             $values[] = $match;
//         }
//         $data[] = [
//             "coursename" => $coursename,
//             "classWith" => $classWith,
//             "data" => [
//                 "txtFac" => $values[1],
//                 "txtCou" => $values[4],
//                 "txtSem" => $values[2],
//                 "txtSec" => $values[3]
//             ]
//         ];
//     }
// }

// echo json_encode($data);
