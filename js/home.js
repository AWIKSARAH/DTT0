const FormComponent = (function () {
  const templateSelect = document.getElementById("templateSelect");
  const formContainer = document.getElementById("formContainer");
  const submitButton = document.getElementById("submitButton");
  const countrySelect = document.getElementById("countryField");
  let template_id;
  submitButton.style.display = "none";
  let hasCityField = false;

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
        console.log("=================njnjn===================");
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
        console.log("====================================");
        console.log(data);
        console.log("====================================");
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

      if (field.field_type === "text" && !field.isDeleted) {
        inputElement = createTextInput(field);
      } else if (field.field_type === "file" && !field.isDeleted) {
        inputElement = createFileInput(field);
      } else if (field.field_type === "number" && !field.isDeleted) {
        inputElement = createNumberInput(field);
      } else if (field.field_type === "country" && !field.isDeleted) {
        inputElement = createCountryInput(field);
      } else if (field.field_type === "city" && !field.isDeleted) {
        hasCityField = true;
      } else if (field.field_type === "select" && !field.isDeleted) {
        inputElement = createSelectInput(field, field.options);
      } else if (field.field_type === "date" && !field.isDeleted) {
        inputElement = createDateInput(field);
      } else if (field.field_type === "checkbox" && !field.isDeleted) {
        inputElement = createCheckboxInput(field, field.options);
      } else if (field.field_type === "radio" && !field.isDeleted) {
        inputElement = createRadioInput(field, field.options);
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
  function submitButtonHandler(event) {
    event.preventDefault();
    const dataContent = {};
    let hasRequiredFields = false;

    formContainer.querySelectorAll(".form-group").forEach((group) => {
      const input = group.querySelector("input, select, textarea");
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
        const fieldName = selectedRadio.name;
        const value = selectedRadio.value;
        dataContent[fieldName] = value;
      } else {
        hasRequiredFields = true;
      }
    });

    if (hasCityField) {
      const citySelect = document.querySelector(".city-select");
      if (citySelect) {
        const selectedCity = citySelect.value;
        dataContent["city"] = selectedCity;
      }
    }
    if (countrySelect) {
      const selectedCountry = countrySelect.value;
      dataContent["country"] = selectedCountry;
    }

    if (hasRequiredFields) {
      const errorMessage = document.createElement("div");
      errorMessage.classList.add("alert", "alert-danger");
      errorMessage.textContent = "Please fill in all required fields.";
      formContainer.appendChild(errorMessage);
      return;
    }

    submitForm(template_id, dataContent);
  }

  submitButton.addEventListener("click", submitButtonHandler);

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

  function createRequiredLabel(required) {
    const label = document.createElement("span");
    label.classList.add("required-label");
    label.textContent = required ? "(Required)" : "(Optional)";
    return label;
  }

  function createCountryInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const countryLabel = document.createElement("label");
    countryLabel.for = field.field_name;
    countryLabel.textContent = field.field_name;
    container.appendChild(countryLabel);

    const select = document.createElement("select");
    select.classList.add("form-control");
    select.id = field.field_name;
    if (field.required) {
      select.required = true;
    }
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
          const cityLabel = document.createElement("label");
          cityLabel.for = "cityField";
          cityLabel.textContent = "City";
          container.appendChild(cityLabel);

          const citySelect = createCitySelect();
          select.addEventListener("change", () => {
            const selectedCountry = select.value;
            const cities = data[selectedCountry];
            updateCityOptions(citySelect, cities);
          });
          container.appendChild(citySelect);

          if (countries.length > 0) {
            const firstCountry = countries[0];
            select.value = firstCountry;
            const cities = data[firstCountry];
            updateCityOptions(citySelect, cities);
          }
        }
      })
      .catch((error) => {
        console.error("Error fetching country data:", error);
      });

    container.appendChild(select);
    const requiredLabel = createRequiredLabel(field.required);
    container.appendChild(requiredLabel);

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
    if (cities.length > 0) {
      select.value = cities[0];
    }
  }

  function createTextInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name + " :";
    container.appendChild(label);

    const input = document.createElement("input");
    input.type = "text";
    input.classList.add("form-control");
    input.id = field.field_name;
    input.placeholder = "Enter the " + field.field_name;
    input.required = true;

    container.appendChild(input);
    const requiredLabel = createRequiredLabel(field.required);
    container.appendChild(requiredLabel);

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
    const requiredLabel = createRequiredLabel(field.required);
    container.appendChild(requiredLabel);

    return container;
  }

  function createRadioInput(field, options) {
    const radioGroup = document.createElement("div");
    radioGroup.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    radioGroup.appendChild(label);

    options.forEach((option) => {
      const radioContainer = document.createElement("div");
      radioContainer.classList.add("form-check");

      const radio = document.createElement("input");
      radio.type = "radio";
      radio.classList.add("form-check-input");
      radio.name = field.field_name;
      radio.value = option;
      radio.required = field.required;

      const radioLabel = document.createElement("label");
      radioLabel.classList.add("form-check-label");
      radioLabel.for = `${field.field_name}_${option}`;
      radioLabel.textContent = option;

      radioContainer.appendChild(radio);
      radioContainer.appendChild(radioLabel);

      radioGroup.appendChild(radioContainer);
    });

    return radioGroup;
  }

  function createFileInput(field) {
    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.classList.add("form-control-file");
    fileInput.id = field.field_name;

    fileInput.accept = field.type === ".pdf" ? "application/pdf" : "image/*";

    fileInput.required = true;

    const label = document.createElement("label");
    label.setAttribute("for", field.field_name);
    label.textContent = field.field_name;

    const fileContainer = document.createElement("div");
    fileContainer.classList.add("form-group");
    fileContainer.appendChild(label);
    fileContainer.appendChild(fileInput);
    const requiredLabel = createRequiredLabel(field.required);
    fileContainer.appendChild(requiredLabel);

    return fileContainer;
  }

  function createSelectInput(field, options) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    container.appendChild(label);

    const select = document.createElement("select");
    select.classList.add("form-control");
    select.id = field.field_name;
    select.required = field.required;
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "Choose one";
    defaultOption.disabled = true;
    defaultOption.selected = true;
    select.appendChild(defaultOption);

    options.forEach((option) => {
      const optionElement = document.createElement("option");
      optionElement.value = option;
      optionElement.textContent = option;
      select.appendChild(optionElement);
    });

    container.appendChild(select);
    const requiredLabel = createRequiredLabel(field.required);
    container.appendChild(requiredLabel);

    return container;
  }

  function createCheckboxInput(field, options) {
    const checkboxGroup = document.createElement("div");
    checkboxGroup.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    checkboxGroup.appendChild(label);

    options.forEach((option) => {
      const checkboxContainer = document.createElement("div");
      checkboxContainer.classList.add("form-check");

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.classList.add("form-check-input");
      checkbox.id = `${field.field_name}_${option}`;
      checkbox.value = option;
      checkbox.required = field.required;

      const checkboxLabel = document.createElement("label");
      checkboxLabel.classList.add("form-check-label");
      checkboxLabel.for = checkbox.id;
      checkboxLabel.textContent = option;

      checkboxContainer.appendChild(checkbox);
      checkboxContainer.appendChild(checkboxLabel);

      checkboxGroup.appendChild(checkboxContainer);
    });
    const requiredLabel = createRequiredLabel(field.required);
    checkboxGroup.appendChild(requiredLabel);
    return checkboxGroup;
  }

  function createDateInput(field) {
    const container = document.createElement("div");
    container.classList.add("form-group");

    const label = document.createElement("label");
    label.for = field.field_name;
    label.textContent = field.field_name;
    container.appendChild(label);

    const input = document.createElement("input");
    input.type = "date";
    input.classList.add("form-control");
    input.id = field.field_name;
    input.required = true;

    container.appendChild(input);
    const requiredLabel = createRequiredLabel(field.required);
    container.appendChild(requiredLabel);

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
      formContainer.querySelectorAll("select").forEach((select) => {
        const fieldName = select.id;
        dataContent[fieldName] = select.value;
      });

      formContainer.querySelectorAll('input[type="radio"]').forEach((radio) => {
        if (radio.checked) {
          const fieldName = radio.name;
          dataContent[fieldName] = radio.value;
        }
      });

      formContainer
        .querySelectorAll('input[type="checkbox"]')
        .forEach((checkbox) => {
          if (checkbox.checked) {
            const fieldName = checkbox.name;
            dataContent[fieldName] = dataContent[fieldName] || [];
            dataContent[fieldName].push(checkbox.value);
          }
        });
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
