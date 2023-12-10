<?php
error_reporting(0);
include_once("Http.php");
include_once('PhpDom.php');

class SzabdeskApi extends HTTP
{
    protected $url = 'https://fallzabdesk.szabist.edu.pk/';

    protected $testing = false;
    public function setUrl($_url)
    {
        $this->url = $_url;
    }
    public function getCourses()
    {
        if ($this->testing) {
            $html = file_get_contents('form.html');
        } else {
            $html = $this->get($this->url . "Student/QryCourseOutline.asp");
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
    public function getProfile()
    {
        if ($this->testing) {
            $html = file_get_contents('form.html');
        } else {
            $html = $this->get($this->url . "student.asp?ASIUUFGUFGF=2");
        }
        $document = new PhpDom($html);
        $username = $document->find('.username-top')[0]->nodeValue;
        $dp = $document->find('img.dp-full')[0]->getAttribute('src');
        return [
            'dp' => $this->url .$dp,
            'username' => trim($username)
        ];
    }
    public function getResult()
    {
        if ($this->testing) {
            $html = file_get_contents('home.html');
        } else {
            $html = $this->get($this->url . "Student/StdViewSemesterResult.asp");
        }
        $document = new PhpDom($html);
        $form = $document->find('form[name="StdViewSemesterResult"]')[0];
        $form_url = new PhpElement($form);
        $form_url = $form_url->attr('action');

        $options = $document->find('select[name="cboSemester"] option');
        $selectValues = [];
        foreach ($options as $key => $el) {
            $option = new PhpElement($el);
            array_push($selectValues, [
                'value' => $option->attr('value'),
                'text' => $option->text()
            ]);
        }

        $currentCGPA = null;
        $transcriptCGPA = null;
        foreach ($document->find('table') as $key => $el) {
            $rowData = $document->findInElement($el, 'td')[0];
            $rowData = $rowData ? $rowData->nodeValue : null;
            if ($rowData) {
                if (strpos($rowData, 'Current CGPA:') !== false) {
                    preg_match('/Current CGPA:\s*([\d.]+)/', $rowData, $matches);
                    if ($matches) {
                        $currentCGPA = $matches[1];
                    }
                }
                if (strpos($rowData, 'Provisional Transcript CGPA:') !== false) {
                    preg_match('/Provisional Transcript CGPA:\s*([\d.]+)/', $rowData, $matches);
                    if ($matches) {
                        $transcriptCGPA = $matches[1];
                    }
                }
            }
        }
        return [
            'currentCGPA' => $currentCGPA,
            'transcriptCGPA' => $transcriptCGPA,
            'postURL' => $form_url,
            'dropDown' => $selectValues
        ];
    }
    public function getAllCourseTaken()
    {
        if ($this->testing) {
            $html = file_get_contents('home.html');
        } else {
            $html = $this->get($this->url . "Student/PreviousCourses.asp");
        }
        $document = new PhpDom($html);
        $table = $document->find("form#frmCourseOutline table")[0];
        $trs = $document->findInElement($table, "tr");
        $courses = [];
        array_shift($trs);
        foreach ($trs as $key => $tr) {
            $tds = $document->findInElement($tr, 'td.textColor');
            $course = [];
            $index = 0;
            foreach ($tds as $key => $td) {
                $tdata = new PhpElement($td);
                $tdata = $tdata->text();

                if ($index == 1) {
                    $course['course'] = $tdata;
                }
                if ($index == 2) {
                    $course['instructor'] = $tdata;
                }
                if ($index == 3) {
                    $course['semester'] = $tdata;
                }
                if ($index == 4) {
                    $course['withClass'] = $tdata;
                }
                $index++;
            }
            if (count($course) > 0) {
                $courses[] = $course;
            }

        }
        return $courses;
    }
    public function getCourseAttendance($params)
    {
        if ($this->testing) {
            $html = file_get_contents('home.html');
        } else {
            $html = $this->postWithParams($this->url . "Student/QryCourseAttendance.asp", $params);
        }
        $document = new PhpDom($html);
        $table = $document->find("table.textColor")[5];
        $trs = $document->findInElement($table, 'tr');
        $index = 0;
        $lectures = [];
        foreach ($trs as $tr) {
            if ($index > 1) {
                $tds = $document->findInElement($tr, 'td');
                $lectureNumber = trim(@$tds[0]->nodeValue);
                $lectureDate = trim(@$tds[1]->nodeValue);
                $attendanceStatus = trim(@$tds[2]->nodeValue);
                if (isset($lectureNumber, $lectureDate, $attendanceStatus) && !empty($lectureNumber) && !empty($lectureDate) && !empty($attendanceStatus)) {
                    array_push($lectures, [
                        'number' => $lectureNumber,
                        'date' => $lectureDate,
                        'attendance' => $attendanceStatus
                    ]);
                }
            }
            $index++;
        }
        return $lectures;

    }
    public function getCurrentWeekSchedule(){
     if($this->testing){
        $html =  file_get_contents('home.html');
     }else{
        $html = $this->get($this->url . 'WeeklySchedule.asp');
     }

     $document = new PhpDom($html);
     $table = $document->find('table')[0];
     $trs = $document->findInElement($table, 'tr');
     $week = [];
     $index = 0;
     foreach ($trs as $tr) {
        if($index >= 1){

            $tds = $document->findInElement($tr , 'td');
            $week[] = [
                'campus' => trim(@$tds[0]->nodeValue),
            'room' => trim(@$tds[1]->nodeValue),
            'course' => trim(@$tds[2]->nodeValue),
            'section' => trim(@$tds[3]->nodeValue),
            'faculty' => trim(@$tds[4]->nodeValue),
            'date' => trim(@$tds[5]->nodeValue),
            'start_time' => trim(@$tds[6]->nodeValue),
            'end_time' => trim(@$tds[7]->nodeValue),
            'status' => trim(@$tds[8]->nodeValue),
        ];
    }
        $index++;
     }
     return $week;
    }
}