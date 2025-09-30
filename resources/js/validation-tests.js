/**
 * Comprehensive Form Validation Testing Suite
 * For ERP System Forms
 */

class FormValidationTester {
    constructor() {
        this.results = [];
    }

    /**
     * Test form for required field validation
     */
    testRequiredFields(form, requiredFields) {
        const result = {
            test: "Required Fields Validation",
            form: form.tagName,
            passed: true,
            errors: [],
        };

        requiredFields.forEach((fieldName) => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field || !field.hasAttribute("required")) {
                result.passed = false;
                result.errors.push(
                    `Field ${fieldName} missing or not marked as required`
                );
            }
        });

        return result;
    }

    /**
     * Test a specific form element's validation
     */
    testFieldValidation(field, expectedRules) {
        const result = {
            test: "Field Validation",
            field: field.name || field.id,
            passed: true,
            errors: [],
        };

        // Test required attribute
        if (expectedRules.required && !field.hasAttribute("required")) {
            result.passed = false;
            result.errors.push("Missing required attribute");
        }

        // Test min/max values for numeric fields
        if (expectedRules.min && parseFloat(field.min) !== expectedRules.min) {
            result.passed = false;
            result.errors.push(
                `Min value should be ${expectedRules.min}, found ${field.min}`
            );
        }

        if (expectedRules.max && parseFloat(field.max) !== expectedRules.max) {
            result.passed = false;
            result.errors.push(
                `Max value should be ${expectedRules.max}, found ${field.max}`
            );
        }

        // Test step value for numeric fields
        if (
            expectedRules.step &&
            parseFloat(field.step) !== expectedRules.step
        ) {
            result.passed = false;
            result.errors.push(
                `Step value should be ${expectedRules.step}, found ${field.step}`
            );
        }

        return result;
    }

    /**
     * Test Sales Order form
     */
    testSalesOrderForm() {
        const form = document.getElementById("so-form");
        const tests = [];

        // Test required fields
        const requiredFields = ["date", "customer_id"];
        tests.push(this.testRequiredFields(form, requiredFields));

        // Test line items
        const lineElements = form.querySelectorAll(".so-line-row");
        lineElements.forEach((line, index) => {
            const qtyField = line.querySelector('[name*="[qty]"]');
            const priceField = line.querySelector('[name*="[unit_price]"]');
            const vatAmountField = line.querySelector('[name*="[vat_amount]"]');
            const wtaxAmountField = line.querySelector(
                '[name*="[wtax_amount]"]'
            );

            // Test qty field
            if (qtyField) {
                tests.push(
                    this.testFieldValidation(qtyField, {
                        required: true,
                        min: 0.01,
                    })
                );
            }

            // Test price field
            if (priceField) {
                tests.push(
                    this.testFieldValidation(priceField, {
                        required: true,
                        min: 0,
                    })
                );
            }

            // Test hidden fields exist
            if (!vatAmountField) {
                tests.push({
                    test: "Hidden VAT Amount Field",
                    passed: false,
                    errors: ["vat_amount hidden field missing"],
                });
            }

            if (!wtaxAmountField) {
                tests.push({
                    test: "Hidden WTax Amount Field",
                    passed: false,
                    errors: ["wtax_amount hidden field missing"],
                });
            }
        });

        return tests;
    }

    /**
     * Test Purchase Order form
     */
    testPurchaseOrderForm() {
        const form = document.querySelector("form");
        const tests = [];

        // Test required fields
        const requiredFields = ["date", "vendor_id"];
        tests.push(this.testRequiredFields(form, requiredFields));

        // Test line items with hidden fields
        const lineElements = form.querySelectorAll("tr");
        lineElements.forEach((line, index) => {
            // Skip header row
            if (index === 0) return;

            const vatAmountField = line.querySelector('[name*="[vat_amount]"]');
            const wtaxAmountField = line.querySelector(
                '[name*="[wtax_amount]"]'
            );

            // Test hidden fields exist
            if (!vatAmountField) {
                tests.push({
                    test: "Hidden VAT Amount Field",
                    passed: false,
                    errors: ["vat_amount hidden field missing"],
                });
            }

            if (!wtaxAmountField) {
                tests.push({
                    test: "Hidden WTax Amount Field",
                    passed: false,
                    errors: ["wtax_amount hidden field missing"],
                });
            }
        });

        return tests;
    }

    /**
     * Test Journal form balance validation
     */
    testJournalForm() {
        const form = document.querySelector("form");
        const tests = [];

        // Test debit/credit balance
        let totalDebit = 0;
        let totalCredit = 0;

        form.querySelectorAll('[name*="[debit]"]').forEach((field) => {
            totalDebit += parseFloat(field.value) || 0;
        });

        form.querySelectorAll('[name*="[credit]"]').forEach((field) => {
            totalCredit += parseFloat(field.value) || 0;
        });

        const balanceTest = {
            test: "Journal Balance Validation",
            passed: Math.abs(totalDebit - totalCredit) < 0.01,
            errors: [],
        };

        if (!balanceTest.passed) {
            balanceTest.errors.push(
                `Debits (${totalDebit}) do not equal Credits (${totalCredit})`
            );
        }

        tests.push(balanceTest);

        return tests;
    }

    /**
     * Run all validation tests
     */
    async runAllTests() {
        console.log("🚀 Running ERP Form Validation Tests");

        // Detect current form type and run appropriate tests
        const path = window.location.pathname;

        if (path.includes("sales-orders/create")) {
            this.results = this.testSalesOrderForm();
        } else if (path.includes("purchase-orders/create")) {
            this.results = this.testPurchaseOrderForm();
        } else if (path.includes("journals")) {
            this.results = this.testJournalForm();
        }

        // Display results
        this.displayResults();
    }

    /**
     * Display test results
     */
    displayResults() {
        console.group("🧪 Form Validation Test Results");

        let totalTests = 0;
        let passedTests = 0;

        this.results.forEach((result, index) => {
            totalTests++;

            if (result.passed) {
                passedTests++;
                console.log(`✅ ${result.test} - PASSED`);
            } else {
                console.error(`❌ ${result.test} - FAILED`);
                result.errors.forEach((error) => {
                    console.error(`   - ${error}`);
                });
            }
        });

        console.log(`\n📊 Summary: ${passedTests}/${totalTests} tests passed`);

        if (passedTests === totalTests) {
            console.log("🎉 All validation tests passed!");
        } else {
            console.warn(
                "⚠️ Some validation tests failed. Please review the errors above."
            );
        }

        console.groupEnd();
    }

    /**
     * Quick validation test for form submission
     */
    testFormSubmission(form) {
        const invalidFields = [];

        form.querySelectorAll(
            "input[required], select[required], textarea[required]"
        ).forEach((field) => {
            if (!field.value.trim()) {
                invalidFields.push({
                    name: field.name || field.id,
                    type: field.type,
                    required: true,
                });
            }
        });

        // Test numeric fields
        form.querySelectorAll('input[type="number"]').forEach((field) => {
            const value = parseFloat(field.value);

            if (field.hasAttribute("min") && value < parseFloat(field.min)) {
                invalidFields.push({
                    name: field.name || field.id,
                    type: "number",
                    error: `Value ${value} is below minimum ${field.min}`,
                });
            }

            if (field.hasAttribute("max") && value > parseFloat(field.max)) {
                invalidFields.push({
                    name: field.name || field.id,
                    type: "number",
                    error: `Value ${value} is above maximum ${field.max}`,
                });
            }
        });

        return {
            isValid: invalidFields.length === 0,
            invalidFields: invalidFields,
        };
    }
}

// Auto-run validation tests when script loads
document.addEventListener("DOMContentLoaded", function () {
    // Only run tests on development/test pages
    if (
        window.location.hostname === "localhost" ||
        window.location.hostname.includes("test")
    ) {
        window.formValidator = new FormValidationTester();
        window.formValidator.runAllTests();
    }
});

// Export for manual testing
window.FormValidationTester = FormValidationTester;
