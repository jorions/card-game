<!DOCTYPE html>
<html>
<head>
    <title>Card Game</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="images/favicon.ico" type="image/ico" />
</head>
<body>

<?php

require('cards.php');

// Start session variables
session_start();

// If reset button is pressed reset all session variables
if(isset($_POST['reset'])) {
    session_destroy();
    session_start();
}

// If the session variables haven't been declared, give starting values
if(!isset($_SESSION['addingNames'])) {
    $_SESSION['addingNames'] = 'TRUE';
    $_SESSION['settingNumCards'] = 'FALSE';
    $_SESSION['allPlayers'] = array();
}

// If the addPlayer button is pressed add a new player to the allPlayers array
if(isset($_POST['addPlayer'])) {
    // If the player name is empty tell the user
    if($_POST['newPlayer'] == "") {
        echo "<div class='headline-on-table'>Please enter a name</div>";
    } else {
        $_SESSION['allPlayers'][] = new Player($_POST['newPlayer']);
    }
}

// If continue button is pressed set session variables for logic to not show the first part of the form
if(isset($_POST['continueToSetNum'])) {
    // If there are less than 2 players tell the user
    if(count($_SESSION['allPlayers']) < 2) {
        echo "<div class='headline-off-table'>You must have at least 2 players for this game!</div>";
    } else {
        $_SESSION['addingNames'] = 'FALSE';
        $_SESSION['settingNumCards'] = 'TRUE';
    }
}

// If the numCards entered isn't a number or is < 1 tell the user
if(isset($_POST['numCards'])) {
    if(!is_numeric($_POST['numCards'])) {
        echo "<div class='headline-off-table'>Please enter a valid number</div>";
    } else if ($_POST['numCards'] < 1) {
        echo "<div class='headline-off-table'>Please enter 1 or more</div>";
    } else {
        $_SESSION['settingNumCards'] = 'FALSE';
    }
}
?>
<div class="table">
    <div class="inner-shadow">
        <form action="<?php echo($_SERVER['PHP_SELF']);?>" method="POST">
            <button class="button reset" type="submit" name="reset">RESET</button>
        </form>
        <div>
            <div class="divider"></div>
        </div>
            <?php
            // If user is still adding names show first part of form
            if($_SESSION['addingNames'] == 'TRUE') { ?>
                <form action="<?php echo($_SERVER['PHP_SELF']);?>" method="POST">
                    <br />
                    <br />
                    <input type="text" name="newPlayer" placeholder="Player Name" />
                    <button class="button add" type="submit" name="addPlayer">+ Player</button><br />
                    <button class="button continue" type="submit" name="continueToSetNum">Continue</button>
                    <?php
                    //Show array of players
                    $i = 1;
                    if(count($_SESSION['allPlayers']) >= 1) {
                        echo "<br><br>";
                        foreach($_SESSION['allPlayers'] as $player) {
                            echo "<div class='headline-on-table'>Player $i - $player->name</div>";
                            $i ++;
                        }
                    } ?>
            </form>

            <?php
            // If the continue button is pressed and the user is no longer adding names then show next part of form
            } else if ($_SESSION['settingNumCards'] == 'TRUE') { ?>
                <form action="<?php echo($_SERVER['PHP_SELF']);?>" method="POST">
                    <input type="text" name="numCards" required placeholder="# Cards Per Player" />
                    <button class="button continue" type="submit" name="setNum">Continue</button><br />
                </form>
                <?php

            // Now play the game!
            } else {

    // Instantiate the deck (this instantiates an array of card objects, and the card constructor can throw an exception, so wrap it in a try/catch)
    try {
        $deck = new Deck();
    } catch (Exception $e) {
        echo 'An error occurred: ' . $e->getMessage();
    }

    // Instantiate the dealer with the array of players, the number of cards to deal them, and the deck of cards
    $dealer = new Dealer($_SESSION['allPlayers'], $_POST['numCards'], $deck);

    // Make sure no additional decks are needed
    echo $dealer->setupGame();

    // Show the deck face down
    echo "<div class='headline-on-table'><b>The Deck</b></div>";
    echo $dealer->showDeck();
    echo "<div class='divider'></div>";

    // Deal the cards to all players
    $dealer->deals();

    // Show the players' hands
    echo "<div class='headline-on-table'>Player's Hands</div>";
    foreach ($_SESSION['allPlayers'] as $player) {
        echo $player->showHand();
    }
    echo "<div class='divider'></div>";

    // Show the players' scores
    echo "<div class='headline-on-table'><b>Score</b></div>";
    $dealer->scoreGame();
    echo $dealer->showScores();
    echo "<div class='divider'></div>";

    // Determine and display a winner
    echo "<div class='headline-on-table'><b>And The Winner Is...</b></div>";
    $dealer->determineWinners();
    echo $dealer->showWinners();
    echo "</div></div>";
}