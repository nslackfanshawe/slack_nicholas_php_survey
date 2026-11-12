<?php

//Creates an arrary to store potential errors
$errors = [];
//Success starts as false as the survey hasnt been submitted yet
$success = false;

//These arrays hold the answers for the multiple choice questions
//These hold the answers for the checkbox questions. These allow multiple selections.
$genres = ['Action', 'RPG', 'Strategy', 'Horror', 'Sports', 'Puzzle'];
$platforms = ['PC', 'PlayStation', 'Xbox', 'Nintendo'];
$features = ['Story', 'Multiplayer', 'Exploration', 'Combat', 'Graphics'];

//These hold the answers for the radio button questions. These allow only one selection.
$frequencies = ['Daily', 'Weekly', 'Monthly', 'Rarely'];
$modes = ['Single-player', 'Multiplayer', 'Both'];
$purchases = ['Digital', 'Physical', 'Both'];
$difficulties = ['Easy', 'Normal', 'Hard'];

//This array stores all of the given answers during the survey. Each holds an empty value until an answer is submitted. 
//The checkbox questions use arrays as they can hold multple answers. 
$answers = [
    'email' => '',
    'phone' => '',
    'date' => '',
    'name' => '',
    'game' => '',
    'developer' => '',
    'character' => '',
    'first_game' => '',
    'franchise' => '',
    'genres' => [],
    'platforms' => [],
    'features' => [],
    'reason' => '',
    'memory' => '',
    'frequency' => '',
    'mode' => '',
    'purchase' => '',
    'difficulty' => ''
];

//This function checks if the answer is within the text limit.
function validateText($value, $min, $max) {
    //Removes the spaces from the front and back of the text and counts the number of characters in the text. 
    $length = mb_strlen($value);

    //Returns true if it is within the min and max length and returns false if otherwise.
    if ($length >= $min && $length <= $max) {
        return true;
    } else {
        return false;
    }
}

//Checks the answers submitted for the checkbox questions
function validateChoices($selected, $allowed) {

    //Makes sure the user selects atleast one option.
    if (empty($selected)) {
        return false;
    }

    //Goes through every selected checkbox answer.
    foreach ($selected as $value) {

        //Checks if the answer is one of the allowed options.
        if (!in_array($value, $allowed, true)) {
            return false;
        }
    }
    //Returns true if all of the selected answers are valid.
    return true;
}

//This function checks if the date of birth is valid.
function validateDate($value) {

    //Converts the submitted date into a PHP date.
    $date = DateTime::createFromFormat('!Y-m-d', $value);

    //Returns false if PHP cannot create the date.
    if ($date === false) {
        return false;
    }

    //Checks that the date is real and matches what the user submitted.
    if ($date->format('Y-m-d') !== $value) {
        return false;
    }

    //Makes sure the date of birth is not in the future.
    if ($date > new DateTime('today')) {
        return false;
    }

    //Returns true if the date passes all of the checks.
    return true;
}

//Only runs the validation when the survey is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Goes through each question and gets the submitted answer.
    foreach ($answers as $key => $default) {

        //Checks if an answer was submitted for the question.
        //If there is no answer it uses the empty default value.
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
        } else {
            $value = $default;
        }

        //Checkbox answers are stored as arrays because they can have multiple selections.
        if (is_array($default)) {

            //Makes sure the submitted checkbox answers are an array.
            if (is_array($value)) {
                $answers[$key] = $value;
            } else {
                $answers[$key] = [];
            }

        } else {

            //Stores the submitted text and removes spaces from the beginning and end.
            if (is_string($value)) {
                $answers[$key] = trim($value);
            } else {
                $answers[$key] = '';
            }
        }
    }

    //Checks if the email address is in a valid format.
    if (!filter_var($answers['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    //Checks if the phone number uses valid characters and is between 7 and 20 characters.
    if (!preg_match('/^[0-9()+.\s-]{7,20}$/', $answers['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    //Checks if the date of birth is valid using the function created earlier.
    if (!validateDate($answers['date'])) {
        $errors['date'] = 'Please enter a valid date of birth.';
    }

    //Stores the names of the six short answer questions.
    $shortFields = [
        'name',
        'game',
        'developer',
        'character',
        'first_game',
        'franchise'
    ];

    //Goes through each short answer and checks its length.
    //Each answer must be between 2 and 100 characters.
    foreach ($shortFields as $field) {

        if (!validateText($answers[$field], 2, 100)) {
            $errors[$field] = 'Please enter between 2 and 100 characters.';
        }
    }

    //Stores the names of the two long answer questions.
    $longFields = ['reason', 'memory'];

    //Checks that each long answer is between 10 and 1000 characters.
    foreach ($longFields as $field) {

        if (!validateText($answers[$field], 10, 1000)) {
            $errors[$field] = 'Please enter between 10 and 1000 characters.';
        }
    }

    //Connects each checkbox question to its allowed answers.
    $checkboxGroups = [
        'genres' => $genres,
        'platforms' => $platforms,
        'features' => $features
    ];

    //Goes through each checkbox question and checks the selected answers.
    foreach ($checkboxGroups as $field => $options) {

        if (!validateChoices($answers[$field], $options)) {
            $errors[$field] = 'Please select at least one valid option.';
        }
    }

    //Connects each radio button question to its allowed answers.
    $radioGroups = [
        'frequency' => $frequencies,
        'mode' => $modes,
        'purchase' => $purchases,
        'difficulty' => $difficulties
    ];

    //Checks that each radio button answer is one of the allowed options.
    foreach ($radioGroups as $field => $options) {

        if (!in_array($answers[$field], $options, true)) {
            $errors[$field] = 'Please select a valid option.';
        }
    }

    //Only processes the email if there are no validation errors.
    if (empty($errors)) {

        //Sets the email address that will receive the survey answers.
        $recipient = 'your-email@example.com';

        //Sets the subject and the starting text of the email.
        $subject = 'Video Game Survey Submission';
        $message = "Video Game Survey Results\n\n";

        //Goes through each question and adds the answer to the email.
        foreach ($answers as $question => $answer) {

            //Converts checkbox answers into a single line of text separated by commas.
            if (is_array($answer)) {
                $answer = implode(', ', $answer);
            }

            //Replaces underscores with spaces and capitalizes the first letter.
            $label = str_replace('_', ' ', $question);
            $label = ucfirst($label);

            //Removes line breaks from answers before adding them to the email.
            $answer = str_replace(["\r", "\n"], ' ', $answer);

            //Adds the question and its answer to the email message.
            $message = $message . $label . ': ' . $answer . "\n";
        }

        //Sets the sender address and the format of the email.
        $headers = "From: survey@example.com\r\n";
        $headers = $headers . "Content-Type: text/plain; charset=UTF-8\r\n";

        //Attempts to send the email using PHP.
        $mailSent = @mail($recipient, $subject, $message, $headers);

        //Records an error in the server log if the email could not be sent.
        if (!$mailSent) {
            error_log('Survey validated, but email delivery failed.');
        }

        //Displays the thank you message because all survey answers passed validation.
        $success = true;
    }
}
