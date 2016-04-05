<!DOCTYPE html>
<html>
<head>
    <title>Card Game</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<?php
/**
 * Class Card represents and encapsulates data and functionality for a playing card
 */
class Card {
    /**
     * The suit of the card
     * @var string
     */
    protected $suit;
    /**
     * The rank of the car
     * @var int
     */
    protected $rank;
    /**
     * The color of the card (red or black)
     * @var string
     */
    protected $color;
    /**
     * The HTML format of the card suit
     * @var string
     */
    protected $icon;
    /**
     * The final value of the card (A, J, Q, K, or 1-10)
     * @var string or int
     */
    protected $finalRank;
    /**
     * The various permitted suits for the cards
     * @var array
     */
    protected $allowedSuits = array("Heart", "Diamond", "Spade", "Club");
    /**
     * @param string $suit
     * @param int $rank
     * @throws Exception
     */
    public function __construct($suit, $rank) {
        $this->suit = $suit;
        $this->rank = $rank;
        // NOTE: These functions must go after the property definitions otherwise the function won't know what "suit" or "card" is
        // Make sure the suit is valid
        $this->checkSuit();
        // Assign the card a color based on suit
        $this->colorCard();
        // Create the HTML format of the card suit
        $this->createIcon();
        // Set the final rank for the card (1-10, A, J, Q, K)
        $this->setRank();
    }
    /**
     * Check to see if the suit is valid
     * @throws Exception
     * @return void
     */
    protected function checkSuit() {
        // If the suit of the given card is not listed in the array of legitimate suits then throw an exception
        if(!in_array($this->suit, $this->allowedSuits)) {
            throw new Exception($this->suit . ' is not allowed! You can pass: ' . implode(', ', $this->allowedSuits));
        }
    }
    /**
     * Set the color of the card
     * @return void
     */
    protected function colorCard() {
        // If the card is a diamond or heart, color it red. Otherwise, color it black
        if($this->suit == 'Diamond' || $this->suit == 'Heart') {
            $this->color = 'red';
        } else {
            $this->color = 'black';
        }
    }
    /**
     * Create an icon for the card
     * @return void
     */
    protected function createIcon() {
        // Assign html format icon for card based on its suit
        switch($this->suit) {
            case "Heart":
                $this->icon = "&hearts;";
                break;
            case "Diamond":
                $this->icon = "&diams;";
                break;
            case "Spade":
                $this->icon = "&spades;";
                break;
            case "Club":
                $this->icon = "&clubs;";
        }
    }
    /**
     * Set the face value for the card
     * @return void
     */
    public function setRank() {
        // Assign the face value for the card based on its rank
        switch($this->rank) {
            case 0:
                $this->finalRank = "A";
                break;
            case 11:
                $this->finalRank = "J";
                break;
            case 12:
                $this->finalRank = "Q";
                break;
            case 13:
                $this->finalRank = "K";
                break;
            default:
                $this->finalRank = $this->rank;
                break;
        }
    }
    /**
     * Return the rank of the card
     * @return int
     */
    public function getRank() {
        return $this->rank;
    }
    /**
     * Return the final card with color, suite, and rank
     * @return string
     */
    public function render() {
        // Return the div structure for creating a playing card
        return "<div class='card-$this->color'>
            <div class='cardValueTopLeft'>$this->finalRank$this->icon</div>
            <div class='cardValueTopRight'>$this->finalRank$this->icon</div>
            <div class='cardSuitMiddle'>$this->icon</div>
            <div class='cardValueBottomLeft'>$this->finalRank$this->icon</div>
            <div class='cardValueBottomRight'>$this->finalRank$this->icon</div>
        </div>";
    }
}
class Deck {
    /**
     * The array of the deck of cards
     * @var array
     */
    public $deck = array();
    /**
     * The card object returned from the $deck array
     * @var Card
     */
    public $returnedCard;
    /**
     * The various permitted suits for the cards
     * @var array
     */
    protected $allowedSuits = array("Heart", "Diamond", "Spade", "Club");
    /**
     * Create the deck using the permitted suits
     */
    public function __construct() {
        // Iterate through the allowed suits and automatically build a deck
        foreach($this->allowedSuits as $suit) {
            for($i=0; $i<14; $i++) {
                $this->deck[] = new Card($suit, $i);
            }
        }
    }
    /**
     * Shuffles the $deck
     * @return void
     */
    public function shuffle() {
        shuffle($this->deck);
    }
    /**
     * Returns the last card from the deck and removes that card from the $deck array
     * @return Card
     */
    public function getCard() {
        $this->returnedCard = array_pop($this->deck);
        return $this->returnedCard;
    }
}
class Player {
    /**
     * The player's name
     * @var string
     */
    public $name;
    /**
     * The player's hand - an array of card objects
     * @var array
     */
    public $hand = array();
    /**
     * A string containing every card in the players hand in an HTML-ready format
     * @var string
     */
    public $handRender = "";
    /**
     * The player's score
     * @var int
     */
    public $score = 0;
    /**
     * Create a player with a name and empty hand of cards
     * @param string $name The player's name
     */
    public function __construct($name) {
        $this->name = $name;
    }
    /**
     * Add a card to the player's hand
     * @param Card $card A card object the player receives to put in their hand
     * @return void
     */
    public function receiveCard(Card $card) {
        $this->hand[] = $card;
    }
    /**
     * Combine all of the player's cards into 1 string that is HTML-ready
     * @return string
     */
    public function showHand() {
        // Start by adding the HTML-formatted player's name to the output string
        $this->handRender = "<div class='hand'><div class='name'><b>$this->name's Cards</b></div>";
        // Iterate through the player's hand and add the HTML-formatted string representation of the card to the output string
        foreach($this->hand as $card) {
            $this->handRender .= $card->render();
        }
        $this->handRender .= "</div>";
        return $this->handRender;
    }
}
class Dealer {
    /**
     * An array of all of the players in the game
     * @var array
     */
    public $players = array();
    /**
     * The number of cards that each player should be dealt
     * @var int
     */
    public $numCards;
    /**
     * A string containing every card in the deck in an HTML-ready format
     * @var string
     */
    public $deckRender = "";
    /**
     * A string containing all player's scores in an HTML-ready format
     * @var string
     */
    public $scoreRender = "";
    /**
     * The array of all winners of the game
     * @var array
     */
    public $winnerArray = array();
    /**
     * The highest score of any of the players
     * @var int
     */
    public $highScore = 0;
    /**
     * The deck of cards that the dealer will handle
     * @var Deck
     */
    public $deck;
    /**
     * Create a dealer who knows all of the players, how many cards to deal them, and has a deck to deal
     * @param array $allPlayers An array of all of the players
     * @param int $num The number of cards to deal each player
     * @param Deck $deck The deck of cards to utilize
     */
    public function __construct($allPlayers, $num, Deck $deck) {
        $this->players = $allPlayers;
        $this->numCards = $num;
        $this->deck = $deck;
    }
    /**
     * Add more decks to the hand if needed, and alert the player of the change via a returned HTML-formatted string
     * @return string
     */
    public function setupGame() {
        // If there are not enough cards based on the number of players and the number of cards they need to be dealt, add more decks
        if (count($this->players) > count($this->deck->deck) / $this->numCards) {
            // Initialize variables to count how many more decks are needed
            $extraCardsNeeded = (count($this->players) * $this->numCards) - count($this->deck->deck);
            $extraDecksNeeded = ceil($extraCardsNeeded / count($this->deck->deck));
            // Initialize variables to hold new decks to be added
            $extraCards = array();
            // Create additional decks to add
            for ($i = 0; $i < $extraDecksNeeded; $i++) {
                // Loop through deck and build additional cards array
                foreach ($this->deck->deck as $card) {
                    $extraCards[] = $card;
                }
            }
            // Add new decks to original deck
            $this->deck->deck = array_merge($this->deck->deck, $extraCards);
            // Tell users what happened
            if ($extraDecksNeeded == 1) {
                return "<div class='headline-on-table'>There weren't enough cards so we have added 1 more deck</div>";
            } else {
                return "<div class='headline-off-table'>There weren't enough cards so we have added  $extraDecksNeeded more decks</div>";
            }
        }
    }
    /**
     * Shuffle the deck and give each player a set number of cards
     * @return void
     */
    public function deals() {
        // Shuffle the deck
        $this->deck->shuffle();
        // Iterate through the array of players
        foreach ($this->players as $player) {
            //  Give each player the set number of cards
            for ($i = 0; $i < $this->numCards; $i++) {
                $player->receiveCard($this->deck->getCard());
            }
        }
    }
    /**
     * Iterate through each player and score their hand of cards
     * @return void
     */
    public function scoreGame() {
        // Iterate through the array of players
        foreach($this->players as $player) {
            // Iterate through the player's hand and add each card to that player's score
            foreach($player->hand as $card) {
                $player->score += $card->getRank();
            }
        }
    }
    /**
     * Show the deck of cards face down
     * @return string
     */
    public function showDeck() {
        // Prepare the HTML formatting for the deck of cards
        $this->deckRender = "<div class='hand'>";
        // Iterate through the deck and add an HTML-formatted string representation of the card image to the output string
        for($i=0; $i<count($this->deck->deck); $i++) {
            $this->deckRender .= "<div class='card-black'><img src='images/card.png' /></div>";
        }
        // Close the HTML formatting div
        $this->deckRender .= '</div>';
        return $this->deckRender;
    }
    /**
     * Show the scores of all players as an HTML-formatted string
     * @return string
     */
    public function showScores() {
        // Iterate through the array of players
        foreach($this->players as $player) {
            // Add the HTML-formatted player's score to the output string
            $this->scoreRender .= "<div class='hand'><div class='name'><b>$player->name's Score</b></div>
            <div class='score'>$player->score</div></div>";
        }
        return $this->scoreRender;
    }
    /**
     * Determine the winning players and the high score
     * @return array
     */
    public function determineWinners() {
        // Iterate through the array of players
        foreach($this->players as $player) {
            // If the player's score is higher than the high score
            if($player->score > $this->highScore) {
                // Set the high score
                $this->highScore = $player->score;
                // Reset the array with the player as the only value
                $this->winnerArray = array();
                $this->winnerArray[] = $player->name;
                // Else if the player's score ties the high score
            } else if($player->score == $this->highScore) {
                // Add the player to the array
                $this->winnerArray[] = $player->name;
            }
        }
        return $this->winnerArray;
    }
    /**
     * Show the winner(s) of the match in an HTML-formatted string
     * @return string
     */
    public function showWinners() {
        // If there is only 1 winner return their name with HTML formatting
        if(count($this->winnerArray) == 1) {
            return "<div class='score'>" . $this->winnerArray[0] . '! With a score of ' . $this->highScore . '!</div>';
            // Else if there are 2 winners return both of their names with HTML formatting
        } else if(count($this->winnerArray) == 2) {
            return "<div class='score'>Both " . $this->winnerArray[0] . ' and ' . $this->winnerArray[1] . ' tied with a score of ' . $this->highScore . '!</div>';
            // Else if there are 3 or more winners return all of their names in a list with HTML formatting
        } else if(count($this->winnerArray) >= 3) {
            // Set a temporary string to hold all winners names
            $tempString = "";
            // Iterate through the array of winning players and add them to the return string
            foreach($this->winnerArray as $winner) {
                $tempString .= $winner . ", ";
            }
            // Remove the last space and comma in the string
            $tempString = substr($tempString, 0, -2);
            return "<div class='score'>" . $tempString . ' all won with a score of ' . $this->highScore . '!</div>';
        }
    }
}

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