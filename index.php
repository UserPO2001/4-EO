<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Welkom bij Slijterij</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        #age-verification {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); Semi-transparent background
            z-index: 9999;
            text-align: center;
            padding-top: 20%;
        }
        .verification-box {
            background-color: white;
            padding: 20px;
            width: 300px;
            margin: 0 auto;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let ageVerified = sessionStorage.getItem('ageVerified');

            if (ageVerified !== 'true') {
                document.getElementById('age-verification').style.display = 'block';
            } else {
                // Age verified, show main content
                document.getElementById('main-content').style.display = 'block';
            }
        });

        function verifyAge(answer) {
            if (answer === 'ja') {
                sessionStorage.setItem('ageVerified', 'true');
                document.getElementById('age-verification').style.display = 'none';
                document.getElementById('main-content').style.display = 'block';
            } else if (answer === 'nee') {
                alert('U moet ouder zijn dan 18 jaar om toegang te krijgen tot deze site.');
                // Optionally, redirect to another page or take appropriate action
            }
        }
    </script>
</head>
<body>
    <div id="main-content">
        <h1>Welkom bij Slijterij Stuk in m'n Kraag</h1>
        <p><a href="register.php">Registreren</a> | <a href="login.php">Inloggen</a></p>
    </div>
    
    <div id="age-verification">
        <div class="verification-box">
            <h2>Bent u ouder dan 18?</h2>
            <button onclick="verifyAge('ja')">Ja</button>
            <button onclick="verifyAge('nee')">Nee</button>
        </div>
    </div>
</body>
</html>
