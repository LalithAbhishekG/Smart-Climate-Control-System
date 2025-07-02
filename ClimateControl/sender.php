<?php

// Include the database connection file
include 'dbconn.php';

// Query to select all data from the table
$query = "SELECT * FROM data";

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query executed successfully
if (!$result) {
    // If query failed, return an error message
    $response = array('status' => 'error', 'message' => mysqli_error($conn));
} else {
    // If query succeeded, fetch the data and build the response array
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        // Access data from each row
        $data[] = array(
            'Temperature' => $row['Temperature'],
            'Humidity' => $row['Humidity'],
            'Datetime' => $row['datetime']
        );
    }
    $response = array('status' => 'success', 'data' => $data);
}

// Close the database connection
mysqli_close($conn);

// Encode the response array to JSON format and return it
echo json_encode($response);
?>
