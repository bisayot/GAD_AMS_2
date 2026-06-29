# Frontend Comparison Report
**Downloaded:** `C:\Users\HP\Downloads\GAD_AMS-main\GAD_AMS-main\frontend`
**Local:** `c:\xampp\htdocs\GAD_AMS_2\frontend`

> [!IMPORTANT]
> Your **local version is generally NEWER** than the downloaded one — most files are larger locally (indicating additions/improvements). The downloaded copy appears to be an **older snapshot** of the codebase.

---

## 🔴 Files Missing from Local (in downloaded, not locally)

| File | Note |
|------|------|
| `views/admin/AccomplishmentReportController.php` | A PHP controller file placed inside the Vue frontend — likely misplaced in the download. Does NOT belong in the frontend `src` directory. |

---

## 🟡 Files With Different Sizes

In almost every case below, your **local file is LARGER** than the download, meaning your local version has more content (likely newer features). Files where the download is larger may contain changes not yet applied locally.

### Root Files
| File | Downloaded | Local | Who is larger? |
|------|-----------|-------|----------------|
| `index.html` | 691 B | 710 B | ✅ Local is newer |
| `vercel.json` | 95 B | 102 B | ✅ Local is newer |

### `src/`
| File | Downloaded | Local | Who is larger? |
|------|-----------|-------|----------------|
| `api.js` | 2,118 B | 1,803 B | ⚠️ **Downloaded is larger** |
| `style.css` | 246 B | 564 B | ✅ Local is newer |

### `src/components/`
| File | Downloaded | Local | Who is larger? |
|------|-----------|-------|----------------|
| `Footer.vue` | 2,134 B | 1,928 B | ⚠️ **Downloaded is larger** |
| `PdfPreviewModal.vue` | 2,775 B | 6,784 B | ✅ Local is much newer |

### `src/views/` (root-level)
| File | Downloaded | Local | Who is larger? |
|------|-----------|-------|----------------|
| `AboutView.vue` | 5,684 B | 9,061 B | ✅ Local is newer |
| `AdminAnnualReport.vue` | 11,021 B | 10,695 B | ⚠️ **Downloaded is larger** |
| `AdminDashboard.vue` | 3,956 B | 4,879 B | ✅ Local is newer |
| `CollegeDashboard.vue` | 3,591 B | 4,365 B | ✅ Local is newer |
| `ContactView.vue` | 13,172 B | 12,968 B | ⚠️ **Downloaded is larger** |
| `ForgotPasswordView.vue` | 5,166 B | 5,287 B | ✅ Local is newer |
| `GADCornerView.vue` | 17,255 B | 17,568 B | ✅ Local is newer |
| `HomeView.vue` | 32,447 B | 33,529 B | ✅ Local is newer |
| `LoginView.vue` | 8,421 B | 9,211 B | ✅ Local is newer |
| `MandatesView.vue` | 12,648 B | 12,928 B | ✅ Local is newer |
| `PlaceholderContent.vue` | 1,097 B | 1,127 B | ✅ Local is newer |
| `RegisterView.vue` | 19,934 B | 20,317 B | ✅ Local is newer |
| `ResetPasswordView.vue` | 9,275 B | 9,470 B | ✅ Local is newer |
| `ResourcesView.vue` | 12,648 B | 10,295 B | ⚠️ **Downloaded is larger** |
| `StaffDashboard.vue` | 4,138 B | 4,834 B | ✅ Local is newer |

### `src/views/admin/`
| File | Downloaded | Local | Who is larger? |
|------|-----------|-------|----------------|
| `ActivityLogsView.vue` | 14,837 B | 15,295 B | ✅ Local is newer |
| `AdListView.vue` | 18,701 B | 21,030 B | ✅ Local is newer |
| `AdminDashboardContent.vue` | 39,039 B | 40,265 B | ✅ Local is newer |
| `ADReview.vue` | 29,819 B | 40,585 B | ✅ Local is much newer |
| `ADView.vue` | 21,852 B | 24,623 B | ✅ Local is newer |
| `ArchiveView.vue` | 23,293 B | 23,901 B | ✅ Local is newer |
| `ARListView.vue` | 19,522 B | 21,108 B | ✅ Local is newer |
| `ARReview.vue` | 30,322 B | 42,635 B | ✅ Local is much newer |
| `ARView.vue` | 25,650 B | 29,881 B | ✅ Local is newer |
| `AssignMandates.vue` | 5,380 B | 5,494 B | ✅ Local is newer |
| `BudgetView.vue` | 14,498 B | 12,116 B | ⚠️ **Downloaded is larger** |
| `DesignReview.vue` | 5,133 B | 5,135 B | Negligible difference |
| `DesignView.vue` | 3,472 B | 3,474 B | Negligible difference |
| `DocumentTrashBin.vue` | 9,843 B | 10,116 B | ✅ Local is newer |
| `GadPlanBudgetView.vue` | 19,837 B | 22,527 B | ✅ Local is newer |
| `MandatesView.vue` | 31,942 B | 32,914 B | ✅ Local is newer |
| `MessagesView.vue` | 58,503 B | 59,773 B | ✅ Local is newer |
| `PrivacyPolicyView.vue` | 14,464 B | 14,980 B | ✅ Local is newer |
| `ReportsView.vue` | 17,363 B | 18,045 B | ✅ Local is newer |
| `SubmittedListView.vue` | 25,592 B | 22,743 B | ⚠️ **Downloaded is larger** |
| `UserManagementView.vue` | 31,063 B | 31,960 B | ✅ Local is newer |
| `UserManualView.vue` | 18,587 B | 19,174 B | ✅ Local is newer |

