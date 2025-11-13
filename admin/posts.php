<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');
$u = current_user();
$pdo = db();

$posts = [];
$stmt = $pdo->query("SELECT p.*, u.name as author_name FROM posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $title = trim($_POST['title']);
            $body = trim($_POST['body']);
            $visible_to = $_POST['visible_to'];
            $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;

            if (empty($title) || empty($body)) {
                $message = 'Title and body cannot be empty.';
                $message_type = 'danger';
            } else {
                if ($_POST['action'] === 'add') {
                    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, body, visible_to) VALUES (?, ?, ?, ?)");
                    if ($stmt->execute([$u['id'], $title, $body, $visible_to])) {
                        $message = 'Post added successfully!';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to add post.';
                        $message_type = 'danger';
                    }
                } elseif ($_POST['action'] === 'edit') {
                    $stmt = $pdo->prepare("UPDATE posts SET title = ?, body = ?, visible_to = ? WHERE id = ?");
                    if ($stmt->execute([$title, $body, $visible_to, $post_id])) {
                        $message = 'Post updated successfully!';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to update post.';
                        $message_type = 'danger';
                    }
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $post_id = (int)$_POST['post_id'];
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            if ($stmt->execute([$post_id])) {
                $message = 'Post deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to delete post.';
                $message_type = 'danger';
            }
        }
    }
    // Redirect to clear POST data and show message
    header("Location: posts.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
    exit;
}

// Check for messages from redirect
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $message_type = htmlspecialchars($_GET['type']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Posts - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border: none;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border: none;
            transition: transform 0.2s;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(0,0,0,0.03);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="index.php">
                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="users.php">
                            <i class="fas fa-users me-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="approvals.php">
                            <i class="fas fa-check-circle me-1"></i>Approvals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="reports.php">
                            <i class="fas fa-chart-bar me-1"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white px-3 active" href="posts.php">
                            <i class="fas fa-bullhorn me-1"></i>Posts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="/attendance-project/public/index.php">
                            <i class="fas fa-globe me-1"></i>Public
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-bullhorn me-2"></i>Manage Posts & Guidelines
        </h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Add New Post
                </h5>
                <button class="btn btn-primary-custom btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addPostForm" aria-expanded="false" aria-controls="addPostForm">
                    <i class="fas fa-plus me-1"></i>Add Post
                </button>
            </div>
            <div class="card-body collapse" id="addPostForm">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="body" class="form-label">Body</label>
                        <textarea class="form-control" id="body" name="body" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="visible_to" class="form-label">Visible To</label>
                        <select class="form-select" id="visible_to" name="visible_to">
                            <option value="all">All (Public & Employees & Admins)</option>
                            <option value="employees">Employees Only</option>
                            <option value="admins">Admins Only</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save me-2"></i>Save Post
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-list-alt me-2"></i>Existing Posts
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Visible To</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No posts found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($post['title']) ?></td>
                                        <td><?= htmlspecialchars($post['author_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars(ucfirst($post['visible_to'])) ?></td>
                                        <td><?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#editPostModal"
                                                    data-id="<?= $post['id'] ?>"
                                                    data-title="<?= htmlspecialchars($post['title']) ?>"
                                                    data-body="<?= htmlspecialchars($post['body']) ?>"
                                                    data-visible_to="<?= htmlspecialchars($post['visible_to']) ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="posts.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPostModalLabel">Edit Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="post_id" id="edit_post_id">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_body" class="form-label">Body</label>
                            <textarea class="form-control" id="edit_body" name="body" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_visible_to" class="form-label">Visible To</label>
                            <select class="form-select" id="edit_visible_to" name="visible_to">
                                <option value="all">All (Public & Employees & Admins)</option>
                                <option value="employees">Employees Only</option>
                                <option value="admins">Admins Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-custom">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var editPostModal = document.getElementById('editPostModal');
        editPostModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');
            var body = button.getAttribute('data-body');
            var visibleTo = button.getAttribute('data-visible_to');

            var modalTitle = editPostModal.querySelector('.modal-title');
            var postIdInput = editPostModal.querySelector('#edit_post_id');
            var editTitleInput = editPostModal.querySelector('#edit_title');
            var editBodyTextarea = editPostModal.querySelector('#edit_body');
            var editVisibleToSelect = editPostModal.querySelector('#edit_visible_to');

            modalTitle.textContent = 'Edit Post (ID: ' + id + ')';
            postIdInput.value = id;
            editTitleInput.value = title;
            editBodyTextarea.value = body;
            editVisibleToSelect.value = visibleTo;
        });
    </script>
</body>
</html>
