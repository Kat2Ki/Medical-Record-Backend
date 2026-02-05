<!DOCTYPE html>
<html>
<head>
    <title>Choose Profile</title>
    <link rel="stylesheet" href="choose_profile.css">
</head>
<body>

<header>
    <h1>Choose Your Profile</h1>
    <p>Select whether you are a Patient or a Hospital</p>
</header>

<div class="profile-container">

    <div class="profile-box" onclick="window.location='cep.html'">
        <h2>Patient</h2>
        <p>Login or create your medical records account.</p>
        <a href="cep.html"><button>Open</button></a>
    </div>

    <div class="profile-box" onclick="window.location='hospital_login.php'">
        <h2>Hospital</h2>
        <p>Manage patient medical data securely.</p>
        <a href="hospital_login.php"><button>Open</button></a>
    </div>

</div>

</body>
</html>
