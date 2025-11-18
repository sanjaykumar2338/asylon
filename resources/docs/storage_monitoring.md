# Storage Monitoring (Plan)

## Goals
- Track per-org storage usage (files uploaded with reports).
- Optional quota per org; send warnings when approaching limits.

## Data Needed
- Sum of `size` column in `report_files` grouped by org.
- Total count and average file size per org.

## Surfacing Usage
- Admin dashboard widget: usage per org with progress bars toward quota.
- Org admin view: current usage, top file types.

## Notifications (future)
- Warning at 80% of quota to org admins.
- Block uploads at 100% (optional toggle).

## Implementation Steps (future)
- Add `storage_quota_mb` to orgs (nullable).
- Add cached usage table or scheduled job to aggregate `report_files.size` per org.
- Add alerts/notifications for thresholds.
- Expose API/Blade components for usage bars.
