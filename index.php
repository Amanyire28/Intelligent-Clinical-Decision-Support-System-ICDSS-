<?php
/**
 * ============================================================================
 * MAIN APPLICATION ROUTER & ENTRY POINT
 * index.php - Routes requests to appropriate controllers
 * ============================================================================
 */

// Start session for user authentication
session_start();

// Load configuration
require_once __DIR__ . '/config/db_config.php';

/**
 * Simple router to direct requests to appropriate views/controllers
 * In a production system, you'd use a more robust routing framework
 */

// Get the requested page from URL query parameter
$page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 'login';

// Check if user is authenticated (except for login page)
if ($page !== 'login' && !isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Route to appropriate view
switch ($page) {
    case 'login':
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            // Redirect to dashboard based on role
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: index.php?page=admin-dashboard');
            } else {
                header('Location: index.php?page=doctor-dashboard');
            }
            exit;
        }
        require_once __DIR__ . '/views/login.php';
        break;
    
    case 'doctor-dashboard':
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/DoctorController.php';
        $controller = new DoctorController();
        $controller->dashboard();
        break;
    
    case 'patient-assessment':
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AssessmentController.php';
        $controller = new AssessmentController();
        $controller->createAssessment();
        break;
    
    case 'assessment-results':
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AssessmentController.php';
        $controller = new AssessmentController();
        $assessment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $controller->displayResults($assessment_id);
        break;
    
    case 'patient-search':
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/PatientController.php';
        $controller = new PatientController();
        $controller->searchPatients();
        break;
    
    case 'admin-dashboard':
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
        break;
    
    case 'admin-users':
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->manageUsers();
        break;
    
    case 'admin-assessments':
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->viewAssessments();
        break;
    
    case 'admin-reports':
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->viewReports();
        break;
    
    case 'admin-config':
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->config();
        break;
    
    case 'api-user-action':
        if ($_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->handleUserAction();
        break;
    
    case 'admin-outcomes':
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->manageOutcomes();
        break;
    
    case 'api-outcome-action':
        // Allow both doctors and admins to record outcomes
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->handleOutcomeAction();
        break;
    
    case 'api-patient-search':
        // Doctors can search for patients
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        require_once __DIR__ . '/controllers/PatientController.php';
        $controller = new PatientController();
        $controller->searchPatientAPI();
        break;
    
    case 'api-historical-insights':
        // Get historical data insights for decision support
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        require_once __DIR__ . '/models/HistoricalAnalytics.php';
        header('Content-Type: application/json');
        
        $db = getDBConnection();
        $analytics = new HistoricalAnalytics($db);
        
        $action = $_GET['action'] ?? null;
        $assessmentId = intval($_GET['assessment_id'] ?? 0);
        
        // Get assessment details first
        require_once __DIR__ . '/models/Assessment.php';
        $assessmentModel = new Assessment($db);
        $assessment = $assessmentModel->getAssessmentById($assessmentId);
        
        if (!$assessment) {
            echo json_encode(['success' => false, 'message' => 'Assessment not found']);
            exit;
        }
        
        // Get patient details
        require_once __DIR__ . '/models/Patient.php';
        $patientModel = new Patient($db);
        $patient = $patientModel->getPatientById($assessment['patient_id']);
        $assessment['date_of_birth'] = $patient['date_of_birth'];
        
        // Get risk result for this assessment
        require_once __DIR__ . '/models/RiskResult.php';
        $riskModel = new RiskResult($db);
        $riskResult = $riskModel->getRiskByAssessmentId($assessmentId);
        if ($riskResult) {
            $assessment['risk_score'] = $riskResult['risk_score'];
            $assessment['risk_level'] = $riskResult['risk_level'];
        }
        
        $result = ['success' => true, 'data' => []];
        
        switch ($action) {
            case 'similar-cases':
                $result['data']['similar_cases'] = $analytics->findSimilarCases($assessment, 5);
                break;
            case 'diagnosis-distribution':
                $result['data']['distribution'] = $analytics->getDiagnosisDistribution(
                    $assessment['risk_level'] ?? 'Moderate',
                    $assessment['smoking_status'] ?? 'unknown'
                );
                break;
            case 'cohort-stats':
                $result['data']['stats'] = $analytics->getCohortStats(
                    $assessment['smoking_status'] ?? 'unknown',
                    $assessment['risk_level'] ?? 'Moderate'
                );
                break;
            case 'recommendation':
                $result['data']['recommendation'] = $analytics->generateRecommendation(
                    $assessment['risk_level'] ?? 'Moderate',
                    $assessment['smoking_status'] ?? 'unknown',
                    $_GET['symptoms'] ?? ''
                );
                break;
            case 'full-insights':
                $result['data']['similar_cases'] = $analytics->findSimilarCases($assessment, 5);
                $result['data']['diagnosis_distribution'] = $analytics->getDiagnosisDistribution(
                    $assessment['risk_level'] ?? 'Moderate',
                    $assessment['smoking_status'] ?? 'unknown'
                );
                $result['data']['cohort_stats'] = $analytics->getCohortStats(
                    $assessment['smoking_status'] ?? 'unknown',
                    $assessment['risk_level'] ?? 'Moderate'
                );
                $result['data']['risk_accuracy'] = $analytics->getRiskAccuracy($assessment['risk_level'] ?? 'Moderate');
                $result['data']['recommendation'] = $analytics->generateRecommendation(
                    $assessment['risk_level'] ?? 'Moderate',
                    $assessment['smoking_status'] ?? 'unknown',
                    $_GET['symptoms'] ?? ''
                );
                break;
            default:
                $result['success'] = false;
                $result['message'] = 'Invalid action';
        }
        
        echo json_encode($result);
        exit;
        break;
    
    case 'api-patient-assessments':
        // Doctors can view patient assessments
        if ($_SESSION['user_role'] !== 'doctor' && $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $patient_id = intval($_POST['patient_id'] ?? 0);
        
        if ($patient_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'assessments' => []]);
            exit;
        }
        
        // Get patient assessments
        header('Content-Type: application/json');
        
        try {
            require_once __DIR__ . '/models/Assessment.php';
            $db = getDBConnection();
            $assessmentModel = new Assessment($db);
            
            $assessments = $assessmentModel->getPatientAssessments($patient_id);
            
            echo json_encode(['success' => true, 'assessments' => $assessments]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'assessments' => []]);
        }
        exit;
        break;
    
    case 'logout':
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    
    default:
        // Redirect to appropriate dashboard based on user role
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: index.php?page=admin-dashboard');
            } else {
                header('Location: index.php?page=doctor-dashboard');
            }
        } else {
            header('Location: index.php?page=login');
        }
        exit;
}

/**
 * Sanitize user input to prevent XSS attacks
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
?>
