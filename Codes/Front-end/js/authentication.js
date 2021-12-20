async function loginRequest(email, password) {
    var login = new XMLHttpRequest();
    var loginURL = BASE_URL + SUB_URL_AUTHENTICATION + LOGIN_PAGE +
        EMAIL_KEY + '=' + email + '&' + PASSWORD_KEY + '=' + password;

    login.open('GET', loginURL, false);
    login.send();

    if (login.readyState === 4 && login.status === 200) {
        var response = JSON.parse(login.responseText);
        if (response[CODE_KEY] === 7898) {
            console.log(response);
            window.localStorage.setItem(TOKEN_KEY_SOTRAGE, response[ACCESS_TOKEN_KEY]);
            window.localStorage.setItem(USER_ID_KEY_SOTRAGE, response[USER_KEY][USER_ID_KEY]);
            window.localStorage.setItem(EMAIL_KEY_SOTRAGE, response[USER_KEY][EMAIL_KEY]);
            window.localStorage.setItem(NAME_KEY_SOTRAGE, response[USER_KEY][NAME_KEY]);
            window.localStorage.setItem(MAJOR_KEY_SOTRAGE, response[USER_KEY][MAJOR_KEY]);
            window.localStorage.setItem(PURCHASES_KEY_STORAGE, response[USER_KEY][PURCHASES_KEY]);
            return true;
        } else {
            console.log('1');
            return false;
        }
    } else {
        return false;
    }
}

async function loginButton() {
    var email = document.getElementById('email_input').value;
    var password = document.getElementById('password_input').value;
    console.log(email);
    console.log(password);
    var result = await loginRequest(email, password);
    console.log('result', result);
    if (result)
        window.location.replace('IntroductionPage.html');
    else
        console.log('Error');
}

async function verifyEmail() {
    var email = window.localStorage.getItem(EMAIL_KEY_SOTRAGE);
    var code = document.getElementById('code_input').value;
    var verify = new XMLHttpRequest();
    var verifyURL = BASE_URL + SUB_URL_AUTHENTICATION + VERIFY_PAGE +
        EMAIL_KEY + '=' + email + '&' + CODE_KEY + '=' + code;

    verify.onreadystatechange = function () {
        if (verify.readyState === 4 && verify.status === 200) {
            var response = JSON.parse(verify.responseText);
            if (response[CODE_KEY] === 7898) {
                console.log(response);
                registerUser();
            }
        } else {
            console.log('Error');
        }
    };

    verify.open('GET', verifyURL);
    verify.send();
}

async function registerUser() {
    var email = window.localStorage.getItem(EMAIL_KEY_SOTRAGE);
    var password = window.localStorage.getItem(PASSWORD_KEY_SOTRAGE);
    var confirmPassowrd = window.localStorage.getItem(CONFIRM_PASSWORD_KEY_SOTRAGE);
    var name = window.localStorage.getItem(NAME_KEY_SOTRAGE);
    var major = window.localStorage.getItem(MAJOR_KEY_SOTRAGE);
    var registerResult = await registerRequest(email, name, password, confirmPassowrd, major);
    if (registerResult) {
        var loginResult = await loginRequest(email, password);
        if (loginResult)
            window.location.replace('IntroductionPage.html');
        else
            console.log('Error in login');
    } else {
        console.log('Error in register');
    }
}

async function registerButton() {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm-password').value;
    var name = document.getElementById('name').value;
    var major = document.getElementById('major').value;
    if (password.value == confirmPassword.value) {
        window.localStorage.setItem(EMAIL_KEY_SOTRAGE, email);
        window.localStorage.setItem(PASSWORD_KEY_SOTRAGE, password);
        window.localStorage.setItem(CONFIRM_PASSWORD_KEY_SOTRAGE, confirmPassword);
        window.localStorage.setItem(NAME_KEY_SOTRAGE, name);
        window.localStorage.setItem(MAJOR_KEY_SOTRAGE, major);
        var result = await verificationRequest(email);
        if (result)
            window.location.replace('Verify-Email.html');
        else
            console.log('Error in verification');
    } else {
        console.log('Error');
    }
}

