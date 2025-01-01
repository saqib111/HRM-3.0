$(document).ready(function () {
  let isLoading = false;      // Flag to track loading state
  let debounceTimer = null;    // Timer for debouncing search input
  let noMoreData = false;      // Flag to track if all data is loaded

  // Function to initialize the custom select behavior
  function initCustomSelect(customSelectId) {
      let selectedOptions = []; // Store selected options for this select instance

      // Open/close dropdown when the select box is clicked
      $("#" + customSelectId).click(function (e) {
          // Prevent closing if the click is inside the tags or remove button
          if ($(e.target).closest(".remove-tag").length || $(e.target).closest(".tag").length) {
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
          const scrollTop = optionsContainer.scrollTop();
          const scrollHeight = optionsContainer[0].scrollHeight;
          const containerHeight = optionsContainer.outerHeight();
          const threshold = 100;

          // Check if we've reached the bottom of the dropdown and we're not loading more data
          if (scrollHeight - (scrollTop + containerHeight) <= threshold && !isLoading && !noMoreData) {
              const customSelect = $("#" + customSelectId);
              const searchTerm = customSelect.find(".search-tags").val();
              let currentPage = customSelect.data("current-page") || 1;

              isLoading = true;
              currentPage++;
              customSelect.data("current-page", currentPage);

              // Load more options and maintain scroll position
              loadOptions(customSelect, searchTerm, currentPage).then(() => {
                  isLoading = false;
                  optionsContainer.scrollTop(scrollTop); // Keep scroll position intact
              });
          }
      });

      // Handle the input event for search and filtering options with debounce
      $("#" + customSelectId + " .search-tags").on("input", function () {
          const searchTerm = $(this).val().trim();
          const customSelect = $("#" + customSelectId);

          if (debounceTimer) clearTimeout(debounceTimer);

          debounceTimer = setTimeout(function () {
              customSelect.data("current-page", 1);
              const optionsContainer = customSelect.find(".options");
              optionsContainer.find(".option").remove(); // Clear current options
              loadOptions(customSelect, searchTerm, 1);
          }, 300);  // Delay of 300ms to reduce unnecessary calls
      });

      $("#" + customSelectId + " .search-tags").click(function (e) {
          e.stopPropagation(); // Prevent click propagation to avoid closing dropdown
      });

      // Function to load options asynchronously
      function loadOptions(customSelect, searchTerm = "", page = 1) {
          const optionsContainer = customSelect.find(".options");
          const noResultMessage = customSelect.find(".no-result-message");

          optionsContainer.append('<div class="loading">Loading...</div>');

          return $.ajax({
              url: "/team-member/get_members",  // Adjust URL to your actual endpoint
              type: "GET",
              data: { searchTerm, page },
              success: function (data) {
                  optionsContainer.find(".loading").remove();

                  if (data.data && data.data.length > 0) {
                      data.data.forEach((item) => {
                          const isSelected = customSelect
                              .find(`.selected-options .tag[data-value="${item.id}"]`)
                              .length > 0;  // Check if the item is already selected

                          const optionElement = $('<div class="option">')
                              .text(item.username)
                              .attr("data-value", item.id)
                              .toggleClass("active", isSelected)
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
                  customSelect.find(".search-tags").focus(); // Focus the search input field after loading options

                  // If no more data is returned, set the noMoreData flag to true
                  if (data.data.length === 0) {
                      noMoreData = true;
                  }
              },
              error: function () {
                  optionsContainer.find(".loading").remove();
                  alert("An error occurred while fetching options.");
              },
          });
      }

      // Remove selected option when clicking the "x" button
      $(document).on("click", "#" + customSelectId + " .remove-tag", function (e) {
          e.stopImmediatePropagation();
          e.preventDefault();

          const customSelect = $("#" + customSelectId);
          const valueToRemove = $(this).data("value");

          selectedOptions = selectedOptions.filter((opt) => opt.id != valueToRemove);

          customSelect
              .find(`.selected-options .remove-tag[data-value="${valueToRemove}"]`)
              .parent()
              .remove();

          customSelect
              .find(`.option[data-value="${valueToRemove}"]`)
              .removeClass("active");

          updateSelectedOptions(customSelect);  // Update selected options UI
      });

      // Close dropdown when clicking anywhere outside
      $(document).click(function (e) {
          if (!$(e.target).closest(".custom-select").length) {
              $("#" + customSelectId).removeClass("open");
          }
      });

      // Initialize the select box with page 1 data
      $("#" + customSelectId).each(function () {
          loadOptions($(this), "", 1);
      });

      // Clear search box when clicking the clear button
      $("#" + customSelectId + " .clear").on("click", function () {
          const searchInput = $(this).closest(".custom-select").find(".search-tags");
          searchInput.val("");  // Clear the search field
          searchInput.trigger("input");  // Trigger the input event to update the options list
      });
  }

  // Initialize the first select box with the ID 'first-assigner-select'
  initCustomSelect("first-assigner-select");

});
