# Task Backlog

## How to Use This File

Tasks are listed in priority order (P0 > P1 > P2). When starting a new task, select the highest priority task with the lowest ID.

## Task States
- `backlog` - Task is defined but not ready to start
- `ready` - Task is ready to be worked on
- `in_progress` - Task is currently being worked on
- `review` - Task is complete and awaiting review
- `done` - Task is completed
- `blocked` - Task is blocked (must include blocker note)

---

## Tasks

### Task 20251130-001: Verify Export Functionality
**Task ID:** 20251130-001  
**Title:** Verify Export Functionality for Bills  
**Type:** feature  
**Priority:** P0  
**State:** ready  
**Owner:** agent

**Summary**
- Check if export functionality for bills exists in the codebase
- Review routes, controllers, and Vue components related to export
- Document current state and identify what needs to be implemented

**Acceptance Criteria**
- [ ] Searched codebase for export functionality
- [ ] Reviewed routes/web.php for export routes
- [ ] Checked controllers for export methods
- [ ] Reviewed Vue components for export interface
- [ ] Documented findings in progress tracking

**Dependencies**
- Read existing codebase in `~/www/compenzations`
- Review routes, controllers, and Vue components

---

### Task 20251130-002: Expand Post Numbers Seeder
**Task ID:** 20251130-002  
**Title:** Expand Post Numbers Seeder with All Slovenian Postal Codes  
**Type:** chore  
**Priority:** P1  
**State:** ready  
**Owner:** agent

**Summary**
- Add all Slovenian postal codes to PostNumberSeeder
- Either import from legacy system or use official postal code data
- Ensure all postal codes are properly formatted

**Acceptance Criteria**
- [ ] PostNumberSeeder contains comprehensive postal code data
- [ ] All major Slovenian cities/towns included
- [ ] Data is properly formatted and validated
- [ ] Seeder runs without errors

**Dependencies**
- Access to legacy post_numbers table or official postal code data

---

### Task 20251130-003: Implement PDF Generation
**Task ID:** 20251130-003  
**Title:** Implement PDF Generation for Compensation Proposals  
**Type:** feature  
**Priority:** P0  
**State:** ready  
**Owner:** agent

**Summary**
- Install PDF generation package (Spatie/Laravel-PDF or DomPDF)
- Create PDF generation job
- Implement PDF generation for compensation proposals
- Store PDFs using Laravel Storage

**Acceptance Criteria**
- [ ] PDF generation package installed
- [ ] GeneratePdfJob created and working
- [ ] PDFs generated and stored correctly
- [ ] PDF generation tested and working

**Dependencies**
- CompensationProposal model exists
- Laravel Storage configured
- Queue system configured (if using async)

---

*Add more tasks below as needed. Use the task-master-template.mdc format.*

