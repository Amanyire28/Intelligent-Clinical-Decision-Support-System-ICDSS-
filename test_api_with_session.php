<?php
// This test page should be inside an authenticated session
session_start();

// Simulate being logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'doctor';
    $_SESSION['user_name'] = 'Dr. Sarah Johnson';
}

// Now test the API
?><!DOCTYPE html>
<html>
<head>
    <title>Test Assessment API - With Session</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        button { padding: 10px 20px; font-size: 14px; cursor: pointer; }
        #result { margin-top: 20px; padding: 10px; border: 1px solid #ccc; background: #f5f5f5; min-height: 200px; white-space: pre-wrap; font-family: monospace; font-size: 12px; }
        .success { background: #e8f5e9; border-color: #4caf50; }
        .error { background: #ffebee; border-color: #f44336; }
    </style>
</head>
<body>
    <h1>Test Assessment API (With Active Session)</h1>
    <button onclick="testPatient(3)">Test Patient 3</button>
    <button onclick="testPatient(5)">Test Patient 5</button>
    <div id="result"></div>
    
    <script>
    async function testPatient(patientId) {
        const resultDiv = document.getElementById('result');
        resultDiv.textContent = 'Loading...';
        resultDiv.className = '';
        
        try {
            const response = await fetch('/CANCER/index.php?page=api-patient-assessments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'patient_id=' + patientId,
                credentials: 'same-origin'
            });
            
            const text = await response.text();
            console.log('Response status:', response.status);
            console.log('Response text length:', text.length);
            
            let data;
            try {
                data = JSON.parse(text);
                if (data.success) {
                    resultDiv.className = 'success';
                    resultDiv.textContent = 'SUCCESS!\n\nPatient ID: ' + patientId + '\nAssessments Found: ' + data.assessments.length + '\n\nFull Response:\n' + JSON.stringify(data, null, 2);
                } else {
                    resultDiv.className = 'error';
                    resultDiv.textContent = 'ERROR from API: ' + (data.message || 'Unknown error') + '\n\n' + JSON.stringify(data, null, 2);
                }
            } catch (parseError) {
                resultDiv.className = 'error';
                resultDiv.textContent = 'JSON Parse Error: ' + parseError.message + '\n\nResponse (first 500 chars):\n' + text.substring(0, 500);
            }
        } catch (error) {
            resultDiv.className = 'error';
            resultDiv.textContent = 'Fetch Error: ' + error.message;
        }
    }
    </script>
</body>
</html>
