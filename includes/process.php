<?php

$errors = [];
$success = false;

$genres = ['Action', 'RPG', 'Strategy', 'Horror', 'Sports', 'Puzzle'];
$platforms = ['PC', 'PlayStation', 'Xbox', 'Nintendo'];
$features = ['Story', 'Multiplayer', 'Exploration', 'Combat', 'Graphics'];

$frequencies = ['Daily', 'Weekly', 'Monthly', 'Rarely'];
$modes = ['Single-player', 'Multiplayer', 'Both'];
$purchases = ['Digital', 'Physical', 'Both'];
$difficulties = ['Easy', 'Normal', 'Hard'];

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

function validateText($value, $min, $max) {
    $length = mb_strlen(trim($value));

    if ($length >= $min && $length <= $max) {
        return true;
    } else {
        return false;
    }
}

function validateChoices($selected, $allowed) {
    if (!is_array($selected)) {
        return false;
    }

    if (empty($selected)) {
        return false;
    }

    foreach ($selected as $value) {
        if (!is_string($value)) {
            return false;
        }

        if (!in_array($value, $allowed, true)) {
            return false;
        }
    }

    return true;
}

function validateDate($value) {
    $date = DateTime::createFromFormat('!Y-m-d', $value);

    if ($date === false) {
        return false;
    }

    if ($date->format('Y-m-d') !== $value) {
        return false;
    }

    if ($date > new DateTime('today')) {
        return false;
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($answers as $key => $default) {

        if (isset($_POST[$key])) {
            $value = $_POST[$key];
        } else {
            $value = $default;
        }

        if (is_array($default)) {

            if (is_array($value)) {
                $answers[$key] = $value;
            } else {
                $answers[$key] = [];
            }

        } else {

            if (is_string($value)) {
                $answers[$key] = trim($value);
            } else {
                $answers[$key] = '';
            }
        }
    }

    if (!filter_var($answers['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (!preg_match('/^[0-9()+.\s-]{7,20}$/', $answers['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    if (!validateDate($answers['date'])) {
        $errors['date'] = 'Please enter a valid date of birth.';
    }

    $shortFields = [
        'name',
        'game',
        'developer',
        'character',
        'first_game',
        'franchise'
    ];

    foreach ($shortFields as $field) {

        if (!validateText($answers[$field], 2, 100)) {
            $errors[$field] = 'Please enter between 2 and 100 characters.';
        }
    }

    $longFields = ['reason', 'memory'];

    foreach ($longFields as $field) {

        if (!validateText($answers[$field], 10, 1000)) {
            $errors[$field] = 'Please enter between 10 and 1000 characters.';
        }
    }

    $checkboxGroups = [
        'genres' => $genres,
        'platforms' => $platforms,
        'features' => $features
    ];

    foreach ($checkboxGroups as $field => $options) {

        if (!validateChoices($answers[$field], $options)) {
            $errors[$field] = 'Please select at least one valid option.';
        }
    }

    $radioGroups = [
        'frequency' => $frequencies,
        'mode' => $modes,
        'purchase' => $purchases,
        'difficulty' => $difficulties
    ];

    foreach ($radioGroups as $field => $options) {

        if (!in_array($answers[$field], $options, true)) {
            $errors[$field] = 'Please select a valid option.';
        }
    }

    if (empty($errors)) {

        $recipient = 'your-email@example.com';
        $subject = 'Video Game Survey Submission';
        $message = "Video Game Survey Results\n\n";

        foreach ($answers as $question => $answer) {

            if (is_array($answer)) {
                $answer = implode(', ', $answer);
            }

            $label = str_replace('_', ' ', $question);
            $label = ucfirst($label);

            $answer = str_replace(["\r", "\n"], ' ', $answer);

            $message = $message . $label . ': ' . $answer . "\n";
        }

        $headers = "From: survey@example.com\r\n";
        $headers = $headers . "Content-Type: text/plain; charset=UTF-8\r\n";

        $mailSent = mail($recipient, $subject, $message, $headers);

        if ($mailSent) {
            $success = true;
        } else {
            $errors['form'] = 'Your answers are valid, but the email could not be sent.';
        }
    }
}
