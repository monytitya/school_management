<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - School Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light auth-bg d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 450px; border-radius: 20px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-3 d-flex justify-content-center align-items-center">
                    <div style="width: 40px; height: 40px; background: #3742fa; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path></svg>
                    </div>
                    <h4 class="fw-bold text-dark mb-0 pb-0" style="font-family: 'Inter', sans-serif;">SchoolManager</h4>
                </div>
                <h5 class="fw-bold mt-4" style="color: #2c3e50;">Create Account</h5>
                <p class="text-muted small">Join the school ecosystem today</p>
            </div>
            <form id="registerForm">
                <div class="mb-3">
                    <label class="form-label font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #a5b1c2; text-transform: uppercase;">Full Name</label>
                    <input type="text" id="name" class="form-control form-control-lg bg-light border-0" style="border-radius: 12px; font-size: 0.95rem; padding: 12px 16px;" placeholder="John Doe" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #a5b1c2; text-transform: uppercase;">Email Address</label>
                    <input type="email" id="email" class="form-control form-control-lg bg-light border-0" style="border-radius: 12px; font-size: 0.95rem; padding: 12px 16px;" placeholder="name@school.edu" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #a5b1c2; text-transform: uppercase;">Password</label>
                    <input type="password" id="password" class="form-control form-control-lg bg-light border-0" style="border-radius: 12px; font-size: 0.95rem; padding: 12px 16px;" placeholder="••••••••" required>
                </div>
                <div class="mb-4">
                    <label class="form-label font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #a5b1c2; text-transform: uppercase;">Role</label>
                    <select id="role" class="form-select form-select-lg bg-light border-0" style="border-radius: 12px; font-size: 0.95rem; padding: 12px 16px;">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold" style="border-radius: 12px; background: linear-gradient(135deg, #5352ed, #3742fa); border: none; font-size: 1rem; padding: 12px;">Complete Registration</button>
            </form>
            <div class="text-center mt-4 pt-2 border-top">
                <a href="login.php" class="text-decoration-none small text-muted fw-medium">Already have an account? <span class="text-primary">Login here</span></a>
            </div>
        </div>
    </div>
    <script src="assets/js/app.js?v=5"></script>
</body>
</html>
