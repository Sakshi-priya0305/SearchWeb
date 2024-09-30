<?php
require_once 'partials/_dbconnect.php';
if (isset($_GET['term'])) {
    $query = $_GET['term'];
    $words = explode(' ', $query);
    $conditions = [];
    foreach ($words as $word) {
        $word = mysqli_real_escape_string($conn_search_app, $word);
        $conditions[] = "suggestion LIKE '%$word%'";
    }
    $conditionString = implode(' OR ', $conditions);
    $sql = "
        SELECT suggestion 
        FROM autocomplete_suggestions 
        WHERE ($conditionString)
        ORDER BY 
            CASE 
                WHEN suggestion LIKE '$query%' THEN 1 
                ELSE 2 
            END 
        LIMIT 5
    ";
    
    $result = mysqli_query($conn_search_app, $sql);
    
    $suggestions = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = $row['suggestion'];
    }
    
    echo json_encode($suggestions);
}



