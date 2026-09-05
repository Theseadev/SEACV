<?php
namespace App\Controllers;

use Flight;
use App\Services\Database;
use PDO;
use Exception;

class ProductController {
    public function add(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pdo = Database::getConnection();
        $error = '';
        $adminUsername = htmlspecialchars($_SESSION["username"] ?? 'Fahrul');
        $adminInitial = strtoupper(substr($adminUsername, 0, 1));

        $stats = ['total' => 0, 'ats' => 0, 'kreatif' => 0, 'lamaran' => 0];
        try {
            $st = $pdo->query("SELECT COUNT(*) as total, SUM(category LIKE '%ats%') as ats, SUM(category LIKE '%kreatif%') as kreatif, SUM(category LIKE '%lamaran%') as lamaran FROM products")->fetch();
            if ($st) {
                $stats['total'] = (int)($st['total'] ?? 0);
                $stats['ats'] = (int)($st['ats'] ?? 0);
                $stats['kreatif'] = (int)($st['kreatif'] ?? 0);
                $stats['lamaran'] = (int)($st['lamaran'] ?? 0);
            }
        } catch (Exception $e) {}

        $sidebarCategories = getCategoriesWithCounts($pdo);
        $categoriesList = [];
        try {
            $categoriesList = $pdo->query("SELECT name FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {}
        if (empty($categoriesList)) {
            $categoriesList = ['CV Kreatif', 'CV ATS', 'Surat Lamaran Kerja'];
        }

        $versionFile = dirname(__DIR__, 2) . '/version.json';
        $versionData = file_exists($versionFile) ? json_decode(file_get_contents($versionFile), true) : [];
        $sidebarRepo = $versionData['github_repo'] ?? 'Theseadev/SEACV';
        $sidebarBranch = $versionData['github_branch'] ?? 'main';
        $sidebarCommit = substr($versionData['current_commit'] ?? '5540ac8', 0, 7);

        $request = Flight::request();
        if ($request->method === 'POST') {
            $name = trim($request->data->name ?? '');
            $price = trim($request->data->price ?? '');
            $category = trim($request->data->category ?? '');
            $newCategory = trim($request->data->new_category ?? '');

            if ($category === '__new__' || !empty($newCategory)) {
                if (!empty($newCategory)) {
                    $category = preg_replace('/\s+/', ' ', $newCategory);
                    try {
                        $insCat = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
                        $insCat->execute([$category]);
                    } catch (Exception $e) {}
                } else {
                    $category = '';
                }
            }

            $files = $_FILES['image'] ?? null;
            if (empty($name) || empty($price) || empty($category) || empty($files['name'])) {
                $error = "Semua field wajib diisi (termasuk kategori) dan file foto template wajib dipilih.";
            } elseif (!is_numeric($price)) {
                $error = "Harga harus berupa angka nominal valid tanpa titik/koma.";
            } else {
                $image_name = $files['name'];
                $image_tmp  = $files['tmp_name'];
                $image_ext  = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($image_ext, $allowed_extensions)) {
                    $uploadsDir = dirname(__DIR__, 2) . '/uploads';
                    if (!is_dir($uploadsDir)) {
                        mkdir($uploadsDir, 0777, true);
                    }
                    $clean_filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $image_name);
                    $rel_image_path = 'uploads/' . uniqid() . '_' . $clean_filename;
                    $abs_image_path = dirname(__DIR__, 2) . '/' . $rel_image_path;

                    if (move_uploaded_file($image_tmp, $abs_image_path)) {
                        $stmt = $pdo->prepare("INSERT INTO products (name, price, category, image) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $price, $category, $rel_image_path]);

                        Flight::redirect(url('/admin?msg=added'));
                        return;
                    } else {
                        $error = "Gagal memproses upload file gambar. Periksa permission folder uploads.";
                    }
                } else {
                    $error = "Format gambar tidak didukung. Harap gunakan format JPG, PNG, atau WEBP.";
                }
            }
        }

        Flight::render('admin/add_product', [
            'error' => $error,
            'adminUsername' => $adminUsername,
            'adminInitial' => $adminInitial,
            'stats' => $stats,
            'sidebarCategories' => $sidebarCategories,
            'categoriesList' => $categoriesList,
            'sidebarRepo' => $sidebarRepo,
            'sidebarBranch' => $sidebarBranch,
            'sidebarCommit' => $sidebarCommit,
            'pdo' => $pdo
        ]);
    }

    public function edit(int $id): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($id <= 0) {
            Flight::redirect(url('/admin'));
            return;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            Flight::redirect(url('/admin'));
            return;
        }

        $error = '';
        $adminUsername = htmlspecialchars($_SESSION["username"] ?? 'Fahrul');
        $adminInitial = strtoupper(substr($adminUsername, 0, 1));
        $displayName = resolveProductDisplayName($product);

        $stats = ['total' => 0, 'ats' => 0, 'kreatif' => 0, 'lamaran' => 0];
        try {
            $st = $pdo->query("SELECT COUNT(*) as total, SUM(category LIKE '%ats%') as ats, SUM(category LIKE '%kreatif%') as kreatif, SUM(category LIKE '%lamaran%') as lamaran FROM products")->fetch();
            if ($st) {
                $stats['total'] = (int)($st['total'] ?? 0);
                $stats['ats'] = (int)($st['ats'] ?? 0);
                $stats['kreatif'] = (int)($st['kreatif'] ?? 0);
                $stats['lamaran'] = (int)($st['lamaran'] ?? 0);
            }
        } catch (Exception $e) {}

        $sidebarCategories = getCategoriesWithCounts($pdo);
        $categoriesList = [];
        try {
            $categoriesList = $pdo->query("SELECT name FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {}
        if (empty($categoriesList)) {
            $categoriesList = ['CV Kreatif', 'CV ATS', 'Surat Lamaran Kerja'];
        }
        if (!empty($product['category']) && !in_array($product['category'], $categoriesList)) {
            $categoriesList[] = $product['category'];
        }

        $versionFile = dirname(__DIR__, 2) . '/version.json';
        $versionData = file_exists($versionFile) ? json_decode(file_get_contents($versionFile), true) : [];
        $sidebarRepo = $versionData['github_repo'] ?? 'Theseadev/SEACV';
        $sidebarBranch = $versionData['github_branch'] ?? 'main';
        $sidebarCommit = substr($versionData['current_commit'] ?? '5540ac8', 0, 7);

        $request = Flight::request();
        if ($request->method === 'POST') {
            $name = trim($request->data->name ?? '');
            $price = trim($request->data->price ?? '');
            $category = trim($request->data->category ?? '');
            $newCategory = trim($request->data->new_category ?? '');

            if ($category === '__new__' || !empty($newCategory)) {
                if (!empty($newCategory)) {
                    $category = preg_replace('/\s+/', ' ', $newCategory);
                    try {
                        $insCat = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
                        $insCat->execute([$category]);
                    } catch (Exception $e) {}
                } else {
                    $category = '';
                }
            }

            if (empty($name) || empty($price) || empty($category)) {
                $error = "Semua field wajib diisi.";
            } elseif (!is_numeric($price)) {
                $error = "Harga harus berupa angka nominal valid tanpa titik/koma.";
            } else {
                $image_path = $product['image'];
                $files = $_FILES['image'] ?? null;

                if (!empty($files['name'])) {
                    $image_name = $files['name'];
                    $image_tmp  = $files['tmp_name'];
                    $image_ext  = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($image_ext, $allowed_extensions)) {
                        $uploadsDir = dirname(__DIR__, 2) . '/uploads';
                        if (!is_dir($uploadsDir)) {
                            mkdir($uploadsDir, 0777, true);
                        }
                        $clean_filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $image_name);
                        $rel_image_path = 'uploads/' . uniqid() . '_' . $clean_filename;
                        $abs_image_path = dirname(__DIR__, 2) . '/' . $rel_image_path;

                        if (move_uploaded_file($image_tmp, $abs_image_path)) {
                            $old_abs_path = dirname(__DIR__, 2) . '/' . $product['image'];
                            if (file_exists($old_abs_path) && is_file($old_abs_path)) {
                                @unlink($old_abs_path);
                            }
                            $image_path = $rel_image_path;
                        } else {
                            $error = "Gagal memproses upload file gambar baru.";
                        }
                    } else {
                        $error = "Format gambar tidak didukung. Harap gunakan format JPG, PNG, atau WEBP.";
                    }
                }

                if (empty($error)) {
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, image = ? WHERE id = ?");
                    $stmt->execute([$name, $price, $category, $image_path, $id]);

                    Flight::redirect(url('/admin?msg=updated'));
                    return;
                }
            }
        }

        Flight::render('admin/edit_product', [
            'id' => $id,
            'product' => $product,
            'displayName' => $displayName,
            'error' => $error,
            'adminUsername' => $adminUsername,
            'adminInitial' => $adminInitial,
            'stats' => $stats,
            'sidebarCategories' => $sidebarCategories,
            'categoriesList' => $categoriesList,
            'sidebarRepo' => $sidebarRepo,
            'sidebarBranch' => $sidebarBranch,
            'sidebarCommit' => $sidebarCommit,
            'pdo' => $pdo
        ]);
    }

    public function delete(int $id): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($id <= 0) {
            Flight::redirect(url('/admin'));
            return;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $delStmt->execute([$id]);

            $imageFile = dirname(__DIR__, 2) . '/' . $product['image'];
            if (file_exists($imageFile) && is_file($imageFile)) {
                @unlink($imageFile);
            }
        }

        Flight::redirect(url('/admin?msg=deleted'));
    }
}
