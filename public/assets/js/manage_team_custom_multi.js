$(document).ready(function () {
    // Function to handle custom select behavior for a single selection
    function initCustomSelectSingle(customSelectId) {
        let isLoading = false;
        let debounceTimer = null;

        // Function to load options from the server
        function loadOptionsLeader(customSelect, searchTerm = "", page = 1) {
            const optionsContainer = customSelect.find(".options");
            const noResultMessage = customSelect.find(".no-result-message");

            optionsContainer.append('<div class="loading">Loading...</div>');

            return $.ajax({
                url: "/assigned-leave-approvals/get_users", // Replace with your actual endpoint
                type: "GET",
                data: { searchTerm, page },
                success: function (data) {
                    optionsContainer.find(".loading").remove();

                    if (data.data && data.data.length > 0) {
                        data.data.forEach((item) => {
                            const isSelected = customSelect
                                .find(`.selected-options .tag[data-value="${item.id}"]`)
                                .length > 0; // Check if the item is already selected

                            const optionElement = $('<div class="option">')
                                .text(item.username)
                                .attr("data-value", item.id)
                                .toggleClass("active", isSelected) // Retain selected state
                                .click(function (e) {
                                    customSelect.find(".option").removeClass("active"); // Remove active class from all options
                                    $(this).addClass("active"); // Add active class to clicked option

                                    // Remove any previously selected tags
                                    customSelect.find(".selected-options .tag").remove();

                                    // Add the newly selected tag
                                    const tagHTML = `
                                <span class="tag" data-value="${item.id}">
                                    ${item.username}
                                    <span class="remove-tag" data-value="${item.id}">&times;</span>
                                </span>`;
                                    customSelect.find(".selected-options").append(tagHTML);

                                    customSelect.find(".tags_input").val(item.id); // Store only the selected user's ID

                                    e.stopPropagation();
                                });

                            optionsContainer.append(optionElement);
                        });

                        noResultMessage.hide();
                    } else if (page === 1) {
                        noResultMessage.show();
                    }

                    customSelect.addClass("open");
                    customSelect.find(".search-tags").focus();
                },
                error: function () {
                    optionsContainer.find(".loading").remove();
                    alert("An error occurred while fetching options.");
                },
            });
        }

        // Open/close dropdown when the select box is clicked
        $("#" + customSelectId).click(function (e) {
            // Prevent closing if the click is inside the tags or remove button
            if (
                $(e.target).closest(".remove-tag").length ||
                $(e.target).closest(".tag").length
            ) {
                return; // Don't toggle dropdown if clicking on the "x" button or inside the tag
            }

            // Toggle the open class to show/hide the dropdown
            $("#" + customSelectId).toggleClass("open");

            // Stop the event from propagating to avoid closing on click outside
            e.stopPropagation();
        });

        // Handle the scroll event to load more options when scrolling to the bottom
        $("#" + customSelectId + " .options").scroll(function () {
            const optionsContainer = $(this);
            const scrollTop = optionsContainer.scrollTop(); // Get current scroll position
            const scrollHeight = optionsContainer[0].scrollHeight;
            const containerHeight = optionsContainer.outerHeight();
            const threshold = 100;

            if (scrollHeight - (scrollTop + containerHeight) <= threshold && !isLoading) {
                const customSelect = $("#" + customSelectId);
                const searchTerm = customSelect.find(".search-tags").val();
                let currentPage = customSelect.data("current-page") || 1;

                isLoading = true;
                currentPage++;
                customSelect.data("current-page", currentPage);

                // Append data and maintain scroll position
                loadOptionsLeader(customSelect, searchTerm, currentPage).then(() => {
                    isLoading = false;

                    // Maintain the scroll position by keeping the scrollTop value
                    optionsContainer.scrollTop(scrollTop);
                });
            }
        });

        // Handle the input event for search and filtering options
        $("#" + customSelectId + " .search-tags").on("input", function () {
            const searchTerm = $(this).val().trim();
            const customSelect = $("#" + customSelectId);

            if (debounceTimer) clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                customSelect.data("current-page", 1);
                const optionsContainer = customSelect.find(".options");
                optionsContainer.find(".option").remove(); // Clear current options (not selected ones)
                loadOptionsLeader(customSelect, searchTerm, 1);
            }, 300);
        });

        $("#" + customSelectId + " .search-tags").click(function (e) {
            e.stopPropagation();
        });

        // Remove selected option when clicking the "x" button
        $(document).on(
            "click",
            "#" + customSelectId + " .remove-tag",
            function (e) {
                e.stopImmediatePropagation();
                e.preventDefault();

                const customSelect = $("#" + customSelectId);

                const valueToRemove = $(this).data("value");

                customSelect.find(`.selected-options .tag[data-value="${valueToRemove}"]`).remove();
                customSelect.find(`.option[data-value="${valueToRemove}"]`).removeClass("active");
                customSelect.find(".tags_input").val(""); // Clear the input value
            }
        );

        // Close dropdown when clicking anywhere outside
        $(document).click(function (e) {
            if (!$(e.target).closest(".custom-select").length) {
                $("#" + customSelectId).removeClass("open");
            }
        });

        $("#" + customSelectId).each(function () {
            loadOptionsLeader($(this), "", 1);
        });

        // Clear search box when clicking the clear button
        $("#" + customSelectId + " .clear").on("click", function () {
            const searchInput = $(this)
                .closest(".custom-select")
                .find(".search-tags");

            searchInput.val(""); // Clear the search field
            searchInput.trigger("input"); // Trigger the input event to update the options list
        });
    }

    // Initialize the first-leader-select field with the single selection behavior
    initCustomSelectSingle("first-leader-select");
});




