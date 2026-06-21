// Cypress E2E Test Suite: E-Services Portal
describe('Complete Portal Flow', () => {
    
    beforeEach(() => {
        // Reset database to a fresh state before each test
        cy.exec('php artisan migrate:fresh --seed');
    });

    it('Registers a new Citizen and routes them to the Citizen Home', () => {
        cy.visit('/register');
        cy.get('input[name=first_name]').type('Sarah');
        cy.get('input[name=last_name]').type('Connor');
        cy.get('input[name=id_document]').type('LB-998877');
        cy.get('input[name=email]').type('sarah@example.com');
        cy.get('input[name=password]').type('SecurePass123!');
        cy.get('input[name=password_confirmation]').type('SecurePass123!');
        cy.get('button[type=submit]').click();

        // Verify Guardian Middleware routes correctly
        cy.url().should('include', '/home');
        cy.contains('Welcome back, Sarah');
    });

    it('Allows an Admin to log in and manage Staff CRUD', () => {
        cy.visit('/login');
        cy.get('input[name=email]').type('test@example.com'); // Admin from Seeder
        cy.get('input[name=password]').type('password');
        cy.get('button[type=submit]').click();

        // Verify Guardian Middleware routes Admin correctly
        cy.url().should('include', '/admin/dashboard');
        
        // Test Create flow
        cy.contains('+ Add New Staff').click();
        cy.get('input[name=first_name]').type('John');
        cy.get('input[name=last_name]').type('Doe');
        cy.get('input[name=email]').type('johndoe@gov.lb');
        cy.get('input[name=password]').type('StaffPass123!');
        cy.get('input[name=password_confirmation]').type('StaffPass123!');
        cy.get('button[type=submit]').click();

        // Verify Success
        cy.url().should('include', '/admin/dashboard');
        cy.contains('New staff member added successfully!');
        cy.contains('johndoe@gov.lb'); 
    });
});
