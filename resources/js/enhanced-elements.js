/**
 * Enhanced ERP Elements Library
 * Shared functionality for improved Item/Account display and currency formatting
 *
 * Usage in Blade templates:
 * @include('assets.enhanced-elements')
 */

// Enhanced Item/Account Selector Functions
window.EnhancedElements = {
    // Format currency display for Indonesian Rupiah
    formatCurrency: function (amount) {
        return `Rp ${parseFloat(amount).toLocaleString("id-ID", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    },

    // Update amount display in table rows
    updateAmountDisplay: function (row, amount) {
        const displayElement = row.find(".amount-display");
        if (displayElement.length) {
            displayElement.text(this.formatCurrency(amount));
        }
        // Update hidden input for form submission
        row.find(".amount-input").val(parseFloat(amount).toFixed(2));
    },

    // Show account selector modal for services
    showAccountSelector: function (callback) {
        const modal = `
            <div class="modal fade" id="accountModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select Account</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Account Name</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${(window.accounts || [])
                                            .map(
                                                (account) => `
                                            <tr>
                                                <td class="font-monospace">${account.code}</td>
                                                <td>${account.name}</td>
                                                <td><span class="badge badge-info">${account.type}</span></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary select-account-btn" 
                                                        data-code="${account.code}" data-name="${account.name}" data-id="${account.id}">
                                                        Select
                                                    </button>
                                                </td>
                                            </tr>
                                        `
                                            )
                                            .join("")}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if it exists
        $("#accountModal").remove();
        $("body").append(modal);

        $("#accountModal").modal("show");

        $("#accountModal").on("click", ".select-account-btn", function () {
            const account = {
                id: $(this).data("id"),
                code: $(this).data("code"),
                name: $(this).data("name"),
            };
            $("#accountModal").modal("hide");
            callback(account);
        });
    },

    // Enhanced item selection handler
    handleItemSelection: function (item, row) {
        row.find(".item-account-id").val(item.id);
        row.find(".item-display").val(item.name);
        row.find(".item-code-name-display").text(`${item.code} - ${item.name}`);
        row.find(".item-id-input").val(item.id);
        row.find(".account-id-input").val(""); // Clear account if switching to item
        if (row.find(".description-input").val() === "") {
            row.find(".description-input").val(item.description || item.name);
        }
        if (
            row.find(".price-input").val() === "" ||
            row.find(".price-input").val() === "0"
        ) {
            row.find(".price-input").val(item.last_cost_price || 0);
        }
    },

    // Enhanced account selection handler
    handleAccountSelection: function (account, row) {
        row.find(".item-account-id").val(account.id);
        row.find(".item-display").val(account.name);
        row.find(".item-code-name-display").text(
            `${account.code} - ${account.name}`
        );
        row.find(".account-id-input").val(account.id);
        row.find(".item-id-input").val(""); // Clear item if switching to service
        if (row.find(".description-input").val() === "") {
            row.find(".description-input").val(account.name);
        }
    },

    // Initialize enhanced row functionality
    enhanceRow: function (row) {
        // Handle item selection
        row.find(".select-item-btn")
            .off("click")
            .on("click", function () {
                const lineType = row.find(".line-type-select").val();

                if (lineType === "item") {
                    window.itemSelector.open(function (item) {
                        window.EnhancedElements.handleItemSelection(item, row);

                        // Trigger update calculation
                        if (typeof updateLineAmount === "function") {
                            updateLineAmount(row);
                        }
                        if (typeof updateTotals === "function") {
                            updateTotals();
                        }
                    });
                } else {
                    window.EnhancedElements.showAccountSelector(function (
                        account
                    ) {
                        window.EnhancedElements.handleAccountSelection(
                            account,
                            row
                        );

                        // Trigger update calculation
                        if (typeof updateLineAmount === "function") {
                            updateLineAmount(row);
                        }
                        if (typeof updateTotals === "function") {
                            updateTotals();
                        }
                    });
                }
            });

        // Handle line type change
        row.find(".line-type-select")
            .off("change")
            .on("change", function () {
                const lineType = $(this).val();
                const itemDisplay = row.find(".item-display");
                const selectBtn = row.find(".select-item-btn");
                const codeDisplay = row.find(".item-code-name-display");

                if (lineType === "item") {
                    itemDisplay.attr("placeholder", "Click to select item");
                    selectBtn.attr("title", "Select Item");
                } else if (lineType === "service") {
                    itemDisplay.attr("placeholder", "Click to select account");
                    selectBtn.attr("title", "Select Account");
                    codeDisplay.text(""); // Clear displayed code/name
                }
            });
    },

    // Initialize enhanced table functionality
    enhanceTable: function (tableId) {
        const table = $(`#${tableId}`);

        // Auto-enhance all existing rows
        table.find("tbody tr").each(function () {
            window.EnhancedElements.enhanceRow($(this));
        });

        // Auto-enhance new rows when added
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === "childList") {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1 && node.tagName === "TR") {
                            // Element node and TR tag
                            window.EnhancedElements.enhanceRow($(node));
                        }
                    });
                }
            });
        });

        observer.observe(table[0], {
            childList: true,
            subtree: true,
        });
    },
};
