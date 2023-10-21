document.addEventListener("DOMContentLoaded", function () {
  const templateList = document.getElementById("template-list");
  const successMessage = document.getElementById("success-message");
  const typeSelect = document.getElementById("typeSelect");
  const addFieldButton = document.getElementById("addFieldButton");
  const addFieldForm = document.getElementById("add-field-form");
  const fieldTypeSelect = document.getElementById("new-field-type");
  const fileAcceptContainer = document.getElementById(
    "file-accept-container-new"
  );
  let templateStructure = [];
  const fieldRequired = document.getElementById("field-required");
  const fieldNameInput = document.getElementById("new-field-name");
  const fieldOptionContainer = document.getElementById("field-option-new");
  const optionsContainer = document.getElementById("options-container");
  const addOptionButton = document.getElementById("add-option");
  const saveTemplateButton = document.getElementById("save-template");

  addFieldButton.addEventListener("click", function () {
    addFieldForm.style.display = "block";
  });

  saveTemplateButton.addEventListener("click", function () {
    addFieldForm.style.display = "none";

    const selectedFieldType = fieldTypeSelect.value;
    const fieldName = fieldNameInput.value;
    const required = fieldRequired.checked;

    if (!selectedFieldType || !fieldName) {
      alert("The selext field or type name of the field it's required ");
      return;
    }

    const templateField = {
      field_name: fieldName,
      field_type: selectedFieldType,
      required: required,
      isDeleted: false,
    };

    if (
      selectedFieldType === "select" ||
      selectedFieldType === "radio" ||
      selectedFieldType === "checkbox"
    ) {
      const optionInputs =
        optionsContainer.querySelectorAll(".form-check-input");
      const options = Array.from(optionInputs).map((input) => input.value);
      templateField.options = options;
    }

    templateStructure.push(templateField);
  });

  fieldTypeSelect.addEventListener("change", function () {
    const selectedFieldType = fieldTypeSelect.value;

    if (selectedFieldType === "file") {
      fileAcceptContainer.style.display = "block";
      fieldOptionContainer.style.display = "none";
    } else if (
      selectedFieldType === "select" ||
      selectedFieldType === "radio" ||
      selectedFieldType === "checkbox"
    ) {
      fileAcceptContainer.style.display = "none";
      fieldOptionContainer.style.display = "block";
    } else {
      fileAcceptContainer.style.display = "none";
      fieldOptionContainer.style.display = "none";
    }
  });

  addOptionButton.addEventListener("click", function () {
    const newOptionInput = document.getElementById("new-option");
    const optionValue = newOptionInput.value.trim();

    if (optionValue !== "") {
      const optionElement = document.createElement("div");
      optionElement.className = "form-check";

      if (
        fieldTypeSelect.value === "select" ||
        fieldTypeSelect.value === "radio" ||
        fieldTypeSelect.value === "checkbox"
      ) {
        optionElement.innerHTML = `
          <input class="form-check-input" type="${fieldTypeSelect.value}" name="options" value="${optionValue}">
          <label class="form-check-label">${optionValue}</label>
        `;
        optionsContainer.appendChild(optionElement);

        newOptionInput.value = "";
      }
    }
  });
  function deleteTemplate(templateId) {
    $.ajax({
      url: `/DTT/delete_template?id=${templateId}`,
      method: "DELETE",
      dataType: "json",
      success: function (data) {
        if (data.success) {
          const row = document.querySelector(
            `[data-template-id="${templateId}"]`
          );
          if (row) {
            row.remove();
            showSuccessMessage();
          }
        } else {
          console.error("Failed to delete template:", data.error);
        }
      },
      error: function (error) {
        console.error("An error occurred:", error.responseText);
      },
    });
  }
  function fetchTypes() {
    fetch("/DTT/get_types/")
      .then((response) => response.json())
      .then((data) => {
        data.types.forEach((type) => {
          const option = document.createElement("option");
          option.value = type.type_id;
          option.textContent = type.type_name;
          typeSelect.appendChild(option);
        });

        typeSelect.addEventListener("change", () => {
          const selectedType = typeSelect.value;
          updateTemplateList(selectedType);
        });

        const initialSelectedTypeId = typeSelect.value;
        if (initialSelectedTypeId !== "") {
          updateTemplateList(initialSelectedTypeId);
        }
      })
      .catch((error) => console.error("Failed to fetch types:", error));
  }

  saveTemplateButton.addEventListener("click", function () {
    addFieldForm.style.display = "none";

    const selectedFieldType = fieldTypeSelect.value;
    const fieldName = fieldNameInput.value;
    const required = fieldRequired.checked;

    const templateField = {
      field_name: fieldName,
      field_type: selectedFieldType,
      required: required,
      isDeleted: false,
    };

    if (
      selectedFieldType === "select" ||
      selectedFieldType === "radio" ||
      selectedFieldType === "checkbox"
    ) {
      const optionInputs =
        optionsContainer.querySelectorAll(".form-check-input");
      const options = Array.from(optionInputs).map((input) => input.value);
      templateField.options = options;
    }
  });

  function updateTemplate(
    templateId,
    updatedTemplateStructure,
    templateName,
    typeId
  ) {
    $.ajax({
      url: `/DTT/update_template?id=${templateId}`,
      method: "POST",
      data: {
        template_structure: updatedTemplateStructure,
        template_name: templateName,
        type_id: typeId,
      },
      dataType: "json",
      success: function (data) {
        if (data.success) {
          alert("Template updated successfully");
          location.reload();
        } else {
          alert("Failed to update template");
        }
      },
      error: function (error) {
        console.error("An error occurred:", error.responseText);
      },
    });
  }

  function updateTemplateList(selectedTypeId) {
    fetch(`/DTT/get_templates_by_type?type_id=${selectedTypeId}`)
      .then((response) => response.json())
      .then((data) => {
        templateList.innerHTML = "";
        if (
          data &&
          data.templates &&
          Array.isArray(data.templates) &&
          data.templates.length > 0
        ) {
          data.templates.forEach((template) => {
            const row = templateList.insertRow();
            row.dataset.templateId = template.template_id;
            row.insertCell(0).textContent = template.template_name;
            row.insertCell(1).innerHTML =
              '<button class="btn btn-danger deleteTemplate" data-id="' +
              template.template_id +
              '">Delete</button>';
            row.insertCell(2).innerHTML =
              '<button class="btn btn-primary updateTemplate" data-id="' +
              template.template_id +
              '">Update</button>';

            const updateButtons = document.querySelectorAll(".updateTemplate");
            updateButtons.forEach((button) => {
              button.addEventListener("click", function () {
                const templateId = button.getAttribute("data-id");
                const updateModal = new bootstrap.Modal(
                  document.getElementById("updateModal")
                );
                fetch(`/DTT/get_templateById?id=${templateId}`)
                  .then((response) => response.json())
                  .then((fieldsData) => {
                    const formContainer =
                      document.getElementById("formContainer");
                    formContainer.innerHTML = `
                      <label> <strong>${fieldsData.fields.template_name}</strong></label></br>
                      <label>Type: ${fieldsData.fields.type_name}</label>
                      `;
                    const fieldArray = JSON.parse(
                      fieldsData.fields.template_structure
                    );

                    fieldArray.forEach((field, index) => {
                      const fieldDiv = document.createElement("div");
                      fieldDiv.className = "field";
                      fieldDiv.innerHTML = `
                    <label>${field.field_name}</label>
                    <button class="btn btn-${
                      field.isDeleted ? "secondary" : "danger"
                    } delete-field">
                        <i class="bi bi-${
                          field.isDeleted ? "arrow-counterclockwise" : "trash"
                        }"></i> ${field.isDeleted ? "Restore" : "Delete"}
                    </button>`;
                      fieldDiv.setAttribute("data-index", index);

                      const deleteButton =
                        fieldDiv.querySelector(".delete-field");
                      deleteButton.addEventListener("click", function () {
                        field.isDeleted = !field.isDeleted;
                        deleteButton.innerHTML = `
                                                            <i class="bi bi-${
                                                              field.isDeleted
                                                                ? "arrow-counterclockwise"
                                                                : "trash"
                                                            }"></i> ${
                          field.isDeleted ? "Restore" : "Delete"
                        }`;
                        deleteButton.className = `btn ${
                          field.isDeleted ? "btn-primary" : "btn-danger"
                        } delete-field`;
                      });

                      formContainer.appendChild(fieldDiv);
                    });

                    const saveChangesButton =
                      document.getElementById("updateSubmit");
                    // saveChangesButton.className = "btn btn-primary";
                    // saveChangesButton.textContent = "Save Changes";
                    saveChangesButton.addEventListener("click", function () {
                      const updatedTemplateStructure = fieldArray;
                      let data;
                      if (templateStructure) {
                        data =
                          updatedTemplateStructure.concat(templateStructure);
                      }

                      const templateName = fieldsData.fields.template_name;
                      const typeId = fieldsData.fields.type_id;
                      updateTemplate(
                        templateId,
                        JSON.stringify(data),
                        templateName,
                        typeId
                      );
                    });


                    updateModal.show();
                  })
                  .catch((error) => {
                    console.error("Failed to fetch template fields:", error);
                  });
              });
            });
          });

          const deleteButtons = document.querySelectorAll(".deleteTemplate");
          deleteButtons.forEach((button) => {
            button.addEventListener("click", function () {
              const templateId = button.getAttribute("data-id");
              if (confirm("Are you sure you want to delete this template?")) {
                deleteTemplate(templateId);
              }
            });
          });
        } else {
          const noTemplatesRow = templateList.insertRow();
          const noTemplatesCell = noTemplatesRow.insertCell();
          noTemplatesCell.colSpan = 2;
          noTemplatesCell.textContent = "No templates available for this type.";
        }
      })
      .catch((error) =>
        console.error("Failed to fetch templates by type:", error)
      );
  }

  fetchTypes();

  const initialSelectedTypeId = typeSelect.value;
  if (initialSelectedTypeId !== "") {
    updateTemplateList(initialSelectedTypeId);
  }
});
