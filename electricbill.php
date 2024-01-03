<?php
session_start();

function calculateElectricityCharge($voltage, $current, $hour, $currentRate) {
    // Calculate Power in Watt-hours
    $power = $voltage * $current;

    // Calculate Energy in Kilowatt-hours
    $energy = ($power * $hour) / 1000;

    // Calculate Total Charge
    $totalCharge = $energy * ($currentRate / 100);

    return array(
        'power' => $power,
        'energy' => $energy,
        'totalCharge' => $totalCharge
    );
}

// Check if form for calculation is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculate'])) {
    $voltage = $_POST['voltage'];
    $current = $_POST['current'];
    $hour = $_POST['hour'];
    $currentRate = $_POST['currentRate'];

    // Validate input (You may want to add more validation)
    if (is_numeric($voltage) && is_numeric($current) && is_numeric($hour) && is_numeric($currentRate)) {
        $result = calculateElectricityCharge($voltage, $current, $hour, $currentRate);

        // Store the result in the session array
        $_SESSION['results'][] = array(
            'hour' => $hour,
            'power' => $result['power'],
            'energy' => $result['energy'],
            'totalCharge' => $result['totalCharge']
        );
    } else {
        $error = "Invalid input. Please enter numeric values.";
    }
}

// Check if form for clearing results is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clearResults'])) {
    // Clear the results stored in the session
    unset($_SESSION['results']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Electricity Calculator</title>
</head>
<body>

<div class="container mt-5">
    <h2>Electricity Calculator</h2>

    <!-- Form for calculation -->
    <form method="post">
        <div class="form-group">
            <label for="voltage">Voltage (V):</label>
            <input type="text" class="form-control" name="voltage" required>
        </div>
        <div class="form-group">
            <label for="current">Current (A):</label>
            <input type="text" class="form-control" name="current" required>
        </div>
        <div class="form-group">
            <label for="hour">Hour:</label>
            <input type="text" class="form-control" name="hour" required>
        </div>
        <div class="form-group">
            <label for="currentRate">Current Rate (sen/kWh):</label>
            <input type="text" class="form-control" name="currentRate" required>
        </div>
        <button type="submit" class="btn btn-primary" name="calculate">Calculate</button>
    </form>

    <!-- Form for clearing results -->
    <form method="post">
        <!-- Clear Results Button -->
        <button type="submit" class="btn btn-danger" name="clearResults">Clear Results</button>
    </form>

    <?php if (isset($result)): ?>
        <div class="mt-4">
            <h3>Individual Result</h3>
            <p>Power: <?php echo $result['power']; ?> Watt-hours</p>
            <p>Energy: <?php echo $result['energy']; ?> kWh</p>
            <p>Total Charge: RM <?php echo number_format(round($result['totalCharge'], 2), 2); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="mt-4 alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['results'])): ?>
        <div class="mt-4">
            <h3>Cumulative Results</h3>

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hour</th>
                        <th>Power (Watt-hours)</th>
                        <th>Energy (kWh)</th>
                        <th>Total Charge (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['results'] as $key => $result): ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $result['hour']; ?></td>
                            <td><?php echo $result['power']; ?></td>
                            <td><?php echo $result['energy']; ?></td>
                            <td><?php echo number_format(round($result['totalCharge'], 2), 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Code injected by live-server -->
<script>
    // This script handles live-reloading when changes are made
    if ('WebSocket' in window) {
        (function () {
            // Function to refresh CSS
            function refreshCSS() {
                var sheets = [].slice.call(document.getElementsByTagName("link"));
                var head = document.getElementsByTagName("head")[0];
                for (var i = 0; sheets && i < sheets.length; ++i) {
                    var elem = sheets[i];
                    var parent = elem.parentElement || head;
                    parent.removeChild(elem);
                    var rel = elem.rel;
                    if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
                        var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
                        elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
                    }
                    parent.appendChild(elem);
                }
            }

            // Set up WebSocket for live-reloading
            var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
            var address = protocol + window.location.host + window.location.pathname + '/ws';
            var socket = new WebSocket(address);
            socket.onmessage = function (msg) {
                if (msg.data == 'reload') window.location.reload();
                else if (msg.data == 'refreshcss') refreshCSS();
            };

            // Check if this is the first time log from live-server
            if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
                console.log('Live reload enabled.');
                sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
            }
        })();
    } else {
        console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
    }
</script>
</body>
</html>
