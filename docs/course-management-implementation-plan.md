# Course Management System Implementation Plan

**Purpose**: Comprehensive implementation plan for transforming ERP system into course management platform  
**Last Updated**: 2025-01-27  
**Status**: Planning Phase

## Executive Summary

This document outlines the complete transformation of the existing ERP system into a comprehensive course management platform. The implementation leverages the robust AR/AP workflows, approval processes, and reporting capabilities already in place while adding course-specific functionality.

### Current System Strengths to Leverage

-   ✅ **Complete AR/AP Workflows**: Sales invoices, receipts, purchase invoices, payments
-   ✅ **Approval Processes**: Draft → Posted workflow with role-based permissions
-   ✅ **Comprehensive Reporting**: Aging reports, balance reports, GL detail
-   ✅ **Tax Compliance**: Indonesian PPN handling (11% VAT)
-   ✅ **Professional Document Management**: Auto-numbering, PDF generation
-   ✅ **Role-Based Access Control**: Granular permissions system
-   ✅ **Audit Trails**: Complete transaction history and user attribution

### Critical Gaps to Address

-   ❌ **Course Master Data**: No course, batch, or enrollment management
-   ❌ **Installment Payments**: No multi-payment schedule support
-   ❌ **Trainer Management**: No trainer-specific vendor management
-   ❌ **Revenue Recognition**: No deferred revenue or milestone-based recognition
-   ❌ **Automated Reminders**: No payment reminder system
-   ❌ **Write-off Management**: No bad debt write-off process

## Implementation Phases

### Phase 1: Core Course Management Foundation (3-4 weeks)