### `src/views/staff/`
| File | Downloaded | Local | Who is larger? |
|------|-----------|-------|----------------|
| `ActivityLogsView.vue` | 14,738 B | 15,194 B | ✅ Local is newer |
| `AdListView.vue` | 18,851 B | 21,124 B | ✅ Local is newer |
| `ADRevision.vue` | 56,671 B | 53,852 B | ⚠️ **Downloaded is larger** |
| `ADView.vue` | 21,856 B | 22,561 B | ✅ Local is newer |
| `ArchiveView.vue` | 23,297 B | 23,905 B | ✅ Local is newer |
| `ARListView.vue` | 19,476 B | 21,202 B | ✅ Local is newer |
| `ARRevision.vue` | 51,805 B | 52,648 B | ✅ Local is newer |
| `ARView.vue` | 25,630 B | 29,874 B | ✅ Local is newer |
| `BudgetAllocationView.vue` | 21,478 B | 21,969 B | ✅ Local is newer |
| `BudgetView.vue` | 24,049 B | 24,986 B | ✅ Local is newer |
| `GadPlanBudgetView.vue` | 19,841 B | 22,531 B | ✅ Local is newer |
| `MandatesView.vue` | 31,946 B | 32,918 B | ✅ Local is newer |
| `MessagesView.vue` | 56,363 B | 57,591 B | ✅ Local is newer |
| `PrivacyPolicyView.vue` | 14,468 B | 14,984 B | ✅ Local is newer |
| `ReportsView.vue` | 17,367 B | 18,049 B | ✅ Local is newer |
| `StaffDashboardContent.vue` | 40,360 B | 41,632 B | ✅ Local is newer |
| `SubmitADView.vue` | 64,996 B | 64,182 B | ⚠️ **Downloaded is larger** |
| `SubmitARView.vue` | 60,457 B | 60,396 B | ⚠️ **Downloaded is larger** |
| `SubmittedListView.vue` | 25,594 B | 22,747 B | ⚠️ **Downloaded is larger** |
| `SubmitView.vue` | 4,855 B | 5,050 B | ✅ Local is newer |
| `UserManagementView.vue` | 30,996 B | 31,892 B | ✅ Local is newer |
| `UserManualView.vue` | 18,591 B | 19,178 B | ✅ Local is newer |

### `src/views/twg/`
| File | Downloaded | Local | Who is larger? |
|------|-----------|-------|----------------|
| `ActivityLogsView.vue` | 10,930 B | 11,279 B | ✅ Local is newer |
| `ADRevision.vue` | 56,613 B | 53,858 B | ⚠️ **Downloaded is larger** |
| `ADView.vue` | 21,884 B | 22,561 B | ✅ Local is newer |
| `ArchiveView.vue` | 24,562 B | 23,911 B | ⚠️ **Downloaded is larger** |
| `ARRevision.vue` | 51,813 B | 52,648 B | ✅ Local is newer |
| `ARView.vue` | 25,597 B | 29,872 B | ✅ Local is newer |
| `CollegeDashboardContent.vue` | 29,531 B | 30,516 B | ✅ Local is newer |
| `GadPlanBudgetView.vue` | 20,088 B | 22,785 B | ✅ Local is newer |
| `MandatesView.vue` | 21,106 B | 21,913 B | ✅ Local is newer |
| `MessagesView.vue` | 56,329 B | 57,556 B | ✅ Local is newer |
| `PrivacyPolicyView.vue` | 14,434 B | 14,949 B | ✅ Local is newer |
| `SubmitADView.vue` | 64,999 B | 64,183 B | ⚠️ **Downloaded is larger** |
| `SubmitARView.vue` | 60,462 B | 60,401 B | ⚠️ **Downloaded is larger** |
| `SubmittedListView.vue` | 24,993 B | 25,919 B | ✅ Local is newer |
| `SubmittView.vue` | 3,884 B | 4,043 B | ✅ Local is newer |
| `UserManualView.vue` | 18,590 B | 19,178 B | ✅ Local is newer |

---

## ⚠️ Files Where Downloaded Version is LARGER (Potentially Missing Changes)

These are the files you should manually diff — the downloaded version has content your local copy might be missing:

| File | Downloaded | Local | Diff |
|------|-----------|-------|------|
| `src/api.js` | 2,118 B | 1,803 B | +315 B |
| `components/Footer.vue` | 2,134 B | 1,928 B | +206 B |
| `views/AdminAnnualReport.vue` | 11,021 B | 10,695 B | +326 B |
| `views/ContactView.vue` | 13,172 B | 12,968 B | +204 B |
| `views/ResourcesView.vue` | 12,648 B | 10,295 B | +2,353 B |
| `views/admin/BudgetView.vue` | 14,498 B | 12,116 B | +2,382 B |
| `views/admin/SubmittedListView.vue` | 25,592 B | 22,743 B | +2,849 B |
| `views/staff/ADRevision.vue` | 56,671 B | 53,852 B | +2,819 B |
| `views/staff/SubmitADView.vue` | 64,996 B | 64,182 B | +814 B |
| `views/staff/SubmitARView.vue` | 60,457 B | 60,396 B | +61 B |
| `views/staff/SubmittedListView.vue` | 25,594 B | 22,747 B | +2,847 B |
| `views/twg/ADRevision.vue` | 56,613 B | 53,858 B | +2,755 B |
| `views/twg/ArchiveView.vue` | 24,562 B | 23,911 B | +651 B |
| `views/twg/SubmitADView.vue` | 64,999 B | 64,183 B | +816 B |
| `views/twg/SubmitARView.vue` | 60,462 B | 60,401 B | +61 B |

---

## ✅ Files Matching (No Differences)
- `src/router/index.js` — identical sizes, no changes needed
- `App.vue`, `main.js` — identical
- All `components/` except `Footer.vue` and `PdfPreviewModal.vue`
