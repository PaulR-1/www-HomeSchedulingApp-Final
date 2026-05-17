function addFunc() {
    var firstName = document.getElementById("fName").value;
    var lastName = document.getElementById("lName").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;


    //validation
    if(firstName.length < 2||lastName.length<2||email.length<2||password.length<2)
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Input Incorect!",
                footer: "<a href=\"#\">Not Enough characters..</a>"
                });
            return;
        }

    $.ajax({
        url: "../Controllers/Controller.php",
        type: "POST",
        data: {
            fName: firstName,
            lName: lastName,
            email: email,
            password: password
        },
        success: function(returnedData) {
            Swal.fire({
                title: "Successfully added a user!",
                text: "You added a user! " + firstName + " " + lastName,
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(true);
                }
            });
        },
        error: function(xhr) {
            alert(xhr.status + " : " + xhr.responseText);
        }
    });
}

function updateFunc(userID){
    var firstName = document.getElementById("fName").value;
    var lastName = document.getElementById("lName").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var RawUpdateSelect = document.getElementById("updateHomeSelect").value;
    var UpdateSelect;

    if(RawUpdateSelect=="#$1001")
    {
        UpdateSelect = 1;
    }
    else if(RawUpdateSelect=="#$1002")
    {
        UpdateSelect = 2;
    }
    else if(RawUpdateSelect=="#$1003")
    {
        UpdateSelect = 3;
    }
    else if(RawUpdateSelect=="#$1004")
    {
        UpdateSelect = 4;
    }
    else if(RawUpdateSelect=="#$FFFFF")
    {
        UpdateSelect = 5;
    }

    $.ajax({
        url: "../Controllers/Controller.php",
        type: "POST",
        data: {
            fName : firstName,
            lName : lastName,
            email : email,
            password : password,
            homeID : UpdateSelect,
            uID : userID
        },
        success: function(returnedData){
            Swal.fire({
                title: "Successfully updated a user!",
                text: "You updated a user! " + firstName + " " + lastName,
                icon: "success",
                confirmButtonText: "OK"
            })
            .then((result) => {
                if (result.isConfirmed) {
                    location.reload(true);
                }
            });
        },
        error: function(xhr){
            alert(xhr.status + " : " + xhr.responseText)
        }
    });
}

function deleteFunc(index) {
    $.ajax({
        url: "../Controllers/Controller.php",
        type: "POST",
        data: {
            indes: index
        },
        success: function(returnedData) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(true);
                }
            });
        },
        error: function(xhr) {
            alert(xhr.status + " : " + xhr.responseText);
        }
    });
}

function redirectFunc(redirectID) {
    if (redirectID == 1) {
        window.location.href = "../views/Dashboards/RedHomeDash.php";
    } else if (redirectID == 2) {
        window.location.href = "../views/Dashboards/BlueHomeDash.php";
    } else if (redirectID == 3) {
        window.location.href = "../views/Dashboards/YellowHomeDash.php";
    } else if (redirectID == 4) {
        window.location.href = "../views/Dashboards/GreenHomeDash.php";
    } else if (redirectID == 5) {
        window.location.href = "../views/LoginPage.php";
    } else if (redirectID == 6) {
        window.location.href = "../views/DashboardPage.php";
    } else if (redirectID == 7) {
        window.location.href = "../views/RegistrationPage.php";
    }
}

function SendEmailFunc()
{
    $.ajax({
        url: "../Controllers/test.php",
        type: "POST",
        success: function(returnedData) {
            // Swal.fire({
            //     title: "Successfully emailed a user!",
            //     icon: "success",
            //     confirmButtonText: "OK"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         location.reload(true);
            //     }
            // });

            console.log(returnedData)
        },
        error: function(xhr) {
            alert(xhr.status + " : " + xhr.responseText);   
        }
    });
}

function loginFunc() {
    var loginEmail = document.getElementById("LFNAME").value;
    var loginPassword = document.getElementById("LLNAME").value;
    var RawLoginSelect = document.getElementById("LhomeSelect").value;
    var LoginSelect = 0;

    //validation
    if(loginEmail.length < 2||RawLoginSelect.length<6)
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Input Incorect!",
                footer: "<a href=\"#\">Not Enough characters..</a>"
                });
            return;
        }
    
    if(!loginEmail.includes("@"))
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "That is an invalid email!",
                footer: "<a href=\"#\">Not Enough characters..</a>"
                });
            return;
        }

    if (RawLoginSelect == "#$1001") {
        LoginSelect = 1;
    } else if (RawLoginSelect == "#$1002") {
        LoginSelect = 2;
    } else if (RawLoginSelect == "#$1003") {
        LoginSelect = 3;
    } else if (RawLoginSelect == "#$1004") {
        LoginSelect = 4;
    } else if (RawLoginSelect == "#$FFFFF") {
        LoginSelect = 5;
    }

    $.ajax({
        url: "../Controllers/Controller.php",
        type: "POST",
        data: {
            LFNAME: loginEmail,
            LLNAME: loginPassword,
            LhomeSelect: LoginSelect
        },
        success: function(returnedData) {
            if (returnedData == 1) {
                redirectFunc(1);
            } else if (returnedData == 2) {
                redirectFunc(2);
            } else if (returnedData == 3) {
                redirectFunc(3);
            } else if (returnedData == 4) {
                redirectFunc(4);
            } else if (returnedData == 5) {
                redirectFunc(6);
            } else {
                Swal.fire({
                    icon: "error",
                    title: "User Not Found",
                    text: "Please check your email, password, or home code.",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#5f9467",
                    background: "#f5f5f5",
                    color: "#2f4f38"
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: "error",
                title: "Login Error",
                text: xhr.status + " : " + xhr.responseText,
                confirmButtonText: "OK",
                confirmButtonColor: "#5f9467",
                background: "#f5f5f5",
                color: "#2f4f38"
            });
        }
    });
}

$(document).ready(function() {
    $('select').formSelect();
});