#### 1.1 Course Master Data Management

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Course Categories
CREATE TABLE course_categories (
    id BIGINT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id BIGINT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Courses
CREATE TABLE courses (
    id BIGINT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id BIGINT NOT NULL,
    duration_hours INT NOT NULL,
    capacity INT NOT NULL,
    base_price DECIMAL(15,2) NOT NULL,
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Course Batches
CREATE TABLE course_batches (
    id BIGINT PRIMARY KEY,
    course_id BIGINT NOT NULL,
    batch_code VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    schedule TEXT, -- JSON: days, times, locations
    location VARCHAR(255),
    trainer_id BIGINT NULL,
    capacity INT NOT NULL,
    status ENUM('planned', 'ongoing', 'completed', 'cancelled') DEFAULT 'planned',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Enrollments
CREATE TABLE enrollments (
    id BIGINT PRIMARY KEY,
    student_id BIGINT NOT NULL, -- References customers table
    batch_id BIGINT NOT NULL,
    enrollment_date DATE NOT NULL,
    status ENUM('enrolled', 'completed', 'dropped', 'suspended') DEFAULT 'enrolled',
    payment_plan_id BIGINT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration files for all course-related tables
-   [ ] Implement Eloquent models with proper relationships
-   [ ] Create controllers with CRUD operations
-   [ ] Build DataTables-based management interface
-   [ ] Add course management permissions to RolePermissionSeeder
-   [ ] Create course category hierarchy management
-   [ ] Implement batch scheduling and capacity management

#### 1.2 Enhanced Customer Management for Students

**Priority**: P1 (Critical)

**Database Schema Extensions**:

```sql
-- Extend customers table
ALTER TABLE customers ADD COLUMN student_id VARCHAR(50) UNIQUE NULL;
ALTER TABLE customers ADD COLUMN emergency_contact_name VARCHAR(255) NULL;
ALTER TABLE customers ADD COLUMN emergency_contact_phone VARCHAR(50) NULL;
ALTER TABLE customers ADD COLUMN student_status ENUM('active', 'graduated', 'suspended') DEFAULT 'active';
ALTER TABLE customers ADD COLUMN enrollment_count INT DEFAULT 0;
ALTER TABLE customers ADD COLUMN total_paid DECIMAL(15,2) DEFAULT 0;
```

**Implementation Tasks**:

-   [ ] Create migration to extend customers table
-   [ ] Update Customer model with student-specific methods
-   [ ] Enhance CustomerController with student management features
-   [ ] Create student dashboard with enrollment overview
-   [ ] Add student enrollment history tracking
-   [ ] Implement student status management
-   [ ] Create student payment history interface

#### 1.3 Installment Payment System

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Payment Plans
CREATE TABLE payment_plans (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    dp_percentage DECIMAL(5,2) NOT NULL, -- Down payment percentage
    installment_count INT NOT NULL,
    installment_interval_days INT NOT NULL,
    penalty_rate DECIMAL(5,2) DEFAULT 0, -- Daily penalty rate
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Payment Schedules
CREATE TABLE payment_schedules (
    id BIGINT PRIMARY KEY,
    enrollment_id BIGINT NOT NULL,
    payment_plan_id BIGINT NOT NULL,
    due_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
    paid_date DATE NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    penalty_amount DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Overdue Management
CREATE TABLE overdue_management (
    id BIGINT PRIMARY KEY,
    schedule_id BIGINT NOT NULL,
    overdue_days INT NOT NULL,
    penalty_amount DECIMAL(15,2) DEFAULT 0,
    reminder_sent_at TIMESTAMP NULL,
    reminder_count INT DEFAULT 0,
    escalation_level INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration files for payment system tables
-   [ ] Implement PaymentPlanService for automated schedule generation
-   [ ] Create payment tracking and allocation system
-   [ ] Add overdue management and penalty calculation
-   [ ] Integrate with existing Sales Receipt system
-   [ ] Create payment plan management interface
-   [ ] Implement payment schedule monitoring dashboard

### Phase 2: Trainer Management System (2-3 weeks)

#### 2.1 Trainer Master Data

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Trainers
CREATE TABLE trainers (
    id BIGINT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    qualifications TEXT,
    specialties TEXT, -- JSON array
    type ENUM('internal', 'external') NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    vendor_id BIGINT NULL, -- Link to existing vendors table
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Trainer Fee Structures
CREATE TABLE trainer_fee_structures (
    id BIGINT PRIMARY KEY,
    trainer_id BIGINT NOT NULL,
    fee_type ENUM('per_hour', 'per_batch', 'revenue_sharing') NOT NULL,
    rate DECIMAL(10,4) NOT NULL, -- Rate or percentage
    effective_date DATE NOT NULL,
    end_date DATE NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Trainer Performance
CREATE TABLE trainer_performance (
    id BIGINT PRIMARY KEY,
    trainer_id BIGINT NOT NULL,
    batch_id BIGINT NOT NULL,
    completion_rate DECIMAL(5,2) DEFAULT 0,
    student_satisfaction_score DECIMAL(3,2) DEFAULT 0,
    revenue_generated DECIMAL(15,2) DEFAULT 0,
    performance_date DATE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration files for trainer management tables
-   [ ] Implement Trainer, TrainerFeeStructure, TrainerPerformance models
-   [ ] Create TrainerController with CRUD operations
-   [ ] Build trainer management views with DataTables
-   [ ] Add trainer performance tracking dashboard
-   [ ] Create trainer fee structure management interface
-   [ ] Add trainer management permissions to RolePermissionSeeder

#### 2.2 Trainer Payment Processing

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Trainer Payments
CREATE TABLE trainer_payments (
    id BIGINT PRIMARY KEY,
    trainer_id BIGINT NOT NULL,
    batch_id BIGINT NOT NULL,
    payment_type ENUM('hourly', 'batch', 'commission') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'paid', 'cancelled') DEFAULT 'pending',
    paid_date DATE NULL,
    payment_reference VARCHAR(100) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration for trainer payments table
-   [ ] Implement TrainerPaymentService for automated calculations
-   [ ] Create revenue sharing calculation system
-   [ ] Add trainer payment approval workflow
-   [ ] Integrate with existing Purchase Payment system
-   [ ] Create trainer payment history and reporting
-   [ ] Implement automated payment scheduling

### Phase 3: Revenue Recognition Enhancement (2-3 weeks)

#### 3.1 Deferred Revenue Management

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Deferred Revenue
CREATE TABLE deferred_revenue (
    id BIGINT PRIMARY KEY,
    enrollment_id BIGINT NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    earned_amount DECIMAL(15,2) DEFAULT 0,
    unearned_amount DECIMAL(15,2) NOT NULL,
    recognition_date DATE NULL,
    last_recognition_date DATE NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Revenue Recognition Rules
CREATE TABLE revenue_recognition_rules (
    id BIGINT PRIMARY KEY,
    course_id BIGINT NOT NULL,
    recognition_method ENUM('milestone', 'time_based', 'completion') NOT NULL,
    milestone_percentage DECIMAL(5,2) NULL, -- For milestone-based recognition
    recognition_frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'monthly',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration files for deferred revenue tables
-   [ ] Implement RevenueRecognitionService for automated recognition
-   [ ] Create revenue recognition posting to GL
-   [ ] Add deferred revenue reporting and analysis
-   [ ] Integrate with existing PostingService
-   [ ] Create revenue recognition management interface
-   [ ] Implement automated journal posting

#### 3.2 Course Schedule Integration

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Course Milestones
CREATE TABLE course_milestones (
    id BIGINT PRIMARY KEY,
    batch_id BIGINT NOT NULL,
    milestone_name VARCHAR(255) NOT NULL,
    completion_percentage DECIMAL(5,2) NOT NULL,
    due_date DATE NOT NULL,
    completed_date DATE NULL,
    revenue_recognition_amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'completed', 'overdue') DEFAULT 'pending',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration for course milestones table
-   [ ] Implement automated revenue recognition based on milestones
-   [ ] Create milestone tracking and progress monitoring
-   [ ] Add automated journal posting for revenue recognition
-   [ ] Create revenue recognition reports and analytics
-   [ ] Implement milestone management interface

### Phase 4: Automation and Reporting (2-3 weeks)

#### 4.1 Automated Reminders

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Reminder Templates
CREATE TABLE reminder_templates (
    id BIGINT PRIMARY KEY,
    type ENUM('payment_due', 'payment_overdue', 'course_start', 'course_completion') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    frequency_days INT NOT NULL,
    escalation_level INT DEFAULT 1,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Reminder Logs
CREATE TABLE reminder_logs (
    id BIGINT PRIMARY KEY,
    student_id BIGINT NOT NULL,
    template_id BIGINT NOT NULL,
    sent_date TIMESTAMP NOT NULL,
    status ENUM('sent', 'failed', 'bounced') DEFAULT 'sent',
    delivery_method ENUM('email', 'sms', 'both') NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration files for reminder system
-   [ ] Implement NotificationService for automated reminders
-   [ ] Create email/SMS reminder system with escalation workflow
-   [ ] Add customizable reminder templates
-   [ ] Create reminder history and tracking
-   [ ] Implement automated reminder scheduling
-   [ ] Add reminder management interface

#### 4.2 Write-off Management

**Priority**: P1 (Critical)

**Database Schema**:

```sql
-- Write-offs
CREATE TABLE write_offs (
    id BIGINT PRIMARY KEY,
    student_id BIGINT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reason TEXT NOT NULL,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT NULL,
    approved_date DATE NULL,
    rejection_reason TEXT NULL,
    tax_implication_amount DECIMAL(15,2) DEFAULT 0,
    journal_id BIGINT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Implementation Tasks**:

-   [ ] Create migration for write-offs table
-   [ ] Implement write-off approval workflow
-   [ ] Create write-off documentation and audit trail
-   [ ] Add tax implications handling for write-offs
-   [ ] Integrate with existing GL for write-off posting
-   [ ] Create write-off management interface
-   [ ] Implement write-off reporting

#### 4.3 Enhanced Reporting

**Priority**: P1 (Critical)

**Implementation Tasks**:

-   [ ] Create CourseReportsController with comprehensive analytics
-   [ ] Implement course revenue reports by course, batch, trainer
-   [ ] Create student payment reports with status tracking
-   [ ] Add trainer performance reports with metrics
-   [ ] Create cash flow projections based on payment schedules
-   [ ] Add export capabilities (CSV/PDF/Excel) for all course reports
-   [ ] Implement dashboard widgets for course management
-   [ ] Create comprehensive business intelligence reports

### Phase 5: Advanced Features (3-4 weeks)

#### 5.1 Advanced Analytics

**Priority**: P2 (Important)

**Implementation Tasks**:

-   [ ] Implement student lifetime value calculations
-   [ ] Create course profitability analysis
-   [ ] Add trainer efficiency metrics
-   [ ] Implement predictive analytics for revenue and cash flow
-   [ ] Create comprehensive business intelligence dashboards
-   [ ] Add machine learning for enrollment prediction
-   [ ] Implement advanced reporting with drill-down capabilities

#### 5.2 Integration Features

**Priority**: P2 (Important)

**Implementation Tasks**:

-   [ ] Create LMS integration capabilities
-   [ ] Implement payment gateway integration
-   [ ] Add communication automation (email/SMS)
-   [ ] Create calendar integration for course schedules
-   [ ] Add external system synchronization
-   [ ] Implement API endpoints for third-party integrations
-   [ ] Create webhook system for real-time updates

## Technical Implementation Strategy

### Database Design Principles

1. **Leverage Existing Schema**: Extend existing tables where possible (customers → students)
2. **Maintain Referential Integrity**: Use foreign keys and proper relationships
3. **Support Scalability**: Design for high-volume course management
4. **Ensure Audit Trail**: Track all changes with timestamps and user attribution

### Service Layer Architecture

1. **CourseManagementService**: Core business logic for course operations
2. **PaymentPlanService**: Automated payment schedule generation
3. **TrainerPaymentService**: Trainer payment calculations and processing
4. **RevenueRecognitionService**: Automated revenue recognition
5. **NotificationService**: Automated reminder and communication system

### Integration Points

1. **Existing AR System**: Extend for installment payments
2. **Existing AP System**: Extend for trainer payments
3. **Existing GL System**: Integrate revenue recognition
4. **Existing Reporting**: Extend for course analytics
5. **Existing Approval Workflows**: Maintain separation of duties

## Success Criteria

### Functional Requirements

-   [ ] Complete course lifecycle management (enrollment to completion)
-   [ ] Automated installment payment processing
-   [ ] Comprehensive trainer management and payment processing
-   [ ] Proper revenue recognition with deferred revenue handling
-   [ ] Automated reminder system for overdue payments
-   [ ] Professional write-off management with approval workflows
-   [ ] Comprehensive reporting and analytics

### Performance Requirements

-   [ ] System response times remain acceptable (< 2 seconds)
-   [ ] Batch processing handles large course portfolios
-   [ ] Export functions complete within reasonable timeframes
-   [ ] Automated processes run efficiently without blocking

### Compliance Requirements

-   [ ] Indonesian tax compliance (PPN, PPh)
-   [ ] Proper audit trails for all transactions
-   [ ] Role-based access control maintained
-   [ ] Data integrity and backup procedures

## Risk Mitigation

### Technical Risks

1. **Database Performance**: Implement proper indexing and query optimization
2. **Integration Complexity**: Use existing patterns and maintain consistency
3. **Data Migration**: Plan carefully for existing data migration

### Business Risks

1. **User Adoption**: Provide comprehensive training and documentation
2. **Process Changes**: Implement gradually with user feedback
3. **Compliance**: Ensure all changes maintain regulatory compliance

## Timeline and Resource Allocation

### Phase 1: Foundation (3-4 weeks)

-   **Week 1-2**: Database design and core models
-   **Week 3-4**: User interface and basic functionality

### Phase 2: Trainer Management (2-3 weeks)

-   **Week 1-2**: Trainer data management
-   **Week 3**: Payment processing integration

### Phase 3: Revenue Recognition (2-3 weeks)

-   **Week 1-2**: Deferred revenue implementation
-   **Week 3**: Automated recognition system

### Phase 4: Automation (2-3 weeks)

-   **Week 1**: Reminder system
-   **Week 2**: Write-off management
-   **Week 3**: Enhanced reporting

### Phase 5: Advanced Features (3-4 weeks)

-   **Week 1-2**: Advanced analytics
-   **Week 3-4**: Integration features

**Total Estimated Duration**: 12-17 weeks

## Conclusion

This implementation plan transforms the existing ERP system into a comprehensive course management platform while leveraging the robust financial management capabilities already in place. The phased approach ensures continuous value delivery while maintaining system stability and user adoption.

The system will provide enterprise-grade course management with:

-   Complete student lifecycle management
-   Automated payment processing with installment support
-   Comprehensive trainer management and payment processing
-   Proper revenue recognition with deferred revenue handling
-   Automated business processes and comprehensive reporting
-   Advanced analytics and integration capabilities

This transformation positions the ERP system as a complete business management solution for educational institutions and training organizations.
