$(document).ready(function () {
    let isLoading = false;
    let hasMoreData = true;
    let debounceTimer = null;

    // Initialize the custom select for a given custom select ID
    function initCustomSelect(customSelectId) {
        const customSelect = $("#" + customSelectId);
        let selectedOptions = []; // Store selected options for this select instance

        customSelect.click(function (e) {
            // If the user clicks on the search field, we don't want to collapse the dropdown
            if ($(e.target).closest(".search-tags").length) {
                return; // Prevent dropdown from closing if the search field is clicked
            }

            if (
                $(e.target).closest(".remove-tag").length ||
                $(e.target).closest(".tag").length
            ) {
                return; // Handle tag removal or selection click separately
            }

            customSelect.toggleClass("open");
            customSelect
                .find(".search-tags")
                .toggle(customSelect.hasClass("open"));

            if (customSelect.hasClass("open")) {
                customSelect.find(".search-tags").focus();
            }

            e.stopPropagation();
        });

        // Handle the scroll event with debounce
        customSelect.find(".options").on("scroll", function () {
            if (debounceTimer) clearTimeout(debounceTimer);

            debounceTimer = setTimeout(() => {
                const optionsContainer = $(this);
                const scrollTop = optionsContainer.scrollTop();
                const scrollHeight = optionsContainer[0].scrollHeight;
                const containerHeight = optionsContainer.outerHeight();
                const threshold = 100;

                if (
                    scrollHeight - (scrollTop + containerHeight) <= threshold &&
                    !isLoading &&
                    hasMoreData
                ) {
                    const searchTerm = customSelect.find(".search-tags").val();
                    let currentPage = customSelect.data("current-page") || 1;

                    isLoading = true;
                    currentPage++;
                    customSelect.data("current-page", currentPage);

                    loadOptions(customSelect, searchTerm, currentPage).then(
                        () => {
                            isLoading = false;
                        }
                    );
                }
            }, 200);
        });

        // Handle the input event for search and filtering options with debounce
        customSelect.find(".search-tags").on("input", function () {
            const searchTerm = $(this).val().trim();
            if (debounceTimer) clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                customSelect.data("current-page", 1);
                hasMoreData = true; // Reset the flag for new search
                const optionsContainer = customSelect.find(".options");
                optionsContainer.find(".option").remove();
                loadOptions(customSelect, searchTerm, 1);
            }, 300);
        });

        // Don't close the dropdown when clicking inside it
        customSelect.find(".options").click(function (e) {
            e.stopPropagation();
        });

        // Close the dropdown when clicking outside
        $(document).click(function (e) {
            if (!$(e.target).closest(".custom-select").length) {
                customSelect.removeClass("open");
                customSelect.find(".search-tags").hide();
            }
        });

        loadOptions(customSelect, "", 1);

        // Handle removal of selected tag when × is clicked
        customSelect.on("click", ".remove-tag", function (e) {
            const tagElement = $(this).closest(".tag");
            const tagValue = tagElement.data("value");

            // Remove the tag from the DOM
            tagElement.remove();

            // Remove the corresponding value from the hidden input field
            const currentValues = customSelect
                .find(".tags_input")
                .val()
                .split(",")
                .filter(Boolean);

            const index = currentValues.indexOf(tagValue.toString());
            if (index > -1) {
                currentValues.splice(index, 1);
            }

            // Update the hidden input with the new values
            customSelect.find(".tags_input").val(currentValues.join(","));

            // Deselect the corresponding option in the dropdown
            customSelect
                .find(`.options .option[data-value="${tagValue}"]`)
                .removeClass("active");

            // Adjust height after removing a tag
            adjustSelectHeight(customSelect);

            e.stopPropagation();
        });
    }

    function loadOptions(customSelect, searchTerm = "", page = 1) {
        const optionsContainer = customSelect.find(".options");
        const noResultMessage = customSelect.find(".no-result-message");

        // Show loading message
        optionsContainer.append('<div class="loading">Loading...</div>');

        $.ajax({
            url: "/brand_loads", // Replace with your actual endpoint
            type: "GET",
            data: { searchTerm, page },
            success: function (data) {
                optionsContainer.find(".loading").remove();

                if (page === 1) {
                    // Clear all options except the noResultMessage
                    optionsContainer.find(".option").remove();
                }

                if (data.data && data.data.length > 0) {
                    noResultMessage.hide(); // Hide the "No Options Found" message

                    data.data.forEach((item) => {
                        const isSelected =
                            customSelect.find(
                                `.selected-options .tag[data-value="${item.id}"]`
                            ).length > 0;

                        const optionElement = $('<div class="option">')
                            .text(item.name)
                            .attr("data-value", item.id)
                            .toggleClass("active", isSelected)
                            .click(function () {
                                $(this).toggleClass("active");

                                const currentValues = customSelect
                                    .find(".tags_input")
                                    .val()
                                    .split(",")
                                    .filter(Boolean);

                                if ($(this).hasClass("active")) {
                                    if (
                                        !currentValues.includes(
                                            item.id.toString()
                                        )
                                    ) {
                                        currentValues.push(item.id.toString());
                                        customSelect.find(".selected-options")
                                            .append(`
                                            <span class="tag" data-value="${item.id}">
                                                ${item.name} <span class="remove-tag" data-value="${item.id}">&times;</span>
                                            </span>
                                        `);
                                    }
                                } else {
                                    customSelect
                                        .find(
                                            `.selected-options .tag[data-value="${item.id}"]`
                                        )
                                        .remove();
                                    const index = currentValues.indexOf(
                                        item.id.toString()
                                    );
                                    if (index > -1)
                                        currentValues.splice(index, 1);
                                }

                                customSelect
                                    .find(".tags_input")
                                    .val(currentValues.join(","));
                            });

                        optionsContainer.append(optionElement);
                    });
                } else {
                    // Show the "No Options Found" message if no data is returned
                    noResultMessage.show();
                }
            },
            error: function () {
                optionsContainer.find(".loading").remove();
                alert("An error occurred while fetching options.");
            },
        });
    }

    // Adjust the height of the select box based on the selected options

    function adjustSelectHeight(customSelect) {
        const selectedOptionsContainer = customSelect.find(".selected-options");
        const selectedOptionsHeight = selectedOptionsContainer.outerHeight();

        // Dynamically adjust the height of the custom select container
        customSelect.css("height", 40 + selectedOptionsHeight + "px");
    }

    initCustomSelect("first-assigner-select");
});
