<?php
error_reporting(E_ALL & ~E_NOTICE);
include_once('Szabdesk.php');


$szab = new SzabdeskApi();

$env = parse_ini_file('.env');
$szab->setUrl($env['URL'] ?? 'https://fallzabdesk.szabist.edu.pk/');


if (isset($_GET['api']) && isset($_POST['cookie'])) {

    $szab->setCookie($_POST['cookie']);

    if(isset($_POST['url'])){
        $szab->setUrl($_POST['url']);
    }
    if ($_GET['api'] == 'profile') {
        echo json_encode($szab->getProfile());
    }
    if ($_GET['api'] == 'courses') {
        echo json_encode($szab->getCourses());
    }
    if ($_GET['api'] == 'result') {
        echo json_encode($szab->getResult());
    }
    if ($_GET['api'] == 'coursesAll') {
        echo json_encode($szab->getAllCourseTaken());
    }
    if ($_GET['api'] == 'schedule') {
        echo json_encode($szab->getCurrentWeekSchedule());
    }
    if ($_GET['api'] == 'attendance') {
        $params = array(
            "txtFac" =>  isset($_POST['txtFac']) ? $_POST['txtFac']  : '' ,
            "txtCou" => isset($_POST['txtCou']) ? $_POST['txtCou']  : '',
            "txtSem" => isset($_POST['txtSem']) ? $_POST['txtSem']  : '',
            "txtSec" => isset($_POST['txtSec']) ? $_POST['txtSec']  : ''
        );
        echo json_encode($szab->getCourseAttendance($params));
    }
    if ($_GET['api'] == 'files') {
        $params = array(
            "txtFac" =>  isset($_POST['txtFac']) ? $_POST['txtFac']  : '' ,
            "txtCou" => isset($_POST['txtCou']) ? $_POST['txtCou']  : '',
            "txtSem" => isset($_POST['txtSem']) ? $_POST['txtSem']  : '',
            "txtSec" => isset($_POST['txtSec']) ? $_POST['txtSec']  : ''
        );
        echo json_encode($szab->getCourseFiles($params));
    }
    if ($_GET['api'] == 'recap') {
        $params = array(
            "txtFac" =>  isset($_POST['txtFac']) ? $_POST['txtFac']  : '' ,
            "txtCou" => isset($_POST['txtCou']) ? $_POST['txtCou']  : '',
            "txtSem" => isset($_POST['txtSem']) ? $_POST['txtSem']  : '',
            "txtSec" => isset($_POST['txtSec']) ? $_POST['txtSec']  : ''
        );
        echo json_encode($szab->getCourseRecap($params));
    }
}