//MultiSelect

$(document).ready(function () {
    let isLoading = false;
    let debounceTimer = null;

    // Function to handle custom select behavior
    function initCustomSelect(customSelectId) {
        let selectedOptions = []; // Store selected options for each select instance

        // Open/close dropdown when the select box is clicked
        $("#" + customSelectId).click(function (e) {
            // Prevent closing if the click is inside the tags or remove button
            if (
                $(e.target).closest(".remove-tag").length ||
                $(e.target).closest(".tag").length
            ) {
                return; // Don't toggle dropdown if clicking on the "x" button or inside the tag
            }

            // Toggle the open class to show/hide the dropdown
            $("#" + customSelectId).toggleClass("open");

            // Stop the event from propagating to avoid closing on click outside
            e.stopPropagation();
        });

        // Handle the scroll event to load more options when scrolling to the bottom
        $("#" + customSelectId + " .options").scroll(function () {
            const optionsContainer = $(this);
            const scrollTop = optionsContainer.scrollTop(); // Get current scroll position
            const scrollHeight = optionsContainer[0].scrollHeight;
            const containerHeight = optionsContainer.outerHeight();
            const threshold = 100;

            if (scrollHeight - (scrollTop + containerHeight) <= threshold && !isLoading) {
                const customSelect = $("#" + customSelectId);
                const searchTerm = customSelect.find(".search-tags").val();
                let currentPage = customSelect.data("current-page") || 1;

                isLoading = true;
                currentPage++;
                customSelect.data("current-page", currentPage);

                // Append data and maintain scroll position
                loadOptionsTeam(customSelect, searchTerm, currentPage).then(() => {
                    isLoading = false;

                    // Maintain the scroll position by keeping the scrollTop value
                    optionsContainer.scrollTop(scrollTop);
                });
            }
        });

        // Handle the input event for search and filtering options
        $("#" + customSelectId + " .search-tags").on("input", function () {
            const searchTerm = $(this).val().trim();
            const customSelect = $("#" + customSelectId);

            if (debounceTimer) clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                customSelect.data("current-page", 1);
                const optionsContainer = customSelect.find(".options");
                optionsContainer.find(".option").remove(); // Clear current options (not selected ones)
                loadOptionsTeam(customSelect, searchTerm, 1);
            }, 300);
        });

        $("#" + customSelectId + " .search-tags").click(function (e) {
            e.stopPropagation();
        });


        function loadOptionsTeam(customSelect, searchTerm = "", page = 1) {
            const optionsContainer = customSelect.find(".options");
            const noResultMessage = customSelect.find(".no-result-message");

            optionsContainer.append('<div class="loading">Loading...</div>');

            return $.ajax({
                url: "/assigned-leave-approvals/get_users", // Replace with your actual endpoint
                type: "GET",
                data: { searchTerm, page },
                success: function (data) {
                    optionsContainer.find(".loading").remove();

                    if (data.data && data.data.length > 0) {
                        data.data.forEach((item) => {
                            const isSelected = customSelect
                                .find(`.selected-options .tag[data-value="${item.id}"]`)
                                .length > 0; // Check if the item is already selected

                            const optionElement = $('<div class="option">')
                                .text(item.username)
                                .attr("data-value", item.id)
                                .toggleClass("active", isSelected) // Retain selected state
                                .click(function (e) {
                                    $(this).toggleClass("active");

                                    const currentValues = customSelect
                                        .find(".tags_input")
                                        .val()
                                        .split(',')
                                        .filter(Boolean);

                                    if ($(this).hasClass("active")) {
                                        // Append to selectedOptions if not already there
                                        if (!currentValues.includes(item.id.toString())) {
                                            currentValues.push(item.id.toString());

                                            // Append the tag to the UI
                                            const tagHTML = `
                                        <span class="tag" data-value="${item.id}">
                                            ${item.username}
                                            <span class="remove-tag" data-value="${item.id}">&times;</span>
                                        </span>`;
                                            customSelect.find(".selected-options").append(tagHTML);
                                        }
                                    } else {
                                        // Remove from selectedOptions
                                        customSelect
                                            .find(`.selected-options .tag[data-value="${item.id}"]`)
                                            .remove();
                                        const index = currentValues.indexOf(item.id.toString());
                                        if (index > -1) currentValues.splice(index, 1);
                                    }

                                    customSelect.find(".tags_input").val(currentValues.join(","));
                                    e.stopPropagation();
                                });

                            optionsContainer.append(optionElement);
                        });

                        noResultMessage.hide();
                    } else if (page === 1) {
                        noResultMessage.show();
                    }

                    customSelect.addClass("open");
                    customSelect.find(".search-tags").focus();
                },
                error: function () {
                    optionsContainer.find(".loading").remove();
                    alert("An error occurred while fetching options.");
                },
            });
        }


        // Function to update the selected options in the UI
        function updateSelectedOptions(customSelect) {
            let tagsHTML = "";
            selectedOptions.forEach(function (opt, index) {
                if (index < 4) {
                    // Ensure data-value is set properly here
                    tagsHTML += `<span class="tag" data-value="${opt.id}">${opt.username}<span class="remove-tag" data-value="${opt.id}">&times;</span></span>`;
                }
            });

            customSelect.find(".selected-options").html(tagsHTML);

            const selectedValues = selectedOptions.map((opt) => opt.id);
            customSelect.find(".tags_input").val(selectedValues.join(", "));
        }

        // Remove selected option when clicking the "x" button
        $(document).on(
            "click",
            "#" + customSelectId + " .remove-tag",
            function (e) {
                e.stopImmediatePropagation();
                e.preventDefault();

                const customSelect = $("#" + customSelectId);

                const valueToRemove = $(this).data("value");

                selectedOptions = selectedOptions.filter(
                    (opt) => opt.id != valueToRemove
                );

                customSelect
                    .find(
                        `.selected-options .remove-tag[data-value="${valueToRemove}"]`
                    )
                    .parent()
                    .remove();

                customSelect
                    .find(`.option[data-value="${valueToRemove}"]`)
                    .removeClass("active");

                updateSelectedOptions(customSelect);
            }
        );

        // Close dropdown when clicking anywhere outside
        $(document).click(function (e) {
            // Ensure the click is outside of the custom select
            if (!$(e.target).closest(".custom-select").length) {
                $("#" + customSelectId).removeClass("open");
            }
        });

        $("#" + customSelectId).each(function () {
            loadOptionsTeam($(this), "", 1);
        });

        // Clear search box when clicking the clear button
        $("#" + customSelectId + " .clear").on("click", function () {
            const searchInput = $(this)
                .closest(".custom-select")
                .find(".search-tags");

            searchInput.val(""); // Clear the search field
            searchInput.trigger("input"); // Trigger the input event to update the options list
        });
    }

    initCustomSelect("second-employee-select");


});



