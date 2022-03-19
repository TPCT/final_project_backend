function login(){
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const loginContainer = document.getElementById("loginContainer");
    const submit = document.getElementById("submit");

    usernameInput.onchange = function(){
        usernameInput.className = "form-control";
    }

    passwordInput.onchange = function(){
        passwordInput.className = "form-control";
    }

    let username = usernameInput.value;
    let password = passwordInput.value;

    createWaiting(loginContainer, "برجاء الانتظار");
    let xhttp = new XMLHttpRequest();
    xhttp.withCredentials = true;
    submit.disabled = true;
    xhttp.onload = function(){
        deleteWaiting(loginContainer);
        try{
            if (this.status == 200){
                data = this.responseText
                data = JSON.parse(data);
                if (data.status == 1){
                    window.location.href = data.redirect;
                }else{
                    message("اسم المستخدم او كلمه المرور غير صحيحه", 'danger', loginContainer);
                    usernameInput.className += " is-invalid";
                    passwordInput.className += " is-invalid";
                }
            }else{
                console.log("[-] couldn't reach to the webiste.");
            }

        }catch(e){
            console.log("[-] an error occurred while trying to parse data", this.responseText, e);
        }
        submit.disabled = false;
    }
    xhttp.open("post", "/");
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(`username=${username}&password=${password}`);
}