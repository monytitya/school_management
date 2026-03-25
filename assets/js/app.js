const API_BASE = window.location.origin + '/Kingardent/school-management/index.php/api';

async function safeJson(response) {
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch (_) {
        const plain = text.replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim();
        throw new Error(plain.substring(0, 300) || 'Unknown server error');
    }
}

function checkAuth() {
    const token = localStorage.getItem('token');
    if (!token && !window.location.pathname.includes('login.php') && !window.location.pathname.includes('register.php')) {
        window.location.href = 'login.php';
    } else if (token && (window.location.pathname.includes('login.php') || window.location.pathname.includes('register.php'))) {
        window.location.href = 'dashboard.php';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    checkAuth();

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email    = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btn      = loginForm.querySelector('button');
            const origText = btn.innerHTML;

            try {
                btn.innerHTML = 'Logging in...';
                btn.disabled  = true;

                const response = await fetch(`${API_BASE}/auth/login`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify({ email, password })
                });

                const data = await safeJson(response);

                const payload = data.data || data;
                if (response.ok && data.success && payload.token) {
                    localStorage.setItem('token', payload.token);
                    localStorage.setItem('user', JSON.stringify(payload.user || { name: 'User' }));
                    window.location.href = 'dashboard.php';
                } else {
                    alert('Login failed: ' + (data.message || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Login error: ' + err.message);
            } finally {
                btn.innerHTML = origText;
                btn.disabled  = false;
            }
        });
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name     = document.getElementById('name').value;
            const email    = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const role     = document.getElementById('role').value;
            const btn      = registerForm.querySelector('button');
            const origText = btn.innerHTML;

            try {
                btn.innerHTML = 'Registering...';
                btn.disabled  = true;

                const response = await fetch(`${API_BASE}/auth/register`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify({ name, email, password, role })
                });

                const data = await safeJson(response);

                if (response.ok && data.success) {
                    alert('Registration successful! Please login.');
                    window.location.href = 'login.php';
                } else {
                    alert('Registration failed: ' + (data.message || data.error || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Error: ' + err.message);
            } finally {
                btn.innerHTML = origText;
                btn.disabled  = false;
            }
        });
    }
});

async function apiFetch(endpoint, options = {}) {
    const token = localStorage.getItem('token');
    const headers = {
        ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        ...(options.headers || {})
    };

    if (options.body instanceof FormData) {
        delete headers['Content-Type'];
    } else if (!headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }

    const fullUrl = endpoint.startsWith('/')
        ? `${API_BASE}${endpoint}`
        : `${API_BASE}/${endpoint}`;

    try {
        const response = await fetch(fullUrl, { ...options, headers });

        if (response.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = 'login.php';
            return null;
        }
        return await safeJson(response);
    } catch (e) {
        console.error('API Fetch Error:', e);
        throw e;
    }
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'login.php';
}
