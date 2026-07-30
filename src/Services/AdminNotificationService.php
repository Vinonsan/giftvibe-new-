<?php

namespace Services;

use App\Core\Database;

class AdminNotificationService
{
    public static function create(string $type, string $title, string $message, ?string $entityType = null, ?int $entityId = null): void
    {
        Database::instance()->execute(
            'INSERT INTO admin_notifications (type, title, message, entity_type, entity_id)
             VALUES (:type, :title, :message, :entity_type, :entity_id)',
            [
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]
        );
    }

    public static function unreadCount(): int
    {
        return (int) (Database::instance()->fetch(
            'SELECT COUNT(*) AS total FROM admin_notifications WHERE status = "unread"'
        )['total'] ?? 0);
    }
}
