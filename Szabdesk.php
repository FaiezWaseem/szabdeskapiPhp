<?php
error_reporting(E_ALL & ~E_NOTICE);
include_once("Http.php");
include_once('PhpDom.php');

class SzabdeskApi extends HTTP
{
    protected $url = 'https://fallzabdesk.szabist.edu.pk/';

    protected $testing  = false;
    public function setUrl($_url) {
        $this->url = $_url;
    }
    public function getCourses()
    {
        if($this->testing){
            $html = file_get_contents('form.html');
        }else{
            $html = $this->get($this->url."Student/QryCourseOutline.asp");
        }
        $document = new PhpDom($html);
        $tables = $document->find('#frmCourseOutline table');
        $trs = $document->findInElement($tables[0], 'tr');
        $data = [];
        array_shift($trs);
        array_shift($trs);
        foreach ($trs as $tr) {
            $coursename = $document->findInElement($tr, 'td:nth-child(2)')[0]->nodeValue;
            $classWith = $document->findInElement($tr, 'td:nth-child(3)')[0]->nodeValue;
            $outlineHref = $document->findInElement($tr, 'td:nth-child(4) a')[0];
            $outlineHref = $outlineHref ? $outlineHref->getAttribute('href') : '';
            if ($outlineHref) {
                $jsCode = $outlineHref;
                $regex = "/'([^']*)'/";
                $values = [];

                preg_match_all($regex, $jsCode, $matches);

                foreach ($matches[1] as $match) {
                    $values[] = $match;
                }
                $data[] = [
                    "coursename" => $coursename,
                    "classWith" => $classWith,
                    "data" => [
                        "txtFac" => $values[1],
                        "txtCou" => $values[4],
                        "txtSem" => $values[2],
                        "txtSec" => $values[3]
                    ]
                ];
            }
        }

        return ($data);

    }
    public function getProfile(){
        if($this->testing){
            $html = file_get_contents('form.html');
        }else{
            $html = $this->get($this->url."student.asp?ASIUUFGUFGF=2");
        }
        $document = new PhpDom($html);
        $username = $document->find('.username-top')[0]->nodeValue;
        $dp = $document->find('img.dp-full')[0]->getAttribute('src');
        return [
            'dp' => $dp,
            'username'=> $username
        ];
    }
}


$env = parse_ini_file('.env');


$szab = new SzabdeskApi();
$szab->setUrl($env['URL']);
$szab->setCookie($env['COOKIE']);


echo json_encode($szab->getCourses());