# group7-software-license-tracker
Web2 Final Project - Software License Tracker
In Progress
tien dat — Catalog Layer
Tables: software_titles, users, allocation_rules
Files phụ trách:

database.sql
config/Database.php
public/index.php
views/layout/header.php
views/layout/footer.php
models/SoftwareTitle.php
models/User.php
models/AllocationRule.php
controllers/SoftwareTitleController.php
controllers/UserController.php
controllers/AllocationRuleController.php
views/software/index.php
views/software/create.php
views/software/edit.php
views/user/index.php
views/user/create.php
views/user/edit.php
views/rule/index.php
views/rule/create.php
views/rule/edit.php

Công việc cụ thể:

Thiết kế toàn bộ schema database (9 bảng)
Cài đặt Singleton Pattern cho kết nối database
Viết router điều hướng request (public/index.php)
Xây dựng layout dùng chung (header, footer, sidebar)
CRUD đầy đủ cho Software Titles, Users, Allocation Rules
Business rules: không trùng tên phần mềm, không trùng username/email, không xóa software nếu còn pool liên kết, không trùng rule cho cùng software + role


manh duc — Pool & Allocation Layer
Tables: license_pools, license_allocations, activation_logs
Files phụ trách:

models/LicensePool.php
models/LicenseAllocation.php
models/ActivationLog.php
controllers/LicensePoolController.php
controllers/LicenseAllocationController.php
controllers/ActivationLogController.php
views/pool/index.php
views/pool/create.php
views/pool/edit.php
views/allocation/index.php
views/allocation/create.php
views/allocation/edit.php
views/activation/index.php

Công việc cụ thể:

CRUD đầy đủ cho License Pools
CRUD + quản lý trạng thái cho License Allocations (ACTIVE / EXPIRED / REVOKED)
Tự động ghi log khi cấp phát license (activation_logs)
Tự động giảm available_quantity trong pool khi cấp phát
Business rules: available quantity không được vượt total, expiry date phải ở tương lai, không cấp từ pool đã hết hạn, không cấp nếu pool hết slot, user không được có 2 license active cho cùng 1 phần mềm
Hiển thị danh sách activation log


ngoc nguyen — Reporting & Events Layer
Tables: expiry_notifications, revocation_logs, usage_stats
Files phụ trách:

models/ExpiryNotification.php
models/RevocationLog.php
models/UsageStat.php
controllers/ExpiryNotificationController.php
controllers/RevocationLogController.php
controllers/UsageStatController.php
views/expiry/index.php
views/revocation/index.php
views/revocation/create.php
views/stats/index.php
views/stats/create.php
README.md

Công việc cụ thể:

Tự động phát hiện và ghi log thông báo hết hạn khi còn 7 ngày hoặc 1 ngày (không gửi lại nếu đã gửi)
Giao diện xem danh sách expiry notifications với badge 7_DAYS / 1_DAY
Form thu hồi license (chỉ thu hồi được license đang ACTIVE), tự động cập nhật status sang REVOKED
Hiển thị lịch sử thu hồi kèm lý do
Nhập và hiển thị usage stats theo học kỳ, tự động tính activation rate
Bộ lọc thống kê theo term
Viết README hướng dẫn cài đặt và phân công thành viên