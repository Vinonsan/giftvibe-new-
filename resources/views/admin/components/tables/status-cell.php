<?php
/**
 * Variables:
 * @var string $status
 */
$status = strtolower($status);
$badgeType = 'gray';
if (in_array($status, ['active', 'published', 'completed', 'paid', 'approved'])) {
    $badgeType = 'success';
} elseif (in_array($status, ['pending', 'draft', 'processing'])) {
    $badgeType = 'warning';
} elseif (in_array($status, ['inactive', 'cancelled', 'failed', 'deleted'])) {
    $badgeType = 'danger';
}
?>
<?php component('admin/components/common/badge', ['label' => ucfirst($status), 'variant' => $badgeType]); ?>