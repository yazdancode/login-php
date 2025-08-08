<?php declare(strict_types=1);

class Semej {
    private static string $domain = 'localhost';

    public static function checkSession(): void {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_set_cookie_params(0, '/', self::$domain, true, true);
                ini_set('session.cookie_httponly', '1');
                session_start();
                session_regenerate_id(true);
                ob_start();
            }
        } catch(Exception $e) {
            echo "Error in checkSession() --> " . (string)$e;
        }
    }

    public static function validation(string $data): string {
        try {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            // فیلتر FILTER_SANITIZE_STRING دیگه استفاده نشود (deprecated)
            return $data;
        } catch(Exception $e) {
            echo "Error in validation() --> " . (string)$e;
            return '';
        }
    }

    public static function set(?string $status = null, ?string $title = null, ?string $message = null): void {
        try {
            self::checkSession();
            $_SESSION['semej_lib_alerts_status'] = self::validation($status ?? '');
            $_SESSION['semej_lib_alerts_title'] = self::validation($title ?? '');
            $_SESSION['semej_lib_alerts_message'] = self::validation($message ?? '');
            session_write_close();
        } catch(Exception $e) {
            echo "Error in set() --> " . (string)$e;
        }
    }

    public static function status(): void {
        try {
            self::checkSession();
            if (isset($_SESSION['semej_lib_alerts_status'])) {
                echo $_SESSION['semej_lib_alerts_status'];
            }
        } catch(Exception $e) {
            echo "Error in status() --> " . (string)$e;
        }
    }

    public static function title(): void {
        try {
            self::checkSession();
            if (isset($_SESSION['semej_lib_alerts_title'])) {
                echo $_SESSION['semej_lib_alerts_title'];
            }
        } catch(Exception $e) {
            echo "Error in title() --> " . (string)$e;
        }
    }

    public static function message(): void {
        try {
            self::checkSession();
            if (isset($_SESSION['semej_lib_alerts_message'])) {
                echo $_SESSION['semej_lib_alerts_message'];
            }
        } catch(Exception $e) {
            echo "Error in message() --> " . (string)$e;
        }
    }

    public static function clear(): void {
        try {
            self::checkSession();
            unset(
                $_SESSION['semej_lib_alerts_status'],
                $_SESSION['semej_lib_alerts_title'],
                $_SESSION['semej_lib_alerts_message']
            );
            session_write_close();
        } catch(Exception $e) {
            echo "Error in clear() --> " . (string)$e;
        }
    }

    public static function show(): void {
        try {
            self::checkSession();
            if (
                isset($_SESSION['semej_lib_alerts_message'],
                    $_SESSION['semej_lib_alerts_title'],
                    $_SESSION['semej_lib_alerts_status'])
            ) {
                $status = $_SESSION['semej_lib_alerts_status'];
                $title = $_SESSION['semej_lib_alerts_title'];
                $message = $_SESSION['semej_lib_alerts_message'];

                echo <<<HTML
<div class="alert alert-{$status} alert-dismissible fade show" role="alert">
    <strong>{$title}</strong> {$message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;
            }
            self::clear();
        } catch(Exception $e) {
            echo "Error in show() --> " . (string)$e;
        }
    }

    public static function alert(): void {
        try {
            self::checkSession();
            if (
                isset($_SESSION['semej_lib_alerts_message'],
                    $_SESSION['semej_lib_alerts_title'],
                    $_SESSION['semej_lib_alerts_status'])
            ) {
                $title = $_SESSION['semej_lib_alerts_title'];
                $message = $_SESSION['semej_lib_alerts_message'];
                $status = $_SESSION['semej_lib_alerts_status']; // مثل success, error, warning

                // نوع آیکون SweetAlert با توجه به وضعیت
                $icon = match($status) {
                    'success' => 'success',
                    'error' => 'error',
                    'warning' => 'warning',
                    'info' => 'info',
                    default => 'question',
                };

                echo <<<JS
<script>
Swal.fire({
    icon: '{$icon}',
    title: '{$title}',
    text: '{$message}',
    confirmButtonText: 'باشه'
});
</script>
JS;
            }
            self::clear();
        } catch(Exception $e) {
            echo "Error in alert() --> " . (string)$e;
        }
    }
}
