const FormComponent = (function () {
  const templateSelect = document.getElementById("templateSelect");
  const formContainer = document.getElementById("formContainer");
  const submitButton = document.getElementById("submitButton");
  let template_id;
  submitButton.style.display = "none";
  let hasCityField = false;
  const countrySelect = document.getElementById("countryField");

  function uploadImage(fileInput, type) {
    const formData = new FormData();
    formData.append("image", fileInput.files[0]);

    return fetch(`/DTT/upload?type=${type}`, {
      method: "POST",
      body: formData,
    }).then((response) => response.json());
  }

  function fetchTemplateNames() {
    fetch("/DTT/get_template_names")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        console.log("====================================");
        console.log(data);
        console.log("====================================");
        if (data.success) {
          data.template_names.forEach((template) => {
            const option = document.createElement("option");
            option.value = template.template_id;
            option.textContent = template.template_name;
            templateSelect.appendChild(option);
          });
        } else {
          console.error("Error fetching template names:", data);
        }
      })
      .catch((error) => {
        console.error("Error fetching template names:", error);
      });
  }

  function fetchTemplateData(id) {
    fetch(`/DTT/get_templateById?id=${id}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }

        return response.json();
      })
      .then((data) => {
        if (data.success) {
          const templateStructure = JSON.parse(data.fields.template_structure);
          template_id = data.fields.template_id;
          renderForm(templateStructure);
        } else {
          console.error("Error fetching template data:", data);
        }
      })
      .catch((error) => {
        console.error("Error fetching template data:", error);
      });
  }

  function renderForm(fields) {
    formContainer.innerHTML = "";

    fields.forEach((field) => {
      let inputElement;

      if (field.field_type === "text") {
        inputElement = createTextInput(field);
      } else if (field.field_type === "radio") {
        inputElement = createRadioInput(field);
      } else if (field.field_type === "file") {
        inputElement = createFileInput(field);
      } else if (field.field_type === "number") {
        inputElement = createNumberInput(field);
      } else if (field.field_type === "country") {
        inputElement = createCountryInput(field);
      } else if (field.field_type === "city") {
        hasCityField = true;
      } else if (field.field_type === "select") {
        inputElement = createSelectInput(field, dataContent[field.field_name]);
      } else if (field.field_type === "checkbox") {
        inputElement = createCheckboxInput(
          field,
          dataContent[field.field_name]
        );
      }

      if (inputElement) {
        const formGroup = document.createElement("div");
        formGroup.classList.add("form-group");
        formGroup.appendChild(inputElement);
        formContainer.appendChild(formGroup);
      }
    });
  }

  templateSelect.addEventListener("change", () => {
    var selectedTemplate = templateSelect.value;
    if (selectedTemplate) {
      submitButton.style.display = "block";
      formContainer.innerHTML = "";
      fetchTemplateData(selectedTemplate);
    } else {
      formContainer.innerHTML = "";
      submitButton.style.display = "none";
    }
  });

  submitButton.addEventListener("click", () => {
    event.preventDefault();
    const selectedTemplate = templateSelect.value;
    const dataContent = {};
    let hasRequiredFields = false;

    formContainer.querySelectorAll(".form-group").forEach((group) => {
      const input = group.querySelector("input, select, textarea, option");
      if (input) {
        const value = input.value.trim();
        dataContent[input.id] = value;
        if (input.required && value === "") {
          hasRequiredFields = true;
        }
      }
    });

    formContainer.querySelectorAll(".radio-group").forEach((radioGroup) => {
      const selectedRadio = radioGroup.querySelector(
        'input[type="radio"]:checked'
      );
      if (selectedRadio) {
        dataContent[radioGroup.id] = selectedRadio.value;
      } else {
        hasRequiredFields = true;
      }
    });

    if (hasRequiredFields) {
      const errorMessage = document.createElement("div");
      errorMessage.classList.add("alert", "alert-danger");
      errorMessage.textContent = "Please fill in all required fields.";
      formContainer.appendChild(errorMessage);
      return;
    }

    submitForm(template_id, dataContent);
  });

  formContainer.addEventListener("change", (event) => {
    const target = event.target;
    if (target && target.classList.contains("city-select")) {
      const selectedCountry = target.value;
      const citySelect = target.nextElementSibling;

      fetch("data/country.json")
        .then((response) => response.json())
        .then((data) => {
          const cities = data[selectedCountry];
          citySelect.innerHTML = "";
          cities.forEach((city) => {
            const option = document.createElement("option");
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
          });
        })
        .catch((error) => {
          console.error("Error fetching city data:", error);
        });
    }
  });

  function createCountryInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    container.appendChild(label);

    const select = document.createElement("select");
    select.classList.add("form-control");
    select.id = field.field_name;
    select.required = true;

    fetch("data/country.json")
      .then((response) => response.json())
      .then((data) => {
        const countries = Object.keys(data);
        countries.forEach((country) => {
          const option = document.createElement("option");
          option.value = country;
          option.textContent = country;
          select.appendChild(option);
        });

        if (hasCityField) {
          const citySelect = createCitySelect();
          select.addEventListener("change", () => {
            const selectedCountry = select.value;
            const cities = data[selectedCountry];
            updateCityOptions(citySelect, cities);
          });
          container.appendChild(citySelect);
        }
      })
      .catch((error) => {
        console.error("Error fetching country data:", error);
      });

    container.appendChild(select);

    return container;
  }

  function createCitySelect() {
    const select = document.createElement("select");
    select.classList.add("form-control", "city-select");
    select.required = true;
    return select;
  }

  function updateCityOptions(select, cities) {
    select.innerHTML = "";
    cities.forEach((city) => {
      const option = document.createElement("option");
      option.value = city;
      option.textContent = city;
      select.appendChild(option);
    });
  }

  function createTextInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    container.appendChild(label);

    const input = document.createElement("input");
    input.type = "text";
    input.classList.add("form-control");
    input.id = field.field_name;
    input.placeholder = field.field_name;
    input.required = true;

    container.appendChild(input);

    return container;
  }

  function createNumberInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    container.appendChild(label);

    const input = document.createElement("input");
    input.type = "number";
    input.classList.add("form-control");
    input.id = field.field_name;
    input.placeholder = "Enter your " + field.field_name;
    input.required = true;

    container.appendChild(input);

    return container;
  }

  function createRadioInput(field) {
    const radioGroup = document.createElement("div");
    radioGroup.classList.add("radio-group");
    radioGroup.id = `radio_${field.field_name}`;
    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;

    radioGroup.appendChild(label);

    field.options.forEach((option) => {
      const radioContainer = document.createElement("div");
      radioContainer.classList.add("form-check");
      const input = document.createElement("input");
      input.type = "radio";
      input.classList.add("form-check-input");
      input.name = `radio_${field.field_name}`;
      input.value = option;
      input.required = true;
      const optionLabel = document.createElement("label");
      optionLabel.classList.add("form-check-label");
      optionLabel.textContent = option;
      optionLabel.for = `${field.field_name}_${option}`;
      radioContainer.appendChild(input);
      radioContainer.appendChild(optionLabel);

      radioGroup.appendChild(radioContainer);
    });

    return radioGroup;
  }

  function createFileInput(field) {
    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.classList.add("form-control-file");
    fileInput.id = field.field_name;
    fileInput.accept = field.type + "/*";
    fileInput.required = true;

    const label = document.createElement("label");
    label.setAttribute("for", field.field_name);
    label.textContent = field.field_name;

    const fileContainer = document.createElement("div");
    fileContainer.classList.add("form-group");
    fileContainer.appendChild(label);
    fileContainer.appendChild(fileInput);

    return fileContainer;
  }
  function createSelectInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    container.appendChild(label);

    const select = document.createElement("select");
    select.classList.add("form-control");
    select.id = field.field_name;
    select.required = true;

    field.options.forEach((option) => {
      const optionElement = document.createElement("option");
      optionElement.value = option;
      optionElement.textContent = option;
      select.appendChild(optionElement);
    });

    container.appendChild(select);

    return container;
  }

  function createCheckboxInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-check");

    const input = document.createElement("input");
    input.type = "checkbox";
    input.classList.add("form-check-input");
    input.id = field.field_name;

    const label = document.createElement("label");
    label.classList.add("form-check-label");
    label.for = field.field_name;
    label.textContent = field.field_name;

    container.appendChild(input);
    container.appendChild(label);

    return container;
  }

  function submitForm(templateName, dataContent) {
    event.preventDefault();

    const uploadedImages = {};
    const uploadPromises = [];

    formContainer.querySelectorAll('input[type="file"]').forEach((input) => {
      const fieldName = input.id;
      const type = input.accept;

      const uploadPromise = uploadImage(input, type).then((result) => {
        if (result.success) {
          uploadedImages[fieldName] = {
            type: type,
            filename: result.filename,
          };
        }
      });
      uploadPromises.push(uploadPromise);
    });

    Promise.all(uploadPromises).then(() => {
      for (const fieldName in uploadedImages) {
        if (uploadedImages.hasOwnProperty(fieldName)) {
          dataContent[fieldName] = uploadedImages[fieldName];
        }
      }
      if (hasCityField) {
        const citySelect = document.querySelector(".city-select");
        if (citySelect) {
          dataContent["city"] = citySelect.value;
        }
      }

      const requestData = {
        template_id: template_id,
        data_content: dataContent,
      };

      $.ajax({
        url: "/DTT/create_document/",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(requestData),
        success: function (data) {
          if (data.success) {
            alert("Document created successfully");
            location.reload();
          } else {
            alert("Failed to create document");
          }
        },
        error: function (error) {
          console.log("An error occurred: " + error.responseText);
        },
      });
    });
  }
  return {
    init: function (templateId) {
      fetchTemplateNames();

      if (templateId) {
        fetchTemplateData(templateId);
      }
    },
  };
})();

FormComponent.init();
const modalTemplateId = updateModal.getAttribute("data-template-id");
FormComponent.init(modalTemplateId);
