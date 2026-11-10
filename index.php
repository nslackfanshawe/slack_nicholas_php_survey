<?php
require_once 'includes/process.php';

function escapeHtml($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Game Survey</title>
    <link rel="stylesheet" href="css/grid.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<header class="grid-con center-grid-con">
    <div class="col-span-full">
        <h1>Video Game Survey</h1>
        <p>Tell us about your gaming preferences!</p>
    </div>
</header>

<main class="grid-con center-grid-con">
    <div class="col-span-full">

        <?php
        if ($success) {
            echo '<h2>Thank you!</h2>';
            echo '<p>Your survey has been submitted.</p>';
            echo '<a href="index.php">Submit another response</a>';
        } else {
        ?>

        <?php
        if (!empty($errors)) {
            echo '<div role="alert">';
            echo '<p>Please correct the errors below.</p>';
            echo '</div>';
        }
        ?>

        <form action="index.php" method="post" novalidate>
            <div class="grid-con">

                <section class="col-span-full">
                    <h2>Personal Information</h2>
                </section>

                <div class="col-span-full m-col-span-6">
                    <label for="email">Email Address</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        value="<?php echo escapeHtml($answers['email']); ?>"
                        <?php
                        if (isset($errors['email'])) {
                            echo 'aria-invalid="true" aria-describedby="error-email"';
                        }
                        ?>
                    >

                    <?php
                    if (isset($errors['email'])) {
                        echo '<p class="error" id="error-email">';
                        echo escapeHtml($errors['email']);
                        echo '</p>';
                    }
                    ?>
                </div>

                <div class="col-span-full m-col-span-6">
                    <label for="phone">Phone Number</label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        required
                        value="<?php echo escapeHtml($answers['phone']); ?>"
                        <?php
                        if (isset($errors['phone'])) {
                            echo 'aria-invalid="true" aria-describedby="error-phone"';
                        }
                        ?>
                    >

                    <?php
                    if (isset($errors['phone'])) {
                        echo '<p class="error" id="error-phone">';
                        echo escapeHtml($errors['phone']);
                        echo '</p>';
                    }
                    ?>
                </div>

                <div class="col-span-full">
                    <label for="date">Date of Birth</label>

                    <input
                        type="date"
                        id="date"
                        name="date"
                        max="<?php echo date('Y-m-d'); ?>"
                        required
                        value="<?php echo escapeHtml($answers['date']); ?>"
                        <?php
                        if (isset($errors['date'])) {
                            echo 'aria-invalid="true" aria-describedby="error-date"';
                        }
                        ?>
                    >

                    <?php
                    if (isset($errors['date'])) {
                        echo '<p class="error" id="error-date">';
                        echo escapeHtml($errors['date']);
                        echo '</p>';
                    }
                    ?>
                </div>

                <section class="col-span-full">
                    <h2>About You</h2>
                </section>

                <?php
                $textQuestions = [
                    'name' => 'What is your name?',
                    'game' => 'What is your favourite game?',
                    'developer' => 'Who is your favourite developer?',
                    'character' => 'Who is your favourite game character?',
                    'first_game' => 'What was your first game?',
                    'franchise' => 'What is your favourite franchise?'
                ];

                foreach ($textQuestions as $key => $question) {
                ?>

                <div class="col-span-full m-col-span-6">
                    <label for="<?php echo $key; ?>">
                        <?php echo escapeHtml($question); ?>
                    </label>

                    <input
                        type="text"
                        id="<?php echo $key; ?>"
                        name="<?php echo $key; ?>"
                        minlength="2"
                        maxlength="100"
                        required
                        value="<?php echo escapeHtml($answers[$key]); ?>"
                        <?php
                        if (isset($errors[$key])) {
                            echo 'aria-invalid="true"';
                            echo ' aria-describedby="error-' . $key . '"';
                        }
                        ?>
                    >

                    <?php
                    if (isset($errors[$key])) {
                        echo '<p class="error" id="error-' . $key . '">';
                        echo escapeHtml($errors[$key]);
                        echo '</p>';
                    }
                    ?>
                </div>

                <?php
                }
                ?>

                <section class="col-span-full">
                    <h2>Gaming Preferences</h2>
                    <p>Select all that apply.</p>
                </section>

                <?php
                $checkboxQuestions = [
                    'genres' => [
                        'question' => 'What genres do you enjoy?',
                        'options' => $genres
                    ],
                    'platforms' => [
                        'question' => 'What platforms do you play on?',
                        'options' => $platforms
                    ],
                    'features' => [
                        'question' => 'What game features do you enjoy?',
                        'options' => $features
                    ]
                ];

                foreach ($checkboxQuestions as $key => $data) {
                ?>

                <fieldset
                    class="col-span-full"
                    <?php
                    if (isset($errors[$key])) {
                        echo 'aria-describedby="error-' . $key . '"';
                    }
                    ?>
                >
                    <legend>
                        <?php echo escapeHtml($data['question']); ?>
                    </legend>

                    <?php
                    foreach ($data['options'] as $option) {
                    ?>

                    <div>
                        <label>
                            <input
                                type="checkbox"
                                name="<?php echo $key; ?>[]"
                                value="<?php echo escapeHtml($option); ?>"
                                <?php
                                if (in_array($option, $answers[$key], true)) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <?php echo escapeHtml($option); ?>
                        </label>
                    </div>

                    <?php
                    }
                    ?>

                    <?php
                    if (isset($errors[$key])) {
                        echo '<p class="error" id="error-' . $key . '">';
                        echo escapeHtml($errors[$key]);
                        echo '</p>';
                    }
                    ?>
                </fieldset>

                <?php
                }
                ?>

                <section class="col-span-full">
                    <h2>Gaming Experiences</h2>
                </section>

                <?php
                $longQuestions = [
                    'reason' => 'Why do you enjoy video games?',
                    'memory' => 'What is your favourite gaming memory?'
                ];

                foreach ($longQuestions as $key => $question) {
                ?>

                <div class="col-span-full">
                    <label for="<?php echo $key; ?>">
                        <?php echo escapeHtml($question); ?>
                    </label>

                    <textarea
                        id="<?php echo $key; ?>"
                        name="<?php echo $key; ?>"
                        rows="5"
                        minlength="10"
                        maxlength="1000"
                        required
                        <?php
                        if (isset($errors[$key])) {
                            echo 'aria-invalid="true"';
                            echo ' aria-describedby="error-' . $key . '"';
                        }
                        ?>
                    ><?php echo escapeHtml($answers[$key]); ?></textarea>

                    <?php
                    if (isset($errors[$key])) {
                        echo '<p class="error" id="error-' . $key . '">';
                        echo escapeHtml($errors[$key]);
                        echo '</p>';
                    }
                    ?>
                </div>

                <?php
                }
                ?>

                <section class="col-span-full">
                    <h2>Gaming Habits</h2>
                    <p>Select one answer for each question.</p>
                </section>

                <?php
                $radioQuestions = [
                    'frequency' => [
                        'question' => 'How often do you play?',
                        'options' => $frequencies
                    ],
                    'mode' => [
                        'question' => 'What game mode do you prefer?',
                        'options' => $modes
                    ],
                    'purchase' => [
                        'question' => 'How do you buy games?',
                        'options' => $purchases
                    ],
                    'difficulty' => [
                        'question' => 'What difficulty do you prefer?',
                        'options' => $difficulties
                    ]
                ];

                foreach ($radioQuestions as $key => $data) {
                ?>

                <fieldset
                    class="col-span-full m-col-span-6"
                    <?php
                    if (isset($errors[$key])) {
                        echo 'aria-describedby="error-' . $key . '"';
                    }
                    ?>
                >
                    <legend>
                        <?php echo escapeHtml($data['question']); ?>
                    </legend>

                    <?php
                    foreach ($data['options'] as $option) {
                    ?>

                    <div>
                        <label>
                            <input
                                type="radio"
                                name="<?php echo $key; ?>"
                                value="<?php echo escapeHtml($option); ?>"
                                <?php
                                if ($answers[$key] === $option) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <?php echo escapeHtml($option); ?>
                        </label>
                    </div>

                    <?php
                    }
                    ?>

                    <?php
                    if (isset($errors[$key])) {
                        echo '<p class="error" id="error-' . $key . '">';
                        echo escapeHtml($errors[$key]);
                        echo '</p>';
                    }
                    ?>
                </fieldset>

                <?php
                }
                ?>

                <?php
                if (isset($errors['form'])) {
                    echo '<div class="col-span-full" role="alert">';
                    echo '<p class="error">';
                    echo escapeHtml($errors['form']);
                    echo '</p>';
                    echo '</div>';
                }
                ?>

                <div class="col-span-full">
                    <button type="submit">Submit Survey</button>
                </div>

            </div>
        </form>

        <?php
        }
        ?>

    </div>
</main>

<footer class="grid-con center-grid-con">
    <div class="col-span-full">
        <p>Video Game Survey</p>
    </div>
</footer>

</body>
</html>
