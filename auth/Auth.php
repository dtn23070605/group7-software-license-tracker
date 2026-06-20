<?php

/**
 * RBAC (rút gọn) — Auth Helper
 *
 * Quản lý session đăng nhập đơn giản, không dùng password thật.
 * Người dùng chỉ cần chọn user có sẵn trong bảng `users` để "đăng nhập"
 * — đủ để demo phân quyền 2 nhóm: ADMIN (gồm TEACHER) và STUDENT.
 *
 * Lưu ý: đây là RBAC tối giản cho mục đích học tập/demo,
 * KHÔNG dùng để bảo mật thật trong môi trường production.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {

    public static function login(int $userId, string $username, string $role): void {
        $_SESSION['user_id']  = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $role;
    }

    public static function logout(): void {
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function getUsername(): ?string {
        return $_SESSION['username'] ?? null;
    }

    public static function getRole(): ?string {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Admin gồm TEACHER — vì trong hệ thống này Teacher đóng vai trò
     * quản lý cấp phát license cho Student, tương đương quyền Admin.
     */
    public static function isAdmin(): bool {
        return self::getRole() === 'TEACHER';
    }

    public static function isStudent(): bool {
        return self::getRole() === 'STUDENT';
    }

    /**
     * Chặn truy cập nếu chưa đăng nhập — redirect về trang login.
     * Gọi đầu mỗi controller cần bảo vệ.
     */
    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header("Location: login.php");
            exit;
        }
    }

    /**
     * Chặn truy cập nếu không phải Admin/Teacher.
     * Dùng cho các module chỉ Admin được thấy (Software Titles, Users, Rules,
     * Pools, Revocation, Expiry, Usage Stats...).
     */
    public static function requireAdmin(): void {
        self::requireLogin();
        if (!self::isAdmin()) {
            header("Location: index.php?module=allocation&action=index&error=forbidden");
            exit;
        }
    }
}
