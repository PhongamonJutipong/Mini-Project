<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pixora | Login</title>
    <link rel="stylesheet" href="../css/StyleLogin.css">
</head>

<body>
    <!-- NAVBAR: สไตล์เดิม -->
    <nav class="navbar">
        <h1>Pixora</h1>
    </nav>

    <!-- LOGIN PANEL: มีแท็บ User / Admin -->
    <div class="login-container">
        <div class="login-box">
            <div class="login-brand">
                <div class="brand-badge"></div>
                <h2 class="login-title">Welcome back</h2>
                <p class="login-subtitle">Sign in to continue to <strong>Pixora</strong></p>
            </div>

            <!-- Tabs -->
            <div class="tabs" role="tablist" aria-label="Login type">
                <button class="tab active" role="tab" aria-selected="true" aria-controls="panel-user" id="tab-user">
                    👤 User
                </button>
                <button class="tab" role="tab" aria-selected="false" aria-controls="panel-admin" id="tab-admin">
                    🔐 Admin
                </button>
            </div>

            <!-- Panel: USER -->
            <section class="tab-panel" id="panel-user" role="tabpanel" aria-labelledby="tab-user">
                <form action="LoginSuccess.php" method="post" class="login-form" novalidate>
                    <label for="user-username" class="sr-only">Email</label>
                    <input
                        type="text"
                        id="user-username"
                        name="username"
                        inputmode="email"
                        autocomplete="username"
                        placeholder="Email"
                        required />

                    <div class="password-field">
                        <label for="user-password" class="sr-only">Password</label>
                        <input
                            type="password"
                            id="user-password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="Password"
                            required />
                        <button type="button" class="toggle-pass" aria-label="Show password"
                            onclick="togglePass('user-password', this)">Show</button>
                    </div>

                    <div class="login-row">
                        <label class="remember">
                            <input type="checkbox" name="remember" value="1" />
                            <span>Remember me</span>
                        </label>
                        <a href="forgot.php" class="forgot">Forgot password?</a>
                    </div>

                    <button type="submit" name="login" class="login-btn">Login</button>
                </form>

                <p class="login-foot">
                    Don't have an account?
                    <a href="register.php">Register</a>
                </p>
            </section>

            <!-- Panel: ADMIN -->
            <section class="tab-panel hidden" id="panel-admin" role="tabpanel" aria-labelledby="tab-admin">
                <form action="LoginAdminsuccess.php" method="post" class="login-form" novalidate>
                    <label for="admin-username" class="sr-only">Admin Email</label>
                    <input
                        type="text"
                        id="admin-username"
                        name="username"
                        inputmode="email"
                        autocomplete="username"
                        placeholder="Admin Email"
                        required />
                    <div class="password-field">
                        <label for="admin-password" class="sr-only">Admin Password</label>
                        <input
                            type="password"
                            id="admin-password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="Admin Password"
                            required />
                        <button type="button" class="toggle-pass" aria-label="Show password"
                            onclick="togglePass('admin-password', this)">Show</button>
                    </div>

                    <button type="submit" name="login" class="login-btn">Login as Admin</button>
                </form>

                <p class="login-foot muted">
                    Admin access only. If you are a user, switch back to the <strong>User</strong> tab.
                </p>
            </section>
        </div>
    </div>

    <!-- JavaScript สำหรับสลับแท็บและแสดง/ซ่อนรหัสผ่าน -->
    <script>
        const elTabUser = document.getElementById('tab-user');
        const elTabAdmin = document.getElementById('tab-admin');
        const panelUser = document.getElementById('panel-user');
        const panelAdmin = document.getElementById('panel-admin');

        function setActiveTab(isUser) {
            elTabUser.classList.toggle('active', isUser);
            elTabAdmin.classList.toggle('active', !isUser);

            elTabUser.setAttribute('aria-selected', String(isUser));
            elTabAdmin.setAttribute('aria-selected', String(!isUser));

            panelUser.classList.toggle('hidden', !isUser);
            panelAdmin.classList.toggle('hidden', isUser);
        }

        elTabUser.addEventListener('click', () => setActiveTab(true));
        elTabAdmin.addEventListener('click', () => setActiveTab(false));

        function togglePass(inputId, btn) {
            const i = document.getElementById(inputId);
            if (i.type === 'password') {
                i.type = 'text';
                btn.setAttribute('aria-label', 'Hide password');
                btn.textContent = 'Hide';
            } else {
                i.type = 'password';
                btn.setAttribute('aria-label', 'Show password');
                btn.textContent = 'Show';
            }
        }
    </script>
</body>

</html>`