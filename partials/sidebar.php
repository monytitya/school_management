<?php
$activeNav = $activeNav ?? 'dashboard';
$navClass = function (string $key) use ($activeNav): string {
    return $key === $activeNav ? 'nav-link active' : 'nav-link';
};
?>
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

    <nav class="nav flex-column mb-auto sidebar-nav">
        <a class="<?php echo $navClass('dashboard'); ?>" href="dashboard.php"><i class="fa-solid fa-house me-3"></i>Dashboard</a>
        <a class="<?php echo $navClass('students'); ?>" href="student-registry.php"><i class="fa-solid fa-user-group me-3"></i>Students</a>
        <a class="<?php echo $navClass('teachers'); ?>" href="coming-soon.php?m=Teachers"><i class="fa-solid fa-chalkboard-user me-3"></i>Teachers</a>
        <a class="<?php echo $navClass('classes'); ?>" href="coming-soon.php?m=Classes"><i class="fa-solid fa-school me-3"></i>Classes</a>
        <a class="<?php echo $navClass('subjects'); ?>" href="coming-soon.php?m=Subjects"><i class="fa-solid fa-book me-3"></i>Subjects</a>
        <a class="<?php echo $navClass('attendance'); ?>" href="coming-soon.php?m=Attendance"><i class="fa-solid fa-calendar-check me-3"></i>Attendance</a>
        <a class="<?php echo $navClass('grades'); ?>" href="coming-soon.php?m=Grades"><i class="fa-solid fa-star me-3"></i>Grades</a>
        <div class="mt-4 mb-2 text-muted fw-bold px-3" style="font-size: 0.70rem; letter-spacing: 0.5px;">SYSTEM</div>
        <a class="<?php echo $navClass('application'); ?>" href="coming-soon.php?m=Application"><i class="fa-solid fa-layer-group me-3"></i>Application</a>
        <a class="<?php echo $navClass('setup'); ?>" href="coming-soon.php?m=Setup"><i class="fa-solid fa-gear me-3"></i>Set up</a>
    </nav>
</div>
