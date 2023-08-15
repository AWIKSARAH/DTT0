const templateSelect = document.getElementById("templateSelect");
const formContainer = document.getElementById("formContainer");
const submitButton = document.getElementById("updateSubmit");
let originalDataContent = {};

let document_id;
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

    if (field.field_type === "text") {
      inputElement = createTextInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "radio") {
      inputElement = createRadioInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "number") {
      inputElement = createNumberInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "country") {
      inputElement = createCountryInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "city") {
      hasCityField = true;
    } else if (field.field_type === "select") {
      inputElement = createSelectInput(field, dataContent[field.field_name]);
    } else if (field.field_type === "checkbox") {
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

  formContainer.querySelectorAll(".form-group").forEach((group) => {
    const input = group.querySelector("input, select, textarea, option");
    if (input) {
      const value = input.value.trim();
      dataContent[input.id] = value;
     
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

  for (const fieldName in dataContent) {
    if (dataContent.hasOwnProperty(fieldName)) {
      const newValue = dataContent[fieldName];
      const oldValue = originalDataContent[fieldName];
      if (newValue !== oldValue) {
        updatedFields[fieldName] = newValue;
      }
    }
  }

  const requestData = {
    data_content: updatedFields,
    document_id: documentId,
  };

  console.log("====================================");
  console.log(requestData);
  console.log("====================================");

  $.ajax({
    url: `/DTT/update_document`,
    method: "PUT",
    dataType: "json",
    contentType: "application/json",
    data: JSON.stringify(requestData),
    success: function (data) {
      if (data.success) {
        alert("Document updated successfully");
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
