<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Statistiques</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(14, 11, 11);
            color: white;
            height: 100vh;
            overflow: hidden;
        }

        /* Navbar */
        .navbar {
            background-color: rgb(44, 25, 3);
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            text-decoration: none;
            color: white;
            margin: 0 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .navbar a:hover {
            background-color: rgb(209, 142, 115);
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(245, 165, 90, 0.5);
        }

        .navbar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .navbar .etu {
            background-color: rgb(87, 70, 64);
        }

        .main-content {
            margin: 20px;
            padding: 20px;
            height: 100%;
            overflow-y: auto;
        }

        .graph-container {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
            padding: 5px;
        }

        canvas {
            flex: 1 1 auto;
            max-width: 300px;
            height: 500px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="gestionEtu.php"> <i class="fas fa-user-graduate"></i> Étudiants </a>
        <a href="gestionVer.php"><i class="fas fa-money-bill-wave"></i> Versements</a>
        <a href="gestionEnsei.php"><i class="fas fa-chalkboard-teacher"></i> Enseignant</a>
        <a class="etu" href="statistiques.php"> <i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="gestionMati.php"> <i class="fas fa-book"></i> Matières</a>
        <a href="listeNote.php"> <i class="fas fa-graduation-cap"></i> Notes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <div class="main-content">
        <h1>Statistiques</h1>
        <div class="graph-container">
            <div><canvas id="studentsByStatusChart"></canvas></div>
            <div><canvas id="paymentsByDayChart"></canvas></div>
            <div><canvas id="paymentsByMonthChart"></canvas></div>
        </div>
    </div>

    <?php
    // Connexion à la base de données
    $mysqli = new mysqli('localhost', 'root', '', 'etudiants');

    if ($mysqli->connect_error) {
        die("Erreur de connexion : " . $mysqli->connect_error);
    }

    // Étudiants par statut
    $query = "SELECT statut, COUNT(*) as count FROM etudiants GROUP BY statut";
    $result = $mysqli->query($query);

    $statuts = [];
    $counts = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $statuts[] = htmlspecialchars($row['statut']);
            $counts[] = (int)$row['count'];
        }
    }

    echo "<script>
        const studentStatusLabels = " . json_encode($statuts) . ";
        const studentStatusData = " . json_encode($counts) . ";
    </script>";

    // Montants par jour
    $query = "SELECT DATE(date_versement) as day, SUM(montant) as total
              FROM versements
              GROUP BY DATE(date_versement)";
    $result = $mysqli->query($query);

    $days = [];
    $totals = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $days[] = htmlspecialchars($row['day']);
            $totals[] = (float)$row['total'];
        }
    }

    echo "<script>
        const paymentDaysLabels = " . json_encode($days) . ";
        const paymentTotalsData = " . json_encode($totals) . ";
    </script>";

    // Montants par mois
    $query = "SELECT DATE_FORMAT(date_versement, '%Y-%m') as month, SUM(montant) as total
              FROM versements
              GROUP BY DATE_FORMAT(date_versement, '%Y-%m')";
    $result = $mysqli->query($query);

    $months = [];
    $monthlyTotals = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $months[] = htmlspecialchars($row['month']);
            $monthlyTotals[] = (float)$row['total'];
        }
    }

    echo "<script>
        const paymentMonthsLabels = " . json_encode($months) . ";
        const paymentMonthlyTotalsData = " . json_encode($monthlyTotals) . ";
    </script>";

    $mysqli->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        new Chart(document.getElementById('studentsByStatusChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: studentStatusLabels,
                datasets: [{
                    data: studentStatusData,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]
            }
        });

        new Chart(document.getElementById('paymentsByDayChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: paymentDaysLabels,
                datasets: [{
                    label: 'Montants versés par jour',
                    data: paymentTotalsData,
                    backgroundColor: '#4CAF50',
                }]
            }
        });

        new Chart(document.getElementById('paymentsByMonthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: paymentMonthsLabels,
                datasets: [{
                    label: 'Montants versés par mois',
                    data: paymentMonthlyTotalsData,
                    backgroundColor: '#FF5733',
                }]
            }
        });
    </script>
</body>

</html>
