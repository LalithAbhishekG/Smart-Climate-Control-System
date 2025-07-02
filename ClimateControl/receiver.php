<?php

// Include the database connection file
include 'dbconn.php';

// Check if the Temp and Humidity parameters are set in the GET request
if (isset($_GET['Temp']) && isset($_GET['Humidity'])) {
    // Retrieve the values of Temp and Humidity parameters from the GET request
    $temp = $_GET['Temp'];
    $humidity = $_GET['Humidity'];

    // Insert the received values into the data table
    $query = "INSERT INTO data (Temperature, Humidity) VALUES ('$temp', '$humidity')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "Data inserted successfully.";
    } else {
        echo "Error inserting data: " . mysqli_error($conn);
    }
} else {
    // If Temp and/or Humidity parameters are not set, return an error message
    echo "Error: Temperature and/or Humidity parameters are missing.";
}

// Close the database connection
mysqli_close($conn);

?>
