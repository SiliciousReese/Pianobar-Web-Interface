<!doctype html>

<html>

<head>
    <title>Pianobar Web Interface</title>
    <link rel="stylesheet" href="/styles.css">
    <meta name="author" content="Daniel Darnell">
</head>

<body>

<div class="container">

<header>
    <h1>Daniel's Pianobar Web Interface</h1>
</header>


<?php
$TESTING = True;
$notStarted = False;

/* Test if pianobar is running and start it if it is not. 1 is the exit status
 * of grep if it fails to match "pianobar". */
/* TODO Find out if there is a built in php function for searching for active 
 * processes, or use a lock file when opening pandora. */
exec("ps -A | grep pianobar", $out, $pianobar_active);
if($pianobar_active == 1 && !$TESTING) {
    $notStarted = True;
    echo "Starting pianobar...<br>";
    exec("pianobar > /var/www/pianobar-out &");
}

/* Check if the user submitted an action. */
$action = "";
if (($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"]) ||  $notStarted) && !$TESTING) {
    if ($notStarted) {
        $action = "s";
    } else {
        $action = $_POST["action"];
    }
    $fifo_name = "/var/www/pianobar-ctl";
    $fifo = fopen($fifo_name, 'w');
    if($fifo) {
        fwrite($fifo, $action);
        if($action == 's' || $notStarted) {
            if ($notStarted) {
                $station_number = "";
            } else {
                $station_number = $_POST["station"];
            }
            fwrite($fifo, "$station_number\n");
        }
        fclose($fifo);
    }
}
?>

<form id="pianobar-action-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <input type="radio" name="action" value=")" id="up-volume">
    <label for="up-volume">Increase Volume</label><br>

    <input type="radio" name="action" value="(" id="down-volume">
    <label for="down-volume">Decrease Volume</label><br>

    <input type="radio" name="action" value="+" id="like">
    <label for="like">Like Song</label><br>

    <input type="radio" name="action" value="n" id="next">
    <label for="next">Next song</label><br>

    <input type="radio" name="action" value="p" id="pause">
    <label for="pause">Pause</label><br>

    <input type="radio" name="action" value="q" id="quit">
    <label for="quit">Quit Pianobar</label><br>

    <input type="radio" name="action" value="s" id="switch">
    <label for="switch">Switch Stations</label><br>
    <input type="number" name="station" id="station" min="0" max="100" value="0">
    <label for="station">Station Number</label><br>

    <input type="submit" name="submit">
</form>

<?php
$pianobar_out_file = "/var/www/pianobar-out";
$output = file_get_contents($pianobar_out_file);

/* Pianobar uses hard to parse formatting, including ANSI terminal escape
 * sequences. Here is my attempt to get something useful out of it. */

/* The station list pops up when the user tries to switch stations. Each
 * station on the list starts with a one or two digit number, then a space then
 * some information about the station encoded in letters. The letter after the
 * first space is either a capital or lowercase 'q' or a space. That is enough
 * information to seperate the station numbers from the rest of the numbers in
 * the output. */
echo "<div id=\"pianobar-station-history\"><h2>Station List</h2><p>";
preg_match_all("/[0-9]{1,2}\) [qQ ].*/", $output, $matches);
foreach ($matches[0] as $match) {
    echo "$match<br>";
}
echo "</p></div>";

/* Display list of played songs. Pianobar uses the format "songname" by
 * "artist" on "album". */
echo "<div id=\"pianobar-song-history\"><h2>Songs Played</h2><p>";
preg_match_all("/\".*\" by \".*\" on \".*\"/", $output, $matches);
foreach ($matches[0] as $match) {
    echo "$match<br>";
}
echo "</p></div>";

?>

</div>
</body>
</html>
