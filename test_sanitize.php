<?php
$page = htmlspecialchars(strip_tags(trim('api-patient-assessments')), ENT_QUOTES, 'UTF-8');
echo "Original: api-patient-assessments\n";
echo "After sanitize: $page\n";
echo "Are they equal? " . ($page === 'api-patient-assessments' ? 'YES' : 'NO');
?>
