<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Control · Pixora</title>
    <link rel="stylesheet" href="StyleMainaddmin.css" />
</head>

<body>
    <header class="site-header">
        <div class="topnav">
            <a href="#" class="brand">
                <h1>Pixora Admin</h1>
            </a>
            <div class="top-actions">
                <span class="badge">Admin</span>
                <img src="user.png" alt="Admin profile" />
            </div>
        </div>
    </header>

    <main class="container">
        <section class="card">
            <h2>Find User by UID</h2>
            <form class="row" aria-label="Query user by UID">
                <div class="field">
                    <label for="uid">UID</label>
                    <input id="uid" name="uid" type="text" placeholder="e.g. u_7f93ab12" />
                    <p class="hint">ใส่ UID ของผู้ใช้เพื่อค้นหา จากนั้นจะแสดงข้อมูลด้านล่าง</p>
                </div>
                <button type="submit" class="btn">Fetch User</button>
            </form>
        </section>

        <!-- User Profile (Edit) -->
        <section class="grid-2">
            <div class="card">
                <div class="card-head">
                    <h2>User Profile</h2>
                    <span class="status status-idle" title="Waiting for fetch">Idle</span>
                </div>

                <!-- ตัวอย่าง: ฟิลด์ถูก disable จนกว่าจะ fetch สำเร็จ -->
                <form class="stack" aria-label="Edit user profile">
                    <div class="field">
                        <label for="u-name">Full name</label>
                        <input id="u-name" name="name" type="text" placeholder="—" disabled />
                    </div>

                    <div class="field">
                        <label for="u-email">Email</label>
                        <input id="u-email" name="email" type="email" placeholder="—" disabled />
                    </div>

                    <div class="row">
                        <div class="field">
                            <label for="u-role">Role</label>
                            <select id="u-role" name="role" disabled>
                                <option value="">—</option>
                                <option value="user">user</option>
                                <option value="creator">creator</option>
                                <option value="admin">admin</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="u-status">Status</label>
                            <select id="u-status" name="status" disabled>
                                <option value="">—</option>
                                <option value="active">active</option>
                                <option value="suspended">suspended</option>
                                <option value="deleted">deleted</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="field">
                            <label for="u-joined">Joined at</label>
                            <input id="u-joined" name="joined_at" type="text" placeholder="—" disabled />
                        </div>
                        <div class="field">
                            <label for="u-last">Last active</label>
                            <input id="u-last" name="last_active" type="text" placeholder="—" disabled />
                        </div>
                    </div>

                    <div class="actions">
                        <button type="button" class="btn btn-secondary" disabled>Reset</button>
                        <button type="submit" class="btn" disabled>Save Changes</button>
                    </div>

                    <p class="note">
                        เมื่อเชื่อมต่อ Backend:
                        • <code>GET /api/users/:uid</code> → เติมค่า field + ปลดล็อกการแก้ไข
                        • <code>PUT /api/users/:uid</code> → ส่งค่าที่แก้ไขแล้วเพื่ออัปเดต
                    </p>
                </form>
            </div>

            <!-- Read-only posts -->
            <div class="card">
                <div class="card-head">
                    <h2>User Posts (Read-only)</h2>
                    <button class="btn btn-light" type="button" disabled>Refresh</button>
                </div>

                <div class="posts">
                    <!-- ตัวอย่างการ์ดโพสต์ (placeholder) -->
                    <article class="post">
                        <div class="post-thumb">
                            <img src="IMG_logo.jpg" alt="Post thumbnail" />
                        </div>
                        <div class="post-body">
                            <h3 class="post-title">—</h3>
                            <p class="post-meta">Post ID: — · Created: —</p>
                            <p class="post-desc">ผลลัพธ์โพสต์ของผู้ใช้จะแสดงที่นี่หลังจาก Fetch โพรไฟล์</p>
                        </div>
                    </article>

                    <!-- เพิ่มรายการโพสต์ตามจริงด้วย server data -->
                </div>

                <p class="note">
                    เมื่อเชื่อมต่อ Backend:
                    • <code>GET /api/users/:uid/posts</code> → สร้างรายการการ์ดโพสต์ตามข้อมูลจริง
                </p>
            </div>
        </section>
    </main>
</body>

</html>