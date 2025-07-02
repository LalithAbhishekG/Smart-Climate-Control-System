<?php
 include('dbconn.php'); 
 $query = "SELECT * FROM data";
 $results = mysqli_query($conn,$query);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sensor Data Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
  </head>
    <body class="bg-gradient-to-b from-blue-50 to-white min-h-screen font-sans text-gray-800">
    <div class="container mx-auto py-10 px-4">
      <h1 class="text-4xl font-bold text-center text-blue-900 mb-12 tracking-wide"> Sensor Monitoring Dashboard</h1>

      <!-- Graph Section -->
      <section class="mb-12">
        <div class="bg-white rounded-xl shadow-xl p-6">
          <h2 class="text-2xl font-semibold text-blue-700 mb-6 border-b pb-2">Real-time Graphs</h2>
          <div class="flex flex-col lg:flex-row gap-6 items-center justify-center">
            <div class="w-full max-w-xl">
              <h3 class="text-lg font-medium text-gray-600 text-center mb-2">Temperature Over Time</h3>
              <canvas id="temperatureChart"></canvas>
            </div>
            <div class="w-full max-w-xl">
              <h3 class="text-lg font-medium text-gray-600 text-center mb-2">Humidity Over Time</h3>
              <canvas id="HumidityChart"></canvas>
            </div>
          </div>
        </div>
      </section>

      <!-- Table Section -->
      <section>
        <div class="bg-white rounded-xl shadow-xl p-6">
          <h2 class="text-2xl font-semibold text-blue-700 mb-6 border-b pb-2"> Tabular Sensor Data</h2>
          <div class="overflow-x-auto">
            <table id="data-table" class="display w-full text-sm text-left">
              <thead>
                <tr class="bg-blue-100 text-gray-800">
                  <th class="px-4 py-2">Index</th>
                  <th class="px-4 py-2">Humidity</th>
                  <th class="px-4 py-2">Temperature</th>
                  <th class="px-4 py-2">Datetime</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($results as $i => $result){ ?>
                <tr>
                  <td><?=$i+1;?></td>
                  <td><?=$result['Humidity']?></td>
                  <td><?=$result['Temperatue']?></td>
                  <td><?=$result['datetime']?></td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      $(document).ready(function() {
        $('#example').DataTable();
        
        // Fetch data for the chart
        var temperatures = [],Humidities = [];
        var dates = [];
        <?php foreach($results as $result){ ?>
          temperatures.push(<?=$result['Temperatue']?>);
          Humidities.push(<?=$result['Humidity']?>);
          dates.push('<?=$result['datetime']?>');
        <?php } ?>

        // Create chart
        var ctx = document.getElementById('temperatureChart').getContext('2d');
        var chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: dates,
            datasets: [{
              label: 'Temperature',
              data: temperatures,
              borderColor: 'rgb(75, 192, 192)',
              tension: 0.1
            }]
          }
        });
        var ctx = document.getElementById('HumidityChart').getContext('2d');
        var chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: dates,
            datasets: [{
              label: 'Humidity',
              data: Humidities,
              borderColor: 'rgb(75, 192, 192)',
              tension: 0.1
            }]
          }
        });
      });
    </script>
  </body>
</html>
