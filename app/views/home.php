<?php if (!isset($viewContent)) {
    header('Location: ../../public/index.php?view=home');
    exit;
} ?>

<main>
    <section class="card-demo">
        <?php    include __DIR__ . '/layout/demo_nav.php'; ?>
    </section>

    <section class="card-section">
        <h2>Overview</h2>
        <div class="nested">
            <p>VitalSync simulates a multi-layer healthcare interoperability environment, 
            demonstrating HL7-style event ingestion, FHIR R4 data enrichment, integration observability, 
            and executive reporting using relational modeling.</p>
        </div>

        <h2>Dashboard Views</h3>
        <div class="nested nested-grid">
            <div class="card nested-card">
                <div>
                    <h3>Clinical View</h3>
                    <p class="nested-meta"><b>Operational Layer</b></p>
                </div>
                <div>
                    <p><b>Audience:</b>Nursing Station</p>
                    <p><b>Purpose:</b>Real-time admitted patient visibility</p>
                    <p><b>Description:</b>Simulated Hospital Information System (HIS) built on a normalized MySQL relational database schema, demonstrating event-driven vital tracking and structured patient record management.</p>
                </div>
            </div>

            <div class="card nested-card">
                <div>
                    <h3>PM Oversight</h2>
                    <p class="nested-meta"><b>Integration Layer</b></p>
                </div>
                <div>
                    <p><b>Audience:</b> Technical PM / Integration Lead</p>
                    <p><b>Purpose:</b> System health & interface governance</p>
                    <p><b>Description:</b> Integration monitoring dashboard simulating HL7-style event tracking, validation workflows, retry handling, and latency analysis. Highlights sync success rates, failed messages, and interoperability KPIs.</p>
                </div>
            </div>

            <div class="card nested-card">
                <div>
                    <h3>Integration Reporting</h3>
                    <p class="nested-meta"><b>Reporting Layer</b></p>
                </div>
                <div>
                    <p><b>Audience:</b> Leadership / Operations Management</p>
                    <p><b>Purpose:</b> Consolidated integration performance reporting</p>
                    <p><b>Description:</b> Denormalized reporting layer built from MySQL database table abstractions. Provides reporting-level visibility into synchronization performance across operational and integration domains.</p>
                </div>
            </div>

            <div class="card nested-card">
                <div>
                    <h3>Clinical Research</h3>
                    <p class="nested-meta"><b>Research Layer</b></p>
                </div>
                <div>
                    <p><b>Audience:</b> Research & Analytics Team</p>
                    <p><b>Purpose:</b> External clinical data ingestion and analysis</p>
                    <p><b>Description:</b> Hybrid ETL simulation ingesting FHIR R4 observations (LOINC 8867-4 – heart rate). Raw JSON payloads are persisted while summary statistics are materialized for analytical visualization and trend modeling.</p>
                </div>
            </div>
        </div>
            
        <h2>Governance & Data Integrity Controls</h2>
        <div>
            <p>This simulation reflects enterprise integration governance principles including separation of operational and system oversight data.</p>
            <ul class="nested-list">
                <li>HL7 v2.x schema validation enforced (validation_status tracking)</li>
                <li>Retry logic monitored (retry_count visibility)</li>
                <li>Message latency measured for SLA adherence</li>
                <li>Failed message logging retained for audit traceability</li>
                <li>Reporting layer isolated via denormalized aggregation view</li>
                <li>Foreign key constraints ensure referential integrity</li>
            </ul>
        </div>
    </section>
</main>
