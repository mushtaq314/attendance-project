<?php
// public/index.php - company feed with posts
require_once __DIR__ . '/../includes/db.php';
$posts = db()->query("SELECT p.*, u.name FROM posts p JOIN users u ON u.id = p.user_id WHERE p.visible_to = 'all' ORDER BY p.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Company Feed - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .hero-section {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            border-radius: 20px;
            padding: 3rem 2rem;
            margin: 2rem 0;
            text-align: center;
            color: white;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        .post-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .post-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 1.5rem;
        }
        .post-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .post-content {
            padding: 1.5rem;
        }
        .post-meta {
            display: flex;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }
        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 1rem;
        }
        .post-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: white;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }
        .company-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 1rem;
            }
            .hero-section h1 {
                font-size: 2rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .post-header {
                padding: 1rem;
            }
            .post-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="#">
                <i class="fas fa-building me-2"></i>Company Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#posts">
                            <i class="fas fa-bullhorn me-1"></i>Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/attendance-project/employee/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Employee Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="hero-section">
            <img src="https://via.placeholder.com/150x60/4CAF50/FFFFFF?text=Company+Logo" alt="Company Logo" class="company-logo">
            <h1>Welcome to Our Company</h1>
            <p>Stay updated with the latest announcements, news, and important updates from our team.</p>
        </div>

        <section id="posts">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="fas fa-bullhorn"></i>
                    <h3>No Announcements Yet</h3>
                    <p>Check back later for company updates and announcements.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($posts as $p): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="post-card">
                                <div class="post-header">
                                    <h3>
                                        <i class="fas fa-newspaper me-2"></i>
                                        <?= htmlspecialchars($p['title']) ?>
                                    </h3>
                                </div>
                                <div class="post-content">
                                    <p class="mb-3">
                                        <?= nl2br(htmlspecialchars(substr($p['body'], 0, 200))) ?>
                                        <?php if (strlen($p['body']) > 200): ?>
                                            <span class="text-muted">... <em>Read more</em></span>
                                        <?php endif; ?>
                                    </p>
                                    <div class="post-meta">
                                        <div class="author-avatar">
                                            <?= strtoupper(substr($p['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                <?= htmlspecialchars($p['name']) ?>
                                            </div>
                                            <div class="post-date">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('F j, Y \a\t g:i A', strtotime($p['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
