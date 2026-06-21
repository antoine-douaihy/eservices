**Sprint Grooming:**
Focused on the core business logic. We defined the "Service Request" flow where citizens apply for documents, and office staff review them. Tasks were divided into: Service Migrations/Models, Citizen Request UI, and Staff Approval Dashboard.

**Sprint Review:**
Successfully demonstrated a citizen logging in, submitting a formal application, and a staff member logging in to approve the request, updating the database status in real-time.

**Sprint Retrospective:**
* What went well: The Guardian Middleware easily scaled to protect the new Staff-only approval routes.
* What could be improved: Query performance when loading hundreds of requests in the staff table was slightly slow.
* Action Item: Implement Laravel Pagination on all data tables moving forward.
