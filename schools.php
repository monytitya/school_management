<?php $activeNav = 'setup'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Settings — School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --bg-body: #f8fafc;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); }
        .main-content { margin-left: 260px; padding: 40px; }
        .premium-card { background: white; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem; }
        .btn-premium { background: var(--primary); color: white; border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 600; border: none; }
        .btn-premium:hover { background: var(--primary-hover); color: white; }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>
        <div class="main-content flex-grow-1">
            <h2 class="fw-bold mb-4">School Directory</h2>
            <div id="schoolList">
                <!-- Schools here -->
            </div>
            
            <div class="premium-card" style="max-width: 500px;">
                <h5 class="fw-bold mb-3">Add New School</h5>
                <form id="schoolForm">
                    <div class="mb-3">
                        <label class="form-label">School Title</label>
                        <input type="text" class="form-control" id="school_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade Levels (e.g. 12)</label>
                        <input type="number" class="form-control" id="level_count" value="12">
                    </div>
                    <button type="submit" class="btn btn-premium w-100">Save School</button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        (function() {
            const form = document.getElementById('schoolForm');
            const list = document.getElementById('schoolList');

            async function load() {
                const res = await apiFetch('/schools');
                if (res && res.success) {
                    list.innerHTML = res.data.map(s => `
                        <div class="premium-card d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="fw-bold mb-0">${s.school_title}</h6>
                                <small class="text-muted">${s.level_count} Grades - ${s.is_active ? 'Active' : 'Inactive'}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="del(${s.id})">Delete</button>
                        </div>
                    `).join('');
                }
            }

            form.onsubmit = async (e) => {
                e.preventDefault();
                const res = await apiFetch('/schools', {
                    method: 'POST',
                    body: JSON.stringify({
                        school_title: document.getElementById('school_title').value,
                        level_count: document.getElementById('level_count').value
                    })
                });
                if (res && res.success) { form.reset(); load(); }
            };

            window.del = async (id) => {
                if (confirm('Delete?')) {
                    await apiFetch('/schools/' + id, { method: 'DELETE' });
                    load();
                }
            };

            load();
        })();
    </script>
</body>
</html>
