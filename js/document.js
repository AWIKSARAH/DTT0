const templateSelect = document.getElementById("templateSelect");
const formContainer = document.getElementById("formContainer");
const submitButton = document.getElementById("updateSubmit");
let originalDataContent = {};
let objectValues = {};
let updatedFields = {};
let document_id;
let hasCityField = false;
const countrySelect = document.getElementById("countryField");

function fetchTemplateData(id) {
  fetch(`/DTT/get_document?id=${id}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      return response.json();
    })
    .then((data) => {
      if (data.success) {
        const templateStructure = JSON.parse(data.document.template_structure);
        const dataContent = JSON.parse(data.document.data_content);
        document_id = data.document.document_id;
        originalDataContent = dataContent;
        for (const key in dataContent) {
          if (dataContent.hasOwnProperty(key)) {
            const value = dataContent[key];
            if (
              typeof value === "object" &&
              value != null &&
              !Array.isArray(value)
            ) {
              objectValues[key] = value;
            }
            console.log(`Key: ${key}, Value:`, value);
          }
        }
        console.log("Object Values:", objectValues);
        renderForm(templateStructure, dataContent);
      } else {
        console.error("Error fetching template data:", data);
      }
    })
    .catch((error) => {
      console.error("Error fetching template data:", error);
    });
}

function renderForm(fields, dataContent) {
  formContainer.innerHTML = "";

  fields.forEach((field) => {
    let inputElement;

    if (field.field_type === "text" && !field.isDeleted) {
      inputElement = createTextInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "file" && !field.isDeleted) {
      inputElement = createFileInput(field);
    } else if (field.field_type === "radio" && !field.isDeleted) {
      inputElement = createRadioInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "number" && !field.isDeleted) {
      inputElement = createNumberInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "country" && !field.isDeleted) {
      inputElement = createCountryInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "city" && !field.isDeleted) {
      hasCityField = true;
    } else if (field.field_type === "select" && !field.isDeleted) {
      inputElement = createSelectInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "checkbox" && !field.isDeleted) {
      inputElement = createCheckboxInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "checkbox" && !field.isDeleted) {
      inputElement = createCheckboxInput(field, dataContent[field.field_name]);
    }

    if (inputElement) {
      const formGroup = document.createElement("div");
      formGroup.classList.add("form-group");
      formGroup.appendChild(inputElement);
      formContainer.appendChild(formGroup);
    }
  });
}

submitButton.addEventListener("click", () => {
  event.preventDefault();
  const dataContent = {};
  const updatedFields = {};
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

  for (const fieldName in dataContent) {
    if (dataContent.hasOwnProperty(fieldName)) {
      const newValue = dataContent[fieldName];
      const oldValue = originalDataContent[fieldName];
      if (newValue !== oldValue) {
        updatedFields[fieldName] = newValue;
      }
    }
  }
  if (hasRequiredFields) {
    const errorMessage = document.createElement("div");
    errorMessage.classList.add("alert", "alert-danger");
    errorMessage.textContent = "Please fill in all required fields.";
    formContainer.appendChild(errorMessage);
    return;
  }

  if (Object.keys(updatedFields).length === 0) {
    alert("No changes were made!");
    return;
  }

  submitForm(document_id, dataContent);
});

formContainer.addEventListener("change", (event) => {
  const target = event.target;
  if (target && target.classList.contains("city-select")) {
    const selectedCountry = target.value;
    const citySelect = target.nextElementSibling;

    fetch("../data/country.json")
      .then((response) => response.json())
      .then((data) => {
        const cities = data[selectedCountry];
        citySelect.innerHTML = "";
        console.log("=================ggg===================");
        console.log(cities);
        console.log("====================================");
        cities.forEach((city) => {
          const option = document.createElement("option");
          option.value = city;
          option.textContent = city;
          console.log("citySelect:", citySelect);
          citySelect.appendChild(option);
        });
      })
      .catch((error) => {
        console.error("Error fetching city data:", error);
      });
  }
});

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
function createCountryInput(field, defaultValue) {
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

  fetch("../data/country.json")
    .then((response) => response.json())
    .then((data) => {
      const countries = Object.keys(data);
      countries.forEach((country) => {
        const option = document.createElement("option");
        option.value = country;
        option.textContent = country;
        if (defaultValue === country) {
          option.selected = true;
        }
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

function createCityInput(field, defaultValue) {
  const container = document.createElement("div");
  container.classList.add("form-group");

  const label = document.createElement("label");
  label.for = field.field_name;
  label.textContent = field.field_name;
  container.appendChild(label);

  const citySelect = createCitySelect();
  container.appendChild(citySelect);

  const selectedCountry = countrySelect.value;
  if (selectedCountry) {
    fetch("../data/country.json")
      .then((response) => response.json())
      .then((data) => {
        const cities = data[selectedCountry];
        updateCityOptions(citySelect, cities, defaultValue);
      })
      .catch((error) => {
        console.error("Error fetching city data:", error);
      });
  }

  return container;
}

function createCitySelect() {
  const select = document.createElement("select");
  select.classList.add("form-control", "city-select");
  select.required = true;

  select.addEventListener("change", () => {
    const selectedCountry = countrySelect.value;
    const cities = data[selectedCountry];
    const selectedCity = select.value;

    updatedFields.city = selectedCity;

    updateCityOptions(select, cities, selectedCity);
  });

  return select;
}

function updateCityOptions(select, cities, defaultValue) {
  select.innerHTML = "";
  cities.forEach((city) => {
    const option = document.createElement("option");
    option.value = city;
    option.textContent = city;
    if (defaultValue === city) {
      option.selected = true;
    }

    select.appendChild(option);
  });
}

function createTextInput(field, defaultValue) {
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
  input.value = defaultValue || "";
  container.appendChild(input);

  return container;
}

function createNumberInput(field, defaultValue) {
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
  input.value = defaultValue || "";

  container.appendChild(input);

  return container;
}

function createRadioInput(field, defaultValue) {
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
    if (defaultValue === option) {
      input.checked = true;
    }
    radioGroup.appendChild(radioContainer);
  });

  return radioGroup;
}

function createSelectInput(field, defaultValue) {
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
    if (defaultValue === option) {
      optionElement.selected = true;
    }
    select.appendChild(optionElement);
  });

  container.appendChild(select);

  return container;
}

function createCheckboxInput(field, defaultValue) {
  const container = document.createElement("div");
  container.classList.add("form-check");

  const input = document.createElement("input");
  input.type = "checkbox";
  input.classList.add("form-check-input");
  input.id = field.field_name;
  if (defaultValue) {
    input.checked = true;
  }

  const label = document.createElement("label");
  label.classList.add("form-check-label");
  label.for = field.field_name;
  label.textContent = field.field_name;

  container.appendChild(input);
  container.appendChild(label);

  return container;
}

function submitForm(documentId, dataContent) {
  event.preventDefault();

  const updatedFields = {};
  formContainer.querySelectorAll(".form-group").forEach((group) => {
    const input = group.querySelector("input, select, textarea, option");
    if (input) {
      const value = input.value.trim();
      dataContent[input.id] = value;
      if (input.required && value === "") {
        hasRequiredFields = true;
      } else if (
        input.id in originalDataContent &&
        originalDataContent[input.id] !== value
      ) {
        updatedFields[input.id] = value;
      }
    }
  });
  const citySelect = formContainer.querySelector(".city-select");
  if (citySelect) {
    const selectedCity = citySelect.value;
    dataContent.city = selectedCity;
  }
  for (const fieldName in dataContent) {
    if (dataContent.hasOwnProperty(fieldName)) {
      const newValue = dataContent[fieldName];
      const oldValue = originalDataContent[fieldName];
      if (
        typeof newValue === "object" &&
        newValue !== null &&
        !Array.isArray(newValue)
      ) {
        updatedFields[fieldName] = newValue;
      } else if (newValue !== oldValue) {
        updatedFields[fieldName] = newValue;
      } else {
        updatedFields[fieldName] = oldValue;
      }
    }
  }

  for (const key in objectValues) {
    if (objectValues.hasOwnProperty(key)) {
      const value = objectValues[key];
      updatedFields[key] = value;
      console.log(`Key: ${key}, Value:`, value);
    }
  }

  const requestData = {
    data_content: updatedFields,
    document_id: documentId,
  };

  console.log("Object Values:", requestData);
  $.ajax({
    url: `/DTT/update_document`,
    method: "PUT",
    dataType: "json",
    contentType: "application/json",
    data: JSON.stringify(requestData),
    success: function (data) {
      if (data) {
        location.reload();
      } else {
        alert("Failed to update document");
      }
    },
    error: function (error) {
      console.log("An error occurred: " + error.responseText);
    },
  });
}
