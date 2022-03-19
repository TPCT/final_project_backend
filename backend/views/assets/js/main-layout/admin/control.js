function fetchAvailableCountries() {
  const countriesSelect = document.getElementById("countries");
  let xhttp = new XMLHttpRequest();
  xhttp.withCredentials = true;
  xhttp.onload = function () {
    let data;
    try {
      if (this.status === 200) {
        data = this.responseText;
        data = JSON.parse(data);
        if (data.status === 1) {
          data.data.forEach(function(country_data){
            let option = new Option(country_data['country_name'], country_data['country_id']);
            countriesSelect.add(option);
          });
        }
      } else {
        console.log("[-] couldn't reach to the website.");
      }
    } catch (e) {
      console.log(
          "[-] an error occurred while trying to parse data",
          this.responseText,
          e
      );
    }
  };
  xhttp.open('get', '/fetchCountries');
  xhttp.send();
  return true;
}

function insertSetting(setting_id, setting_name, telegram_bot, telegram_channel, obstruction_name, country_name){
    const botSettingsTableBody = document.getElementById("bot-settings-table-body");
    const row = botSettingsTableBody.insertRow();
    const operations = row.insertCell(0);
    const channel = row.insertCell(1);
    const bot = row.insertCell(2);
    const obstructions = row.insertCell(3);
    const country = row.insertCell(4);
    const name = row.insertCell(5);
    const del = createButton('حذف', () => {deleteSetting(setting_id)}, 'danger');
    const container = document.createElement("div");
    container.className = "d-flex justify-content-center align-item-center w-100 gap-2";
    container.appendChild(del);
    operations.appendChild(container);
    name.innerText = setting_name;
    bot.innerText = telegram_bot;
    obstructions.innerText = obstruction_name;
    country.innerText = country_name;
    channel.innerText = telegram_channel;
    row.id = setting_id;

}

function deleteSetting(id){
    let xhttp = new XMLHttpRequest();
    xhttp.withCredentials = true;
    xhttp.onload = function () {
        let data;
        try {
            if (this.status === 200) {
                data = this.responseText;
                data = JSON.parse(data);
                if (data.status === 1) {
                    deleteTableRow(id);
                    viewToast("تم الحذف بنجاح");
                }else{
                    viewToast("فشل الحذف");
                }
            } else {
                console.log("[-] couldn't reach to the webiste.");
            }
        } catch (e) {
            console.log(
                "[-] an error occurred while trying to parse data",
                this.responseText,
                e
            );
        }
    };
    xhttp.open("post", "/deleteSetting");
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + id);
    return true
}

function fetchAvailableSettings(reset=false){
    let xhttp = new XMLHttpRequest();
    xhttp.withCredentials = true;
    xhttp.onload = function () {
        let data;
        try {
            if (this.status === 200) {
                data = this.responseText;
                data = JSON.parse(data);
                if (data.status === 1) {
                    data.data.append.forEach(function(setting_data){
                        insertSetting(setting_data.id, setting_data.setting_name,
                            setting_data.telegram_bot, setting_data.telegram_channel,
                            setting_data.obstruction_names, setting_data.country_name)
                    });
                    data.data.delete.forEach(function(setting_data){
                        try{deleteTableRow(setting_data.id)}catch (e){}
                    });
                }
            } else {
                console.log("[-] couldn't reach to the webiste.");
            }
        } catch (e) {
            console.log(
                "[-] an error occurred while trying to parse data",
                this.responseText,
                e
            );
        }
    };
    xhttp.open("get", "/fetchSettings?reset=" + reset);
    xhttp.send();
    return true
}

function fetchAvailableObstructions() {
  const obstructionsDiV = document.getElementById("obstructions");

  let xhttp = new XMLHttpRequest();
  xhttp.withCredentials = true;
  xhttp.onload = function () {
    let data;
    try {
      if (this.status === 200) {
        data = this.responseText;
        data = JSON.parse(data);
        if (data.status === 1) {
          data.data.forEach(function(obstruction_data){
            let checkbox = createCheckbox(obstruction_data['obstruction_name'], obstruction_data['obstruction_id']);
            obstructionsDiV.appendChild(checkbox);
          });
        }
      } else {
        console.log("[-] couldn't reach to the webiste.");
      }
    } catch (e) {
      console.log(
          "[-] an error occurred while trying to parse data",
          this.responseText,
          e
      );
    }
  };
  xhttp.open("get", "/fetchObstructions");
  xhttp.send();
  return true
}

function saveSetting(){
      const name = document.getElementById('name');
      const bot = document.getElementById('bot');
      const channel = document.getElementById('channel');
      const country = document.getElementById('countries');
      const obstructions = document.getElementsByName('obstructions[]');
      const submit = document.getElementById("submit");
      const settingsForm = document.getElementById("settings-form");

      name.onchange = function (){name.className="form-control"}
      bot.onchange = function (){bot.className="form-control"}
      channel.onchange = function (){channel.className="form-control"}
      let status = true;

      if (!String(name.value).trim().length){
          name.className += " is-invalid";
          status = false;
      }

      if (!String(bot.value).trim().length){
        bot.className += " is-invalid";
        status = false;
      }

      if (!String(channel.value).trim().length){
        channel.className += " is-invalid"
        status = false;
      }

      if (!status) return;

      let nameString = name.value;
      let botString = bot.value;
      let channelString = channel.value;
      let countryString = country.options[country.options.selectedIndex].value;
      let obstructionsArray = [];

      for (let i = 0; i < obstructions.length; i++) {
          if (obstructions[i].checked)
              obstructionsArray.push("obstructions[]=" + obstructions[i].value);
      }

      let obstructionsString = obstructionsArray.join("&");
      if (obstructionsString.length === 0)
          obstructionsString = "obstructions[] = 0";

      submit.disabled = true;

      let xhttp = new XMLHttpRequest();
      xhttp.withCredentials = true;
      xhttp.onload = function () {
          submit.disabled = false;
          let data;
          try {
              if (this.status === 200) {
                  data = this.responseText;
                  data = JSON.parse(data);
                  if (data.status === 1) {
                      viewToast("تم الحفظ بنجاح");
                      settingsForm.reset();
                  } else
                      viewToast("لم يتم حفظ الاعداد");
              } else {
                  console.log("[-] couldn't reach to the webiste.");
              }
          } catch (e) {
              console.log(
                  "[-] an error occurred while trying to parse data",
                  this.responseText,
                  e
              );
          }
      };
  xhttp.open("post", "/admin/dashboard/save");
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send(`name=${nameString}&bot=${botString}&channel=${channelString}&country=${countryString}&${obstructionsString}`);
}

function runner() {
  if (document.readyState === "complete") {
    let result = fetchAvailableCountries();
    result = fetchAvailableObstructions();
    result = fetchAvailableSettings(true);
    const checkLoaded = setInterval(function () {
      if (result)
        loaded();
      clearInterval(checkLoaded);
    }, 1500);
    const loadSettings = setInterval(async function(){fetchAvailableSettings()}, 1000);
  }
}
document.onreadystatechange = function () {
    runner();
};
