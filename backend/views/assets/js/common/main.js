function createWaiting(container, message) {
  var wrapper = document.createElement("div");
  wrapper.className = "d-flex justify-content-center p-2 status";
  wrapper.innerHTML = `<span>${message}</span><div class='spinner-grow text-primary' role='status'></div>`;
  container.insertBefore(wrapper, container.firstChild);
}

function deleteWaiting(container) {
  container.getElementsByClassName("status")[0].remove();
}

function message(message, type, placeholder) {
  var wrapper = document.createElement("div");
  wrapper.className = "text-center alert alert-" + type + " fade in show";
  wrapper.innerHTML = message;
  placeholder.insertBefore(wrapper, placeholder.firstChild);
  message = new bootstrap.Alert(wrapper);
  setTimeout(() => {
    message.close();
  }, 500);
}

function createButton(message, onclick, color){
    const button = document.createElement('button')
    const text = document.createTextNode(message);
    button.onclick = onclick;
    button.appendChild(text);
    button.className = "btn btn-" + color;
    return button;
}

function deleteTableRow(id) {
    const row = document.getElementById(id);
    row.disabled = true;
    row.remove();
}

function viewToast(message) {
  let toastContainer = document.getElementById("toast-container");
  toastContainer.style.zIndex = 9999;
  let toastElement = createToast(message);
  toastContainer.insertBefore(toastElement, toastContainer.firstChild);
  var toast = new bootstrap.Toast(toastElement);
  toast.show();
}

function createToast(message) {
  const toastDiv = document.createElement("div");
  toastDiv.className =
    "toast align-items-center text-white bg-primary border-0 mb-1";
  toastDiv.role = "alert";
  toastDiv.innerHTML =
    '<div class="d-flex"><div class="toast-body">' +
    message +
    '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
  return toastDiv;
}

function createCheckbox(name, value, checked=false){
    let formCheck = document.createElement('div');
    formCheck.className = "form-check";
    let inputCheck = document.createElement('input');
    inputCheck.className = 'form-check-input';
    inputCheck.name = `obstructions[]`;
    inputCheck.value = value;
    inputCheck.id = value;
    inputCheck.type = "checkbox";
    inputCheck.checked = checked;
    inputCheck.setAttribute('aria-label', name);
    let labelCheck = document.createElement('label');
    let textNode = document.createTextNode(name);
    labelCheck.setAttribute('for', value);
    labelCheck.appendChild(textNode);
    labelCheck.className = "form-check-label";
    formCheck.appendChild(labelCheck);
    formCheck.appendChild(inputCheck);
    formCheck.style.flexBasis = "33.333%";
    formCheck.style.flexGrow = "1";
    return formCheck;
}

function loaded() {
  const loading = document.getElementById("loading");
  const fadeEffect = setInterval(() => {
    if (!loading.style.opacity) {
      loading.style.opacity = 1;
    }
    if (loading.style.opacity > 0) {
      loading.style.opacity -= 0.1;
    } else {
      loading.style.display = "none";
      clearInterval(fadeEffect);
    }
  }, 70);
}

