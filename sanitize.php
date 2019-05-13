if (!isset($_POST['data']) {
    echo '';
} else {
    $userInput = $_POST['data'];
    $userInput = filter_var($userInput, FILTER_SANITIZE_STRING);
    echo $userInput;
}