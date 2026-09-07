<?php
/**
 * Elite Estates - Admin Logout
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Models/Admin.php';

use EliteEstates\Models\Admin;

Admin::logout();
header('Location: login.php');
exit;
