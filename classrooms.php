<?php $activeNav = 'classrooms'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Management — School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #059669;
            --bg-body: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-body);
            color: #1e293b;
        }

        .main-content { 
            margin-left: 260px; 
            padding: 40px;
        }

        .premium-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            font-size: 0.95rem;
        }

        .btn-premium {
            background: var(--primary);
            color: white;
            border-radius: 14px;
            padding: 0.8rem 2rem;
            font-weight: 700;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-premium:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .room-card {
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            background: #fff;
            padding: 1.5rem;
            transition: all 0.3s;
            height: 100%;
            position: relative;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }

        .room-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--primary), #34d399);
        }

        .action-btns {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: 0.3s;
        }

        .room-card:hover .action-btns { opacity: 1; }

        .btn-circle {
            width: 32px; height: 32px;
            padding: 0; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem;
        }

        #loadingOverlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body>
    <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="d-flex">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-1" id="userNameDisplay">Loading...</h2>
                    <p class="text-muted mb-0">Campus Facility Management</p>
                </div>
                <div class="dropdown">
                    <a href="#" class="text-decoration-none d-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=User" id="userAvatar" class="rounded-circle" width="35" height="35">
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.8rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4">
                        <li><a class="dropdown-item py-2 text-danger rounded-4" href="#" onclick="logout(); return false;"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </header>

            <div id="formView">
                <div class="premium-card mx-auto" style="max-width: 600px;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold mb-0" id="formTitle"><i class="fa-solid fa-school me-2 text-primary"></i>Register Classroom</h4>
                        <button class="btn btn-light rounded-pill btn-sm" onclick="toggleView()"><i class="fa-solid fa-list me-2"></i>View All Rooms</button>
                    </div>

                    <form id="roomForm">
                        <input type="hidden" name="id" id="field_id">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">ROOM IDENTIFIER *</label>
                                <input type="text" class="form-control" name="name" id="field_name" required placeholder="e.g. Room 101, Science Lab A">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">CAPACITY (STUDENTS)</label>
                                <input type="number" class="form-control" name="capacity" id="field_capacity" placeholder="e.g. 30" value="30">
                            </div>
                            <div class="col-md-12 pt-3 d-flex justify-content-end gap-3">
                                <button type="reset" class="btn btn-light rounded-pill px-4" onclick="resetForm()">Clear</button>
                                <button type="button" class="btn btn-premium px-5" id="btnSave">
                                    <span id="saveLabel">Register Room</span>
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="listView" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0">Facility Directory</h4>
                    <button class="btn btn-premium rounded-pill" onclick="toggleView()"><i class="fa-solid fa-plus me-2"></i>Add Room</button>
                </div>
                <div class="row g-4" id="roomGrid">
                    <!-- Cards injected here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="assets/js/app.js?v=9"></script>
    <script>
        (function() {
            const gridEl = document.getElementById('roomGrid');
            const form = document.getElementById('roomForm');
            const btnSave = document.getElementById('btnSave');
            const overlay = document.getElementById('loadingOverlay');

            let user = {};
            try { user = JSON.parse(localStorage.getItem('user') || '{}'); } catch (e) {}

            document.getElementById('userNameDisplay').textContent = (user.name || 'Admin') + "'s School";
            document.getElementById('userAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name || 'U') + '&background=10b981&color=fff&bold=true';

            function showLoader() { overlay.classList.remove('d-none'); }
            function hideLoader() { overlay.classList.add('d-none'); }
            function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

            async function loadList() {
                showLoader();
                try {
                    const res = await apiFetch('/classrooms');
                    if (res && res.success) {
                        const rows = res.data || [];
                        if (!rows.length) {
                            gridEl.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No rooms defined.</p></div>';
                        } else {
                            gridEl.innerHTML = rows.map(r => `
                                <div class="col-lg-4 col-md-6">
                                     <div class="room-card">
                                         <div class="action-btns">
                                             <button class="btn btn-circle btn-light text-primary btn-edit" data-id="${r.id}"><i class="fa-solid fa-pen"></i></button>
                                             <button class="btn btn-circle btn-light text-danger btn-del" data-id="${r.id}"><i class="fa-solid fa-trash"></i></button>
                                         </div>
                                         <h5 class="fw-bold mb-2">${esc(r.name)}</h5>
                                         <div class="small text-muted mb-1"><i class="fa-solid fa-users me-2"></i>Capacity: ${esc(r.capacity)} Students</div>
                                         <div class="small text-muted"><i class="fa-solid fa-hashtag me-2"></i>Internal ID: ${esc(r.id)}</div>
                                     </div>
                                 </div>
                            `).join('');
                            gridEl.querySelectorAll('.btn-edit').forEach(btn => btn.onclick = () => openEdit(btn.dataset.id));
                            gridEl.querySelectorAll('.btn-del').forEach(btn => btn.onclick = () => delRow(btn.dataset.id));
                        }
                    }
                } finally { hideLoader(); }
            }

            async function openEdit(id) {
                if (!id || id === 'undefined') return;
                showLoader();
                try {
                    const res = await apiFetch('/classrooms/' + id);
                    if (res && res.success) {
                        const r = res.data;
                        document.getElementById('field_id').value = r.id;
                        document.getElementById('field_name').value = r.name || '';
                        document.getElementById('field_capacity').value = r.capacity || 30;
                        document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Classroom';
                        document.getElementById('saveLabel').textContent = 'Update Room';
                        window.toggleView('form');
                    }
                } finally { hideLoader(); }
            }

            async function delRow(id) {
                if (!confirm('Delete this classroom?')) return;
                showLoader();
                try {
                    const res = await apiFetch('/classrooms/' + id, { method: 'DELETE' });
                    if (res && res.success) loadList();
                } finally { hideLoader(); }
            }

            btnSave.onclick = async () => {
                const id = document.getElementById('field_id').value;
                const name = document.getElementById('field_name').value.trim();
                const capacity = document.getElementById('field_capacity').value;
                if (!name) return;
                showLoader();
                try {
                    const path = id ? '/classrooms/' + id : '/classrooms';
                    const method = id ? 'PUT' : 'POST';
                    const res = await apiFetch(path, { method, body: JSON.stringify({ name, capacity }) });
                    if (res && res.success) {
                        resetForm();
                        toggleView('list');
                        loadList();
                    } else alert(res.message || 'Save failed');
                } finally { hideLoader(); }
            };

            window.toggleView = (v) => {
                const l = document.getElementById('listView');
                const f = document.getElementById('formView');
                if (v === 'form') { l.classList.add('d-none'); f.classList.remove('d-none'); }
                else if (v === 'list') { l.classList.remove('d-none'); f.classList.add('d-none'); }
                else { l.classList.toggle('d-none'); f.classList.toggle('d-none'); }
            };

            window.resetForm = () => {
                form.reset();
                document.getElementById('field_id').value = '';
                document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-school me-2 text-primary"></i>Register Classroom';
                document.getElementById('saveLabel').textContent = 'Register Room';
            };

            loadList();
        })();
    </script>
</body>
</html>
