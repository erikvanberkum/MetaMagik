# Changelog

## 1.5.6-erikvanberkum (2026-11-24)

Based on upstream 1.5.5 plus upstream changes since. Targets Kanboard 1.2.58+.

### Bugs fixed
- **Task save returned HTTP 500 on Kanboard 1.2.58.** Core now calls `validateAssignee()`, which `NewTaskValidator` lacked. Added the method (copied from core). Also covers bulk assignee changes.
- **Bulk edit with "Just add these tags" failed (upstream #99).** The form-only flag `tags_only_add_new` reached the SQL update (`Unknown column 'tags_only_add_new'`). The update model now handles add-only tags and strips the flag before the query.
- **Non-Latin custom field names were dropped (upstream #61).** `fixHumanName()` and `createMachineName()` now keep any letters and digits.

### Security
- **Task update model re-synced with core 1.2.58.** Restores the checks that reject updates to missing tasks, project changes, and column/swimlane/category IDs from other projects.

### Upstream changes since 1.5.5
- Task duplication: stale model overrides removed; custom fields copy through core hooks, so the 1.2.58 copy/move security fixes apply (761e6d3).
- UI: accordion line break fixed; "Custom Fields" collapses when empty (d46a854, cac7e5c).
- Added `isTypeDate()` helper (bdb5455), Ukrainian translation, updated Russian translations (b9c889d, 97ff067).

### Known issues
- Custom fields are not carried over when a recurring task creates its next copy (upstream #103).
