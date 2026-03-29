<?php
$module = isset($_GET['m']) ? htmlspecialchars($_GET['m'], ENT_QUOTES, 'UTF-8') : 'This module';
$activeNav = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $module; ?> — School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .sidebar { width: 260px; height: 100vh; position: fixed; background: white; border-right: 1px solid #edf2f7; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link.active { background: #fff1f2; color: #ff4757; }
        .nav-link:hover:not(.active) { background: #f1f5f9; }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold text-dark" id="userNameDisplay">Loading...</h5>
            <a href="#" class="text-danger text-decoration-none font-sm" onclick="logout(); return false;">Sign out</a>
        </header>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <i class="fa-solid fa-screwdriver-wrench text-danger mb-3" style="font-size: 2.5rem;"></i>
            <h4 class="fw-bold"><?php echo $module; ?></h4>
            <p class="text-muted mb-0">This section is not built in the UI yet. Use the API or Student Registry for enrollment data.</p>
            <a href="dashboard.php" class="btn btn-danger mt-4 rounded-pill px-4">Back to dashboard</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="assets/js/app.js?v=6"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    try {
        const u = localStorage.getItem('user');
        if (u) document.getElementById('userNameDisplay').textContent = JSON.parse(u).name || 'User';
    } catch (e) {}
});
function logout() {
    localStorage.clear();
    window.location.href = 'login.php';
}
</script>
</body>
</html>
