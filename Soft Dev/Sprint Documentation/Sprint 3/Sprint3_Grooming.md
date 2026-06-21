# Sprint 3 — Grooming (Backlog Refinement)
**Epic: Government Office Dashboard**
**Sprint Dates:** February 7, 2026 – February 13, 2026
**Facilitator:** Person C
**Attendees:** Person A, Person B, Person C, Person D

---

## Sprint Goal
Build the complete Government Office (Municipality) workspace. Staff must be able to define services with pricing and required documents, manage incoming citizen requests through a Kanban pipeline, auto-generate PDF certificates on approval, and communicate with citizens via in-app messaging and notifications.

---

## Backlog Items Reviewed & Estimated

| Story ID | User Story | Assigned To | Priority | Story Points | Status |
|----------|-----------|-------------|----------|--------------|--------|
| US-013 | As Office Staff, I want to define services with a name, price, and list of required documents. | Person A | Critical | 8 | Selected |
| US-014 | As Office Staff, I want to view all incoming citizen requests in a Kanban/list inbox and update their status. | Person B | Critical | 8 | Selected |
| US-015 | As a system, when a request is approved, a PDF certificate must be auto-generated and made available for download. | Person C | High | 8 | Selected |
| US-016 | As Office Staff, I want to message citizens in-app when documents are missing or updates are needed. | Person D | High | 8 | Selected |
| US-017 | As a citizen, I want to receive a real-time or email notification whenever my request status changes. | Person D | High | 5 | Selected |
| US-018 | As Office Staff, I want to create categories for services so they are organized and easy for citizens to browse. | Person A | Medium | 3 | Selected |

**Total Committed Points: 40**

---

## Task Breakdown per Person

### Person A — Service Definition Engine
- [ ] Create `services` table: `office_id`, `category_id`, `name`, `description`, `price`, `duration_days`
- [ ] Create `service_categories` table: `office_id`, `name`
- [ ] Create `required_documents` table linked to `services`
- [ ] Build Office Staff CRUD interface for services and categories
- [ ] Allow staff to add/remove required document entries per service
- [ ] Display service list on the citizen-facing browse page

### Person B — Request Pipeline (Kanban / List View)
- [ ] Create `requests` table: `citizen_id`, `service_id`, `office_id`, `status`, `notes`, `payment_status`
- [ ] Build the Office Staff "Inbox" page listing all requests for their office
- [ ] Add status filter tabs: Pending / In Review / Missing Documents / Approved / Rejected / Completed
- [ ] Build status-change dropdown/button per request with confirmation
- [ ] Allow staff to add internal notes and upload response documents per request
- [ ] Update `requests.status` in database on each change

### Person C — Automated Document Generator (PDF)
- [ ] Install `barryvdh/laravel-dompdf` via Composer
- [ ] Design a Blade PDF template: certificate with office header, citizen name, service name, date, reference number
- [ ] Trigger PDF generation automatically when request status is set to `Approved`
- [ ] Store generated PDF path in `requests.certificate_path`
- [ ] Allow citizen to download the certificate from their dashboard

### Person D — Communication & Notification Hub
- [ ] Install and configure `laravel/echo` and `pusher-php-server` (or use Laravel's database broadcast driver)
- [ ] Create `messages` table: `request_id`, `sender_id`, `receiver_id`, `body`, `read_at`
- [ ] Build the in-app chat UI accessible from each request detail page
- [ ] Trigger email notification (via Laravel Mail + Queue) on every request status change
- [ ] Show real-time notification badge in the Office Staff nav bar

---

## Database Changes This Sprint

| Table | Column | Type | Notes |
|-------|--------|------|-------|
| `services` | `office_id`, `category_id`, `price` | FK + decimal | New table |
| `service_categories` | `id`, `name`, `office_id` | Standard | New table |
| `required_documents` | `service_id`, `label` | FK + varchar | New table |
| `requests` | Full schema | See above | New table — central entity |
| `messages` | Full schema | See above | New table |

---

## Definition of Done
- [ ] Office Staff can create a service with categories and required documents
- [ ] Requests can be moved through all 6 statuses
- [ ] PDF auto-generated on approval and downloadable by citizen
- [ ] In-app message sent and received in the same browser session
- [ ] Email notification arrives within 60 seconds of status change
- [ ] All tasks closed on Azure DevOps board

---

## Dependencies & Risks
- `dompdf` requires the PHP `gd` extension — verify server has it enabled.
- Real-time notifications with Pusher require a Pusher account or a self-hosted `soketi` instance.
- Request status logic must be guarded: only forward transitions allowed (e.g., cannot go from Completed back to Pending).
