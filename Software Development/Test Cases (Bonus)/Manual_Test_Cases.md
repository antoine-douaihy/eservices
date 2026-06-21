| Test ID | Module | Title | Pre-conditions | Action Steps | Expected Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-001** | Auth | Validate Guardian Routing (Citizen) | Citizen account exists | 1. Navigate to /login<br>2. Enter citizen credentials<br>3. Click Submit | System logs user in and redirects to /home. Access to /admin/dashboard is strictly blocked. | [ PASS ] |
| **TC-002** | Auth | Validate Guardian Routing (Admin) | Admin account exists | 1. Navigate to /login<br>2. Enter admin credentials<br>3. Click Submit | System logs user in and redirects directly to /admin/dashboard. | [ PASS ] |
| **TC-003** | CRUD | Form Constraint Validation | Logged in as Admin | 1. Navigate to /admin/staff/create<br>2. Leave all fields blank<br>3. Click Create | Form does not submit. HTML5 validation blocks the action. | [ PASS ] |
| **TC-004** | CRUD | Unique Email Enforcement | Logged in as Admin | 1. Navigate to Add Staff<br>2. Enter an email already in the DB<br>3. Submit | Laravel returns standard error: "The email has already been taken." | [ PASS ] |
| **TC-005** | CRUD | Staff Account Deletion | Logged in as Admin, Staff exists | 1. Navigate to Dashboard<br>2. Click Delete on a staff member<br>3. Confirm browser prompt | Record is permanently removed from the database; success message appears. | [ PASS ] |
