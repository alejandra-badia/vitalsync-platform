<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VitalSync Platform | Interoperability & Oversight</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body class="bg-app">
    <nav class="top-nav">
        <div class="header-container">
            <div class="logo">
                <div>Vital<span class="vital">Sync </span></div>
                <div><span class="subtitle"><span class="hidden-small"> | </span>Interoperability & Oversight <span class="mobile-hidden">Platform</span></span></div>
            </div>
            <div class="user-profile">
                <span class="pill pill-info">No Auth Required - Demo</span>
                <div class="system-health">
                    <span class="med-hidden"> | <span class="mobile-hidden">Connectivity Status: </span></span>
                    <span class="health-item">
                        <span class="status-dot <?= !empty($dbOnline) ? 'status-online' : 'status-offline' ?>"></span>
                        DB
                    </span>

                    <span class="health-item">
                        <span class="status-dot <?= !empty($apiOnline) ? 'status-online' : 'status-offline' ?>"></span>
                        API
                    </span>

                    <span class="health-item">
                        <span class="status-dot <?= !empty($apiOnline) ? 'status-online' : 'status-offline' ?>"></span>
                        FHIR
                    </span>

                    <span class="health-item">
                        <span class="status-dot status-online"></span>
                        HL7 Bridge
                    </span>
                </div>
            </div>
        </div>
    </nav>