async function registerRequest(email, name, password, confirmPassword, major) {
    var register = new XMLHttpRequest();
    var registerURL = BASE_URL + SUB_URL_PROFILE + REGISTER_PAGE +
        EMAIL_KEY + '=' + email + '&' + NAME_KEY + '=' + name +
        PASSWORD_KEY + '=' + password + '&' + PASSWORD_CONFIRMATION_KEY + '=' + confirmPassword +
        MAJOR_KEY + '=' + major;

    register.open('GET', registerURL, false);
    register.send();

    if (register.readyState === 4 && register.status === 200) {
        var response = JSON.parse(register.responseText);
        if (response[CODE_KEY] === 7898) {
            console.log(response);
            return true;
        }
    } else {
        console.log('Error');
    }
}

async function verificationRequest(email) {
    var verification = new XMLHttpRequest();
    var verificationURL = BASE_URL + SUB_URL_AUTHENTICATION + REQUEST_VERIFICATION_CODE_PAGE +
        EMAIL_KEY + '=' + email;

    verification.open('GET', verificationURL, false);
    verification.send();

    if (verification.readyState === 4 && verification.status === 200) {
        var response = JSON.parse(verification.responseText);
        if (response[CODE_KEY] === 7898) {
            console.log(response);
            return true;
        }
    } else {
        console.log('Error');
    }
}

function registerButton() {
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    if (password == confirmPassword) {
        let verificationRequest = new XMLHttpRequest();
        verificationRequest.onreadystatechange = function () {
            if (verificationRequest.readyState === 4 && verificationRequest.status === 200) {
                let response = JSON.parse(verificationRequest.responseText);
                if (response[CODE_KEY] === 7898) {
                    window.location.assign("Verify-Email.html?" + EMAIL_KEY + "=" + email + "&" + NAME_KEY + "=" + name + "&" + PASSWORD_KEY + "=" + password + "&" + PASSWORD_CONFIRMATION_KEY + "=" + confirmPassword);
                }
            } else {
                console.log('Error');
            }
        };

        verificationRequest.open('GET', BASE_URL + SUB_URL_AUTHENTICATION + REQUEST_VERIFICATION_CODE_PAGE + EMAIL_KEY + "=" + email);
        verificationRequest.send();
    }
}

function verifyEmail() {
    let code = document.getElementById("code_input").value;

    let verificationRequest = new XMLHttpRequest();
    verificationRequest.onreadystatechange = function () {
        if (verificationRequest.readyState === 4 && verificationRequest.status === 200) {
            console.log(verificationRequest.responseText);
            let response = JSON.parse(verificationRequest.responseText);
            if (response[CODE_KEY] === 7898) {
                register();
            }
        } else {
            console.log('Error');
        }
    };
    let email = window.location.search.substr(window.location.search.indexOf(EMAIL_KEY + "=") + 6, window.location.search.indexOf(NAME_KEY + "=") - (window.location.search.indexOf(EMAIL_KEY + "=") + 6) - 1);
    verificationRequest.open('GET', BASE_URL + SUB_URL_AUTHENTICATION + VERIFY_PAGE + CODE_KEY + "=" + code + "&" + EMAIL_KEY + "=" + email);
    verificationRequest.send();
}

function register() {
    let registerRequest = new XMLHttpRequest();
    registerRequest.onreadystatechange = function () {
        if (registerRequest.readyState === 4 && registerRequest.status === 200) {
            console.log(registerRequest.responseText);
            let response = JSON.parse(registerRequest.responseText);
            if (response[CODE_KEY] === 7898) {
                window.location.assign("Restore-Password.html");
            }
        } else {
            console.log('Error');
        }
    };
    console.log(BASE_URL + SUB_URL_PROFILE + REGISTER_PAGE + window.location.search.substr(1));
    registerRequest.open('GET', BASE_URL + SUB_URL_PROFILE + REGISTER_PAGE + window.location.search.substr(1));
    registerRequest.send();
}
