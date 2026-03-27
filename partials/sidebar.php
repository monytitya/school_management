<?php
$activeNav = $activeNav ?? 'dashboard';
$navClass = function (string $key) use ($activeNav): string {
    return $key === $activeNav ? 'nav-link active' : 'nav-link';
};

$role = $_SESSION['user']['role'] ?? 'guest';
// Since we use JS-based auth mostly, let's add a script trigger for client side hide/show too but base PHP on session if possible.
// Fallback: If no session, the JS in dashboard will redirect anyway.
?>
<style>
    .sidebar-nav .nav-link { padding: 12px 16px; border-radius: 12px; margin-bottom: 4px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; }
</style>
<div class="sidebar d-flex flex-column p-4">
    <div class="d-flex align-items-center mb-5 mt-2 px-2">
        <div class="logo-icon bg-danger text-white rounded-3 d-flex align-items-center justify-content-center me-2"
            style="width: 28px; height: 28px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
            </svg>
        </div>
        <h5 class="fw-bold mb-0" style="letter-spacing: -0.5px; font-size: 1.15rem; color: #1e293b;">School<span
                style="color: #ff4757;">Manager</span></h5>
    </div>

    <nav class="nav flex-column mb-auto sidebar-nav" id="sidebarLinks">
        <a class="<?php echo $navClass('dashboard'); ?>" href="dashboard.php" id="nav_dash"><i class="fa-solid fa-house me-3"></i>Dashboard</a>
        
        <!-- STUDENT ROLE ONLY -->
        <a class="<?php echo $navClass('portal'); ?> d-none student-only" href="student-portal.php"><i class="fa-solid fa-user-graduate me-3"></i>My Portal</a>
        
        <a class="<?php echo $navClass('teachers'); ?> admin-only" href="teachers.php"><i class="fa-solid fa-chalkboard-user me-3"></i>Teachers</a>
        <a class="<?php echo $navClass('students'); ?> admin-only" href="student-registry.php"><i class="fa-solid fa-address-book me-3"></i>Registry</a>
        <a class="<?php echo $navClass('classes'); ?> staff-only" href="classes.php"><i class="fa-solid fa-school me-3"></i>Classes</a>
        <a class="<?php echo $navClass('subjects'); ?> staff-only" href="subjects.php"><i class="fa-solid fa-book me-3"></i>Subjects</a>
        <a class="<?php echo $navClass('classrooms'); ?> staff-only" href="classrooms.php"><i class="fa-solid fa-layer-group me-3"></i>Rooms</a>
        <a class="<?php echo $navClass('attendance'); ?> staff-only" href="attendance.php"><i class="fa-solid fa-calendar-check me-3"></i>Attendance</a>
        <a class="<?php echo $navClass('grades'); ?>" href="coming-soon.php?m=Grades"><i class="fa-solid fa-star me-3"></i>Grades</a>
        <div class="mt-4 mb-2 text-muted fw-bold px-3 admin-only" style="font-size: 0.70rem; letter-spacing: 0.5px;">SYSTEM</div>
        <a class="<?php echo $navClass('setup'); ?> admin-only" href="setup_admin.php"><i class="fa-solid fa-gear me-3"></i>Set up</a>
    </nav>
</div>

<script>
    (function() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const role = (user.role || 'guest').toLowerCase();
        
        if (role === 'student') {
            document.querySelectorAll('.student-only').forEach(e => e.classList.remove('d-none'));
            document.querySelectorAll('.admin-only, .staff-only').forEach(e => e.classList.add('d-none'));
        } else if (role === 'teacher') {
             document.querySelectorAll('.staff-only').forEach(e => e.classList.remove('d-none'));
             document.querySelectorAll('.admin-only').forEach(e => e.classList.add('d-none'));
        } else if (role === 'admin') {
             document.querySelectorAll('.admin-only, .staff-only').forEach(e => e.classList.remove('d-none'));
        }
    })();
</script>