////EDIT AND UPDATE Search BACKEND

$(document).ready(function () {
    // Function to handle custom select behavior for single selection
    function initCustomSelectSingle(customSelectId) {
        let isLoading = false;
        let debounceTimer = null;

        // Function to load options from the server
        function loadOptionsLeader(customSelect, searchTerm = "", page = 1) {
            const optionsContainer = customSelect.find(".options");
            const noResultMessage = customSelect.find(".no-result-message");

            optionsContainer.append('<div class="loading">Loading...</div>');

            return $.ajax({
                url: "/assigned-leave-approvals/get_users", // Replace with your actual endpoint
                type: "GET",
                data: { searchTerm, page },
                success: function (data) {
                    optionsContainer.find(".loading").remove();

                    if (data.data && data.data.length > 0) {
                        data.data.forEach((item) => {
                            const isSelected = customSelect
                                .find(`.selected-options .tag[data-value="${item.id}"]`)
                                .length > 0; // Check if the item is already selected

                            const optionElement = $('<div class="option">')
                                .text(item.username)
                                .attr("data-value", item.id)
                                .toggleClass("active", isSelected) // Retain selected state
                                .click(function (e) {
                                    customSelect.find(".option").removeClass("active"); // Remove active class from all options
                                    $(this).addClass("active"); // Add active class to clicked option

                                    // Remove any previously selected tags
                                    customSelect.find(".selected-options .tag").remove();

                                    // Add the newly selected tag
                                    const tagHTML = `
                                    <span class="tag" data-value="${item.id}">
                                        ${item.username}
                                        <span class="remove-tag" data-value="${item.id}">&times;</span>
                                    </span>`;
                                    customSelect.find(".selected-options").append(tagHTML);

                                    customSelect.find(".tags_input").val(item.id); // Store only the selected user's ID

                                    e.stopPropagation();
                                });

                            optionsContainer.append(optionElement);
                        });

                        noResultMessage.hide();
                    } else if (page === 1) {
                        noResultMessage.show();
                    }

                    customSelect.addClass("open");
                    customSelect.find(".search-tags").focus();
                },
                error: function () {
                    optionsContainer.find(".loading").remove();
                    alert("An error occurred while fetching options.");
                },
            });
        }

        // Open/close dropdown when the select box is clicked
        $("#" + customSelectId).click(function (e) {
            // Prevent closing if the click is inside the tags or remove button
            if (
                $(e.target).closest(".remove-tag").length ||
                $(e.target).closest(".tag").length
            ) {
                return; // Don't toggle dropdown if clicking on the "x" button or inside the tag
            }

            // Toggle the open class to show/hide the dropdown
            $("#" + customSelectId).toggleClass("open");

            // Stop the event from propagating to avoid closing on click outside
            e.stopPropagation();
        });

        // Handle the scroll event to load more options when scrolling to the bottom
        $("#" + customSelectId + " .options").scroll(function () {
            const optionsContainer = $(this);
            const scrollTop = optionsContainer.scrollTop(); // Get current scroll position
            const scrollHeight = optionsContainer[0].scrollHeight;
            const containerHeight = optionsContainer.outerHeight();
            const threshold = 100;

            if (scrollHeight - (scrollTop + containerHeight) <= threshold && !isLoading) {
                const customSelect = $("#" + customSelectId);
                const searchTerm = customSelect.find(".search-tags").val();
                let currentPage = customSelect.data("current-page") || 1;

                isLoading = true;
                currentPage++;
                customSelect.data("current-page", currentPage);

                // Append data and maintain scroll position
                loadOptionsLeader(customSelect, searchTerm, currentPage).then(() => {
                    isLoading = false;

                    // Maintain the scroll position by keeping the scrollTop value
                    optionsContainer.scrollTop(scrollTop);
                });
            }
        });

        // Handle the input event for search and filtering options
        $("#" + customSelectId + " .search-tags").on("input", function () {
            const searchTerm = $(this).val().trim();
            const customSelect = $("#" + customSelectId);

            if (debounceTimer) clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                customSelect.data("current-page", 1);
                const optionsContainer = customSelect.find(".options");
                optionsContainer.find(".option").remove(); // Clear current options (not selected ones)
                loadOptionsLeader(customSelect, searchTerm, 1);
            }, 300);
        });

        $("#" + customSelectId + " .search-tags").click(function (e) {
            e.stopPropagation();
        });

        // Remove selected option when clicking the "x" button
        $(document).on("click", "#" + customSelectId + " .remove-tag", function (e) {
            e.stopImmediatePropagation();
            e.preventDefault();

            const customSelect = $("#" + customSelectId);

            const valueToRemove = $(this).data("value");

            customSelect.find(`.selected-options .tag[data-value="${valueToRemove}"]`).remove();
            customSelect.find(`.option[data-value="${valueToRemove}"]`).removeClass("active");
            customSelect.find(".tags_input").val(""); // Clear the input value
        });

        // Close dropdown when clicking anywhere outside
        $(document).click(function (e) {
            if (!$(e.target).closest(".custom-select").length) {
                $("#" + customSelectId).removeClass("open");
            }
        });

        $("#" + customSelectId).each(function () {
            loadOptionsLeader($(this), "", 1);
        });

        // Clear search box when clicking the clear button
        $("#" + customSelectId + " .clear").on("click", function () {
            const searchInput = $(this).closest(".custom-select").find(".search-tags");

            searchInput.val(""); // Clear the search field
            searchInput.trigger("input"); // Trigger the input event to update the options list
        });
    }

    // Initialize the first-leader-select field with the single selection behavior
    initCustomSelectSingle("leader-select");

    // Function for the second select box (multi-select employees)
    // Function for the second select box (multi-select employees)
    function initCustomSelectMulti(customSelectId) {
        let selectedOptions = []; // Store selected options for each select instance
        let isLoading = false;  // Track loading state
        let debounceTimer = null;

        function loadOptionsTeam(customSelect, searchTerm = "", page = 1) {
            const optionsContainer = customSelect.find(".options");
            const noResultMessage = customSelect.find(".no-result-message");

            // Append loading message when making the request
            optionsContainer.append('<div class="loading">Loading...</div>');

            return $.ajax({
                url: "/assigned-leave-approvals/get_users", // Replace with your actual endpoint
                type: "GET",
                data: { searchTerm, page },
                success: function (data) {
                    optionsContainer.find(".loading").remove(); // Remove loading indicator

                    if (data.data && data.data.length > 0) {
                        data.data.forEach((item) => {
                            const isSelected = customSelect
                                .find(`.selected-options .tag[data-value="${item.id}"]`)
                                .length > 0; // Check if the item is already selected

                            const optionElement = $('<div class="option">')
                                .text(item.username)
                                .attr("data-value", item.id)
                                .toggleClass("active", isSelected) // Retain selected state
                                .click(function (e) {
                                    $(this).toggleClass("active");

                                    const currentValues = customSelect
                                        .find(".tags_input")
                                        .val()
                                        .split(',')
                                        .filter(Boolean);

                                    if ($(this).hasClass("active")) {
                                        // Append to selectedOptions if not already there
                                        if (!currentValues.includes(item.id.toString())) {
                                            currentValues.push(item.id.toString());

                                            // Append the tag to the UI
                                            const tagHTML = `
                                          <span class="tag" data-value="${item.id}">
                                              ${item.username}
                                              <span class="remove-tag" data-value="${item.id}">&times;</span>
                                          </span>`;
                                            customSelect.find(".selected-options").append(tagHTML);
                                        }
                                    } else {
                                        // Remove from selectedOptions
                                        customSelect
                                            .find(`.selected-options .tag[data-value="${item.id}"]`)
                                            .remove();
                                        const index = currentValues.indexOf(item.id.toString());
                                        if (index > -1) currentValues.splice(index, 1);
                                    }

                                    customSelect.find(".tags_input").val(currentValues.join(","));
                                    e.stopPropagation();
                                });

                            optionsContainer.append(optionElement);
                        });

                        noResultMessage.hide();
                    } else if (page === 1) {
                        noResultMessage.show();
                    }

                    customSelect.addClass("open");
                    customSelect.find(".search-tags").focus();
                },
                error: function () {
                    optionsContainer.find(".loading").remove();
                    alert("An error occurred while fetching options.");
                },
            });
        }

        $("#" + customSelectId).click(function (e) {
            if (
                $(e.target).closest(".remove-tag").length ||
                $(e.target).closest(".tag").length
            ) {
                return; // Don't toggle dropdown if clicking on the "x" button or inside the tag
            }

            $("#" + customSelectId).toggleClass("open");
            e.stopPropagation();
        });

        $("#" + customSelectId + " .options").scroll(function () {
            const optionsContainer = $(this);
            const scrollTop = optionsContainer.scrollTop();
            const scrollHeight = optionsContainer[0].scrollHeight;
            const containerHeight = optionsContainer.outerHeight();
            const threshold = 100;

            // Check if the user has scrolled to the bottom and prevent multiple requests
            if (scrollHeight - (scrollTop + containerHeight) <= threshold && !isLoading) {
                const customSelect = $("#" + customSelectId);
                const searchTerm = customSelect.find(".search-tags").val();
                let currentPage = customSelect.data("current-page") || 1;

                isLoading = true;  // Set loading flag to prevent multiple requests
                currentPage++;
                customSelect.data("current-page", currentPage);

                loadOptionsTeam(customSelect, searchTerm, currentPage).then(() => {
                    isLoading = false;  // Reset loading flag once the request is complete
                    optionsContainer.scrollTop(scrollTop);  // Maintain scroll position
                });
            }
        });

        $("#" + customSelectId + " .search-tags").on("input", function () {
            const searchTerm = $(this).val().trim();
            const customSelect = $("#" + customSelectId);

            if (debounceTimer) clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                customSelect.data("current-page", 1);
                const optionsContainer = customSelect.find(".options");
                optionsContainer.find(".option").remove(); // Clear current options (not selected ones)
                loadOptionsTeam(customSelect, searchTerm, 1);  // Reset to first page
            }, 300);
        });

        $("#" + customSelectId + " .search-tags").click(function (e) {
            e.stopPropagation();
        });

        $(document).on("click", "#" + customSelectId + " .remove-tag", function (e) {
            e.stopImmediatePropagation();
            e.preventDefault();

            const customSelect = $("#" + customSelectId);
            const valueToRemove = $(this).data("value");

            selectedOptions = selectedOptions.filter(opt => opt.id !== valueToRemove);

            customSelect
                .find(`.selected-options .tag[data-value="${valueToRemove}"]`)
                .remove();

            customSelect
                .find(`.option[data-value="${valueToRemove}"]`)
                .removeClass("active");

            customSelect.find(".tags_input").val(selectedOptions.map(opt => opt.id).join(","));
        });

        $(document).click(function (e) {
            if (!$(e.target).closest(".custom-select").length) {
                $("#" + customSelectId).removeClass("open");
            }
        });

        $("#" + customSelectId).each(function () {
            loadOptionsTeam($(this), "", 1);  // Initialize with the first page of data
        });

        $("#" + customSelectId + " .clear").on("click", function () {
            const searchInput = $(this).closest(".custom-select").find(".search-tags");
            searchInput.val(""); // Clear the search field
            searchInput.trigger("input");
        });
    }

    // Initialize employee-select field with multi-select behavior
    initCustomSelectMulti("employee-select");

});
