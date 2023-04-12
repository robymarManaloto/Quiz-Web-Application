<?php

require "vendor/autoload.php";

session_start();

use App\QuestionManager;

$score = null;
try {
    $manager = new QuestionManager;
    $manager->initialize();

    if (!isset($_SESSION['answers'])) {
        throw new Exception('Missing answers');
    }
    $score = $manager->computeScore($_SESSION['answers']);
      $results = $manager->results($_SESSION['answers']);
} catch (Exception $e) {
    echo '<h1>An error occurred:</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
    exit;
}

$file = "download.txt";
$txt = fopen($file, "w") or die("Unable to open file!");

$output = "Complete Name: ".$_SESSION['user_complete_name']."
Email: ".$_SESSION['user_email']."
Birthdate: ".$_SESSION['user_birthdate']."
Score: ".$score." out of ".$manager->getQuestionSize()."
Answers:
";

foreach ($results as $number => $answers) {
    if($answers[1] == 1){
        $output .= $number.") ".$answers[0]."   (correct) \n";
    }else{
        $output .= $number.") ".$answers[0]."   (incorrect) \n";
    }
    
};

fwrite($txt, $output);
fclose($txt);

header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename='.basename($file));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
header("Content-Type: text/plain");
readfile($file);