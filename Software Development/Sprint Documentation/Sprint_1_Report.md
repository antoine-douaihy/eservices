**Sprint Grooming (Backlog Refinement):**
The team established the core architecture. We determined the database schema required modification to support government IDs (id_document) and advanced names (first_name, last_name). Tasks were divided: Citizen Registration (Person A), Profile Management (Person C), Database Seeding & 2FA (Person D), and Guardian Middleware Access Control (System Admin).

**Sprint Review:**
The team successfully demonstrated a working Citizen Registration flow. The Guardian Middleware was tested and successfully intercepts logins, correctly routing citizens to /home and admins to /admin/dashboard.

**Sprint Retrospective:**
* What went well: Task delegation allowed for parallel development. The middleware logic is highly secure.
* What could be improved: We experienced severe merge conflicts in routes/web.php and app/Models/User.php when combining features.
* Action Item: Mandate daily communication before pushing routing updates and utilize visual merge tools to protect code integrity.
