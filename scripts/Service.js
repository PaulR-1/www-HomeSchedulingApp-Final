function isValidEmailFunc(email) {
    if (!email.includes("@")) {
        return false;
    }

    if (email.length < 6) {
        return false;
    }

    var emailParts = email.split("@");
    if (emailParts.length != 2) {
        return false;
    }

    var localPart = emailParts[0];
    var domainPart = emailParts[1];

    if (localPart.length < 1 || domainPart.length < 4) {
        return false;
    }

    if (!domainPart.includes(".")) {
        return false;
    }

    var emailPattern = /^[^\s@]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(\.[a-zA-Z]{2,})?$/;
    if (!emailPattern.test(email)) {
        return false;
    }

    return true;
}

function getCurrentHomeThemeConfigFunc() {
    var defaultTheme = {
        homeName: "Red Home",
        primaryColor: "#b3484e",
        secondaryColor: "#64748b",
        backgroundColor: "#f8f9fb",
        textColor: "#1f2937"
    };

    if (typeof document === "undefined" || !document.body) {
        return defaultTheme;
    }

    if (document.body.classList.contains("dashboard-blue")) {
        return {
            homeName: "Blue Home",
            primaryColor: "#4f9fd8",
            secondaryColor: "#4b6f8a",
            backgroundColor: "#f4f9fd",
            textColor: "#1e3a4c"
        };
    }

    if (document.body.classList.contains("dashboard-green")) {
        return {
            homeName: "Green Home",
            primaryColor: "#5f9467",
            secondaryColor: "#56705c",
            backgroundColor: "#f4faf5",
            textColor: "#1f3a24"
        };
    }

    if (document.body.classList.contains("dashboard-yellow")) {
        return {
            homeName: "Yellow Home",
            primaryColor: "#c8bd3f",
            secondaryColor: "#8a8452",
            backgroundColor: "#fffdf2",
            textColor: "#3f3a1d"
        };
    }

    return defaultTheme;
}

function patchSweetAlertHomeThemeFunc() {
    if (typeof Swal === "undefined" || typeof Swal.fire !== "function") {
        return;
    }

    if (typeof window !== "undefined" && window.__homeThemeSwalPatched) {
        return;
    }

    var originalSwalFire = Swal.fire.bind(Swal);
    Swal.fire = function() {
        if (arguments.length === 1 && typeof arguments[0] === "object" && arguments[0] !== null) {
            var themedOptions = Object.assign({}, arguments[0]);
            var themeConfig = getCurrentHomeThemeConfigFunc();

            themedOptions.confirmButtonColor = themeConfig.primaryColor;
            themedOptions.background = themeConfig.backgroundColor;
            themedOptions.color = themeConfig.textColor;

            if (themedOptions.showCancelButton) {
                themedOptions.cancelButtonColor = themeConfig.secondaryColor;
            }

            return originalSwalFire(themedOptions);
        }

        return originalSwalFire.apply(Swal, arguments);
    };

    if (typeof window !== "undefined") {
        window.__homeThemeSwalPatched = true;
    }
}

patchSweetAlertHomeThemeFunc();

function isDuplicateEmailRegistrationResponseFunc(responseText) {
    var normalizedResponse = String(responseText || "").trim().toUpperCase();
    if (normalizedResponse === "DUPLICATE_EMAIL") {
        return true;
    }
    return normalizedResponse.includes("DUPLICATE") && normalizedResponse.includes("EMAIL");
}

function addFunc() {
    var firstName = document.getElementById("fName").value;
    var lastName = document.getElementById("lName").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;


    //validation
    if(firstName.length < 2||lastName.length<2||email.length<2||password.length<2||confirmPassword.length<2)
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Input Incorect!",
                footer: "<a href=\"#\">Not Enough characters..</a>"
                });
            return;
        }

    if(!isValidEmailFunc(email))
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "That is an invalid email!",
                footer: "<a href=\"#\">Use a proper email like name@gmail.com or name@ust.edu.ph</a>"
                });
            return;
        }

    if(password.length < 12 || password.length > 50 || confirmPassword.length < 12 || confirmPassword.length > 50)
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Password must be 12 to 50 characters!",
                footer: "<a href=\"#\">Minimum password length is 12 characters.</a>"
                });
            return;
        }

    if(password != confirmPassword)
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Passwords do not match!",
                footer: "<a href=\"#\">Please retype your password..</a>"
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
            if (isDuplicateEmailRegistrationResponseFunc(returnedData)) {
                Swal.fire({
                    icon: "error",
                    title: "Duplicate Email",
                    text: "This email is already registered. Please use a different email or log in instead.",
                    confirmButtonText: "OK"
                });
                return;
            }

            Swal.fire({
                title: "Successfully added a user!",
                text: "You added a user! " + firstName + " " + lastName,
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    redirectFunc(5);
                }
            });
        },
        error: function(xhr) {
            if (isDuplicateEmailRegistrationResponseFunc(xhr.responseText)) {
                Swal.fire({
                    icon: "error",
                    title: "Duplicate Email",
                    text: "This email is already registered. Please use a different email or log in instead.",
                    confirmButtonText: "OK"
                });
                return;
            }

            Swal.fire({
                icon: "error",
                title: "Registration Error",
                text: "Unable to complete registration right now. Please try again.",
                confirmButtonText: "OK"
            });
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

function getUserControllerUrlFunc() {
    if (typeof window !== "undefined" && window.userAjaxUrl) {
        return window.userAjaxUrl;
    }
    return "../../../Controllers/Controller.php";
}

function getAdminActionWeekKeyFunc(dateValue) {
    var dateObj = new Date(dateValue);
    var dayNumber = dateObj.getDay();
    var mondayOffset = dayNumber === 0 ? -6 : (1 - dayNumber);
    dateObj.setDate(dateObj.getDate() + mondayOffset);
    var year = dateObj.getFullYear();
    var month = String(dateObj.getMonth() + 1).padStart(2, "0");
    var day = String(dateObj.getDate()).padStart(2, "0");
    return year + "-" + month + "-" + day;
}

function getRedHomeAdminActionStorageKeyFunc(homeID) {
    var weekKey = getAdminActionWeekKeyFunc(new Date());
    return "redHomeAdminActions_" + homeID + "_" + weekKey;
}

function getRedHomeAdminActionCountFunc(homeID) {
    try {
        var storageKey = getRedHomeAdminActionStorageKeyFunc(homeID);
        var rawValue = localStorage.getItem(storageKey);
        var parsedValue = parseInt(rawValue, 10);
        if (isNaN(parsedValue) || parsedValue < 0) {
            return 0;
        }
        return parsedValue;
    } catch (error) {
        return 0;
    }
}

function setRedHomeAdminActionCountDisplayFunc(homeID) {
    var counterElement = document.getElementById("adminActionsWeekCount");
    if (!counterElement) {
        return;
    }
    counterElement.textContent = getRedHomeAdminActionCountFunc(homeID);
}

function incrementRedHomeAdminActionCountFunc(homeID) {
    try {
        var storageKey = getRedHomeAdminActionStorageKeyFunc(homeID);
        var currentCount = getRedHomeAdminActionCountFunc(homeID);
        var nextCount = currentCount + 1;
        localStorage.setItem(storageKey, String(nextCount));
    } catch (error) {
    }
    setRedHomeAdminActionCountDisplayFunc(homeID);
}

function updateRedHomeRoleFunc(targetUserID, homeID, selectID, residentName) {
    var roleSelect = document.getElementById(selectID);
    if (!roleSelect) {
        return;
    }

    var roleName = roleSelect.value;
    if (roleName !== "Admin" && roleName !== "Member") {
        Swal.fire({
            icon: "error",
            title: "Invalid Role",
            text: "Please choose either Admin or Member.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    Swal.fire({
        title: "Update Resident Role?",
        text: residentName + " will be set to " + roleName + ".",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (!confirmResult.isConfirmed) {
            return;
        }

        $.ajax({
            url: getUserControllerUrlFunc(),
            type: "POST",
            dataType: "json",
            data: {
                adminAction: "updateUserRole",
                targetUserID: targetUserID,
                homeID: homeID,
                roleName: roleName
            },
            success: function(returnedData) {
                if (returnedData.success) {
                    incrementRedHomeAdminActionCountFunc(homeID);
                    Swal.fire({
                        title: "Role Updated",
                        text: returnedData.message || "Resident role has been updated.",
                        icon: "success",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#b3484e",
                        background: "#f8f9fb",
                        color: "#1f2937"
                    }).then((okResult) => {
                        if (okResult.isConfirmed) {
                            location.reload(true);
                        }
                    });
                    return;
                }

                Swal.fire({
                    icon: "error",
                    title: "Role Not Updated",
                    text: returnedData.message || "Unable to update role.",
                    confirmButtonColor: "#b3484e",
                    background: "#f8f9fb",
                    color: "#1f2937"
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Role Not Updated",
                    text: xhr.status + " : " + xhr.responseText,
                    confirmButtonColor: "#b3484e",
                    background: "#f8f9fb",
                    color: "#1f2937"
                });
            }
        });
    });
}

function addRedHomeResidentFunc(homeID) {
    var residentEmailInput = document.getElementById("redHomeResidentEmailInput");
    if (!residentEmailInput) {
        return;
    }

    var residentEmail = residentEmailInput.value.trim();
    if (!isValidEmailFunc(residentEmail)) {
        Swal.fire({
            icon: "error",
            title: "Invalid Email",
            text: "Please enter a valid resident email address.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    var currentTheme = getCurrentHomeThemeConfigFunc();
    Swal.fire({
        title: "Assign Resident to " + currentTheme.homeName + "?",
        text: residentEmail + " will be assigned to this home.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Assign",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (!confirmResult.isConfirmed) {
            return;
        }

        $.ajax({
            url: getUserControllerUrlFunc(),
            type: "POST",
            dataType: "json",
            data: {
                adminAction: "addResidentToHome",
                residentEmail: residentEmail,
                homeID: homeID
            },
            success: function(returnedData) {
                if (returnedData.success) {
                    Swal.fire({
                        title: "Resident Updated",
                        text: returnedData.message || "Resident was assigned successfully.",
                        icon: "success",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#b3484e",
                        background: "#f8f9fb",
                        color: "#1f2937"
                    }).then((okResult) => {
                        if (okResult.isConfirmed) {
                            location.reload(true);
                        }
                    });
                    return;
                }

                Swal.fire({
                    icon: "error",
                    title: "Resident Not Updated",
                    text: returnedData.message || "Unable to assign resident.",
                    confirmButtonColor: "#b3484e",
                    background: "#f8f9fb",
                    color: "#1f2937"
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Resident Not Updated",
                    text: xhr.status + " : " + xhr.responseText,
                    confirmButtonColor: "#b3484e",
                    background: "#f8f9fb",
                    color: "#1f2937"
                });
            }
        });
    });
}

function redirectFunc(redirectID) {
    if (redirectID == 1) {
        window.location.href = "../views/Dashboards/RedHome/RedHomeHome.php";
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
    
    if(!isValidEmailFunc(loginEmail))
        {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "That is an invalid email!",
                footer: "<a href=\"#\">Use a proper email like name@gmail.com or name@ust.edu.ph</a>"
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

function getBudgetControllerUrlFunc() {
    if (typeof window !== "undefined" && window.budgetAjaxUrl) {
        return window.budgetAjaxUrl;
    }
    return "../../../Controllers/BudgetController.php";
}

function showBudgetRequestErrorFunc(xhr, fallbackTitle) {
    var errorText = "Unable to process your request right now. Please try again.";
    if (xhr && xhr.status) {
        errorText = "Request failed (" + xhr.status + "). Please try again.";
    }

    Swal.fire({
        icon: "error",
        title: fallbackTitle || "Request Failed",
        text: errorText,
        confirmButtonColor: "#b3484e",
        background: "#f8f9fb",
        color: "#1f2937"
    });
}

function toggleBudgetAllocationManagerFunc() {
    var managerBox = document.getElementById("budgetCategoryManager");
    if (!managerBox) {
        return;
    }

    if (managerBox.style.display === "none" || managerBox.style.display === "") {
        managerBox.style.display = "block";
    } else {
        managerBox.style.display = "none";
    }
}

function addBudgetCategoryFunc(homeID) {
    var categoryNameInput = document.getElementById("newBudgetCategoryName");
    if (!categoryNameInput) {
        return;
    }

    var categoryName = categoryNameInput.value.trim();
    if (categoryName === "") {
        Swal.fire({
            icon: "error",
            title: "Category Name Required",
            text: "Enter a category name before adding.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    Swal.fire({
        title: "Add This Category?",
        text: "\"" + categoryName + "\" will be added to your budget categories.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Add",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "addBudgetCategory",
                    homeID: homeID,
                    categoryName: categoryName
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Category Added",
                            text: returnedData.message || "The new category is ready to use.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Category Not Added",
                            text: returnedData.message,
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Category Not Added");
                }
            });
        }
    });
}

function deleteBudgetCategoryFunc(homeID) {
    var categorySelect = document.getElementById("deleteBudgetCategorySelect");
    if (!categorySelect || categorySelect.value === "") {
        Swal.fire({
            icon: "error",
            title: "No Category Selected",
            text: "Select a category first, then try again.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    var categoryID = categorySelect.value;
    var categoryName = categorySelect.options[categorySelect.selectedIndex].text;

    Swal.fire({
        title: "Delete This Category?",
        text: "\"" + categoryName + "\" and its related transactions will be permanently removed.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "deleteBudgetCategory",
                    homeID: homeID,
                    categoryID: categoryID
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Category Deleted",
                            text: returnedData.message || "The category and its transactions were removed.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((okResult) => {
                            if (okResult.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Category Not Deleted",
                            text: returnedData.message,
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Category Not Deleted");
                }
            });
        }
    });
}

function updateBudgetMonthlyFunc(homeID, currentMonthlyBudget) {
    Swal.fire({
        title: "Update Monthly Budget",
        icon: "question",
        input: "number",
        inputValue: currentMonthlyBudget,
        inputAttributes: {
            min: 0,
            step: "0.01"
        },
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937",
        inputValidator: (value) => {
            if (value === "" || Number(value) < 0) {
                return "Please enter a valid budget amount.";
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "updateMonthlyBudget",
                    homeID: homeID,
                    monthlyBudget: result.value
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Budget Updated",
                            text: returnedData.message || "Monthly budget has been updated.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((okResult) => {
                            if (okResult.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Budget Not Updated",
                            text: returnedData.message,
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Budget Not Updated");
                }
            });
        }
    });
}

function showMonthlyBudgetEditFunc(currentMonthlyBudget, currentSavingsGoal) {
    var inlineEdit = document.getElementById("monthlyBudgetInlineEdit");
    var inputField = document.getElementById("monthlyBudgetInputField");
    var remainingInlineEdit = document.getElementById("remainingBalanceInlineEdit");
    var remainingInputField = document.getElementById("remainingBalanceInputField");
    var savingsInlineEdit = document.getElementById("savingsGoalInlineEdit");
    var savingsInputField = document.getElementById("savingsGoalInputField");
    if (!inlineEdit || !inputField) {
        return;
    }

    inlineEdit.style.display = "block";
    inputField.value = currentMonthlyBudget;
    if (remainingInlineEdit) {
        remainingInlineEdit.style.display = "block";
    }
    if (remainingInputField) {
        remainingInputField.value = "";
    }
    if (savingsInlineEdit) {
        savingsInlineEdit.style.display = "block";
    }
    if (savingsInputField) {
        savingsInputField.value = currentSavingsGoal;
    }
    inputField.focus();
}

function cancelMonthlyBudgetEditFunc() {
    var inlineEdit = document.getElementById("monthlyBudgetInlineEdit");
    var remainingInlineEdit = document.getElementById("remainingBalanceInlineEdit");
    var remainingInputField = document.getElementById("remainingBalanceInputField");
    var savingsInlineEdit = document.getElementById("savingsGoalInlineEdit");
    var savingsInputField = document.getElementById("savingsGoalInputField");
    if (inlineEdit) {
        inlineEdit.style.display = "none";
    }
    if (remainingInlineEdit) {
        remainingInlineEdit.style.display = "none";
    }
    if (remainingInputField) {
        remainingInputField.value = "";
    }
    if (savingsInlineEdit) {
        savingsInlineEdit.style.display = "none";
    }
    if (savingsInputField) {
        savingsInputField.value = "";
    }
}

function saveMonthlyBudgetEditFunc(homeID) {
    var inputField = document.getElementById("monthlyBudgetInputField");
    if (!inputField) {
        return;
    }

    var monthlyBudget = Number(inputField.value);
    if (isNaN(monthlyBudget) || monthlyBudget < 0) {
        Swal.fire({
            icon: "error",
            title: "Invalid Budget",
            text: "Enter a monthly budget amount that is 0 or higher.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    Swal.fire({
        title: "Save Monthly Budget?",
        text: "This will update the overall budget totals.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "updateMonthlyBudget",
                    homeID: homeID,
                    monthlyBudget: monthlyBudget
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Budget Updated",
                            text: returnedData.message || "Monthly budget has been updated.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((okResult) => {
                            if (okResult.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Budget Not Updated",
                            text: returnedData.message,
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Budget Not Updated");
                }
            });
        }
    });
}

function addBudgetTopUpFunc(homeID) {
    Swal.fire({
        title: "Add Funds to Budget",
        text: "Enter the amount to add to your remaining balance.",
        icon: "question",
        input: "number",
        inputAttributes: {
            min: 0.01,
            step: "0.01"
        },
        showCancelButton: true,
        confirmButtonText: "Add Funds",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937",
        inputValidator: (value) => {
            if (value === "" || Number(value) <= 0) {
                return "Please enter a valid amount greater than 0.";
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            var topUpAmount = Number(result.value);

            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "addBudgetTopUp",
                    homeID: homeID,
                    amount: topUpAmount
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Funds Added",
                            text: returnedData.message || "Your remaining balance has been increased.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((okResult) => {
                            if (okResult.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Funds Not Added",
                            text: returnedData.message || "Unable to add funds right now.",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Funds Not Added");
                }
            });
        }
    });
}

function saveRemainingBalanceEditFunc(homeID) {
    var remainingInputField = document.getElementById("remainingBalanceInputField");
    if (!remainingInputField) {
        return;
    }

    var topUpAmount = Number(remainingInputField.value);
    if (isNaN(topUpAmount) || topUpAmount <= 0) {
        Swal.fire({
            icon: "error",
            title: "Invalid Amount",
            text: "Enter an amount greater than 0.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    Swal.fire({
        title: "Add to Remaining Balance?",
        text: "This will increase your budget by ₱" + topUpAmount.toFixed(2) + ".",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Add",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "addBudgetTopUp",
                    homeID: homeID,
                    amount: topUpAmount
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Remaining Balance Updated",
                            text: returnedData.message || "Funds were added successfully.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((okResult) => {
                            if (okResult.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Update Failed",
                            text: returnedData.message || "Unable to add funds.",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Update Failed");
                }
            });
        }
    });
}

function saveSavingsGoalEditFunc(homeID) {
    var savingsInputField = document.getElementById("savingsGoalInputField");
    if (!savingsInputField) {
        return;
    }

    var savingsGoal = Number(savingsInputField.value);
    if (isNaN(savingsGoal) || savingsGoal < 0) {
        Swal.fire({
            icon: "error",
            title: "Invalid Savings Goal",
            text: "Enter a savings goal amount that is 0 or higher.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    Swal.fire({
        title: "Save Savings Goal?",
        text: "Your savings goal will be set to ₱" + savingsGoal.toFixed(2) + ".",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "updateSavingsGoal",
                    homeID: homeID,
                    savingsGoal: savingsGoal
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Savings Goal Updated",
                            text: returnedData.message || "Savings goal has been saved.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((okResult) => {
                            if (okResult.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Savings Goal Not Updated",
                            text: returnedData.message || "Unable to update savings goal.",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Savings Goal Not Updated");
                }
            });
        }
    });
}

function updateBudgetCategoryFunc(homeID, categoryID, categoryName, currentAllocatedAmount) {
    Swal.fire({
        title: "Edit " + categoryName + " Allocation",
        icon: "question",
        input: "number",
        inputValue: currentAllocatedAmount,
        inputAttributes: {
            min: 0,
            step: "0.01"
        },
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937",
        inputValidator: (value) => {
            if (value === "" || Number(value) < 0) {
                return "Please enter a valid amount.";
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Save Category Allocation?",
                text: "The allocation for \"" + categoryName + "\" will be updated.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#b3484e",
                cancelButtonColor: "#64748b",
                background: "#f8f9fb",
                color: "#1f2937"
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    $.ajax({
                        url: getBudgetControllerUrlFunc(),
                        type: "POST",
                        dataType: "json",
                        data: {
                            budgetAction: "updateBudgetCategory",
                            homeID: homeID,
                            categoryID: categoryID,
                            allocatedAmount: result.value
                        },
                        success: function(returnedData) {
                            if (returnedData.success) {
                                Swal.fire({
                                    title: "Category Updated",
                                    text: returnedData.message || "Category allocation has been updated.",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#b3484e",
                                    background: "#f8f9fb",
                                    color: "#1f2937"
                                }).then((okResult) => {
                                    if (okResult.isConfirmed) {
                                        location.reload(true);
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Allocation Not Updated",
                                    text: returnedData.message,
                                    confirmButtonColor: "#b3484e",
                                    background: "#f8f9fb",
                                    color: "#1f2937"
                                });
                            }
                        },
                        error: function(xhr) {
                            showBudgetRequestErrorFunc(xhr, "Allocation Not Updated");
                        }
                    });
                }
            });
        }
    });
}

function addBudgetTransactionFunc(homeID) {
    var amount = document.getElementById("budgetAmount").value;
    var categoryID = document.getElementById("budgetCategory").value;
    var transactionDate = document.getElementById("budgetDate").value;
    var note = document.getElementById("budgetNote").value;

    if (amount === "" || Number(amount) <= 0 || categoryID === "") {
        Swal.fire({
            icon: "error",
            title: "Invalid Transaction",
            text: "Enter a valid amount and choose a category before adding.",
            confirmButtonColor: "#b3484e",
            background: "#f8f9fb",
            color: "#1f2937"
        });
        return;
    }

    Swal.fire({
        title: "Add This Transaction?",
        text: "This amount will be deducted from the remaining budget.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Add",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
            $.ajax({
                url: getBudgetControllerUrlFunc(),
                type: "POST",
                dataType: "json",
                data: {
                    budgetAction: "addBudgetTransaction",
                    homeID: homeID,
                    categoryID: categoryID,
                    amount: amount,
                    note: note,
                    transactionDate: transactionDate
                },
                success: function(returnedData) {
                    if (returnedData.success) {
                        Swal.fire({
                            title: "Transaction Added",
                            text: returnedData.message || "The transaction was recorded successfully.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Transaction Not Added",
                            text: returnedData.message,
                            confirmButtonColor: "#b3484e",
                            background: "#f8f9fb",
                            color: "#1f2937"
                        });
                    }
                },
                error: function(xhr) {
                    showBudgetRequestErrorFunc(xhr, "Transaction Not Added");
                }
            });
        }
    });
}

$(document).ready(function() {
    $('select').formSelect();
});

function getTaskControllerUrlFunc() {
    if (typeof window !== "undefined" && window.taskAjaxUrl) {
        return window.taskAjaxUrl;
    }
    return "../../../Controllers/TaskController.php";
}

function showTaskRequestErrorFunc(xhr, fallbackTitle) {
    var errorText = "Unable to process your request right now. Please try again.";
    if (xhr && xhr.status) {
        errorText = "Request failed (" + xhr.status + "). Please try again.";
    }

    Swal.fire({
        icon: "error",
        title: fallbackTitle || "Request Failed",
        text: errorText,
        confirmButtonColor: "#b3484e",
        background: "#f8f9fb",
        color: "#1f2937"
    });
}

function getTaskStatusContainerSelectorFunc(status) {
    if (status === "In Progress") {
        return "#taskInProgressColumn";
    }
    if (status === "Done") {
        return "#taskDoneColumn";
    }
    return "#taskTodoColumn";
}

function getPriorityClassFunc(priorityText) {
    var normalized = String(priorityText || "Medium").toLowerCase().replace(" ", "-");
    if (normalized !== "high" && normalized !== "medium" && normalized !== "low") {
        normalized = "medium";
    }
    return "task-priority-" + normalized;
}

function buildTaskCardHTMLFunc(taskData, statusText) {
    var dueText = taskData.dueDate ? taskData.dueDate : "No Date";
    var priorityClass = getPriorityClassFunc(taskData.priorityLevel);
    var safeDescription = taskData.description ? taskData.description : "";
    var doneClass = statusText === "Done" ? " task-board-item-done" : "";
    var descriptionHTML = statusText === "Done" ? "" : "<div class=\"calendar-list-note\">" + safeDescription + "</div>";
    var metaTop = statusText === "Done" ? "" : "<div class=\"task-board-meta\"><span class=\"task-priority-tag " + priorityClass + "\">" + (taskData.priorityLevel || "Medium") + "</span><span class=\"task-due-tag\">Due: " + dueText + "</span></div>";

    var actionButtonsHTML = "";
    if (statusText === "To Do") {
        actionButtonsHTML = "<button type=\"button\" class=\"calendar-view-btn\" onclick=\"updateTaskStatusFunc(" + taskData.taskID + ", " + taskData.homeID + ", 'In Progress', this)\">Start</button><button type=\"button\" class=\"calendar-view-btn\" onclick=\"deleteTaskFunc(" + taskData.taskID + ", " + taskData.homeID + ", this)\">Delete</button>";
    } else if (statusText === "In Progress") {
        actionButtonsHTML = "<button type=\"button\" class=\"calendar-view-btn\" onclick=\"updateTaskStatusFunc(" + taskData.taskID + ", " + taskData.homeID + ", 'Done', this)\">Mark Done</button><button type=\"button\" class=\"calendar-view-btn\" onclick=\"updateTaskStatusFunc(" + taskData.taskID + ", " + taskData.homeID + ", 'To Do', this)\">Back</button>";
    } else {
        actionButtonsHTML = "<button type=\"button\" class=\"calendar-view-btn\" onclick=\"updateTaskStatusFunc(" + taskData.taskID + ", " + taskData.homeID + ", 'To Do', this)\">Reopen</button><button type=\"button\" class=\"calendar-view-btn\" onclick=\"deleteTaskFunc(" + taskData.taskID + ", " + taskData.homeID + ", this)\">Delete</button>";
    }

    return "<div class=\"task-board-item task-item-row" + doneClass + "\" data-task-id=\"" + taskData.taskID + "\" data-task-title=\"" + (taskData.taskTitle || "") + "\" data-task-description=\"" + safeDescription + "\" data-task-priority=\"" + (taskData.priorityLevel || "Medium") + "\" data-task-due=\"" + (taskData.dueDate || "") + "\"><div class=\"task-board-main\">" + taskData.taskTitle + "</div>" + descriptionHTML + metaTop + "<div class=\"task-board-meta\">" + actionButtonsHTML + "</div></div>";
}

function removeTaskEmptyStateFunc(columnElement) {
    if (!columnElement) {
        return;
    }
    var emptyState = columnElement.querySelector(".task-empty-state");
    if (emptyState) {
        emptyState.remove();
    }
}

function ensureTaskEmptyStateFunc(columnElement, messageText) {
    if (!columnElement) {
        return;
    }
    var cards = columnElement.querySelectorAll(".task-item-row");
    if (cards.length === 0 && !columnElement.querySelector(".task-empty-state")) {
        var emptyDiv = document.createElement("div");
        emptyDiv.className = "task-empty-state";
        emptyDiv.textContent = messageText;
        columnElement.appendChild(emptyDiv);
    }
}

function appendTaskCardToStatusFunc(taskData, statusText) {
    var targetSelector = getTaskStatusContainerSelectorFunc(statusText);
    var targetColumn = document.querySelector(targetSelector);
    if (!targetColumn) {
        return;
    }

    removeTaskEmptyStateFunc(targetColumn);

    var wrapper = document.createElement("div");
    wrapper.innerHTML = buildTaskCardHTMLFunc(taskData, statusText);
    targetColumn.appendChild(wrapper.firstChild);
}

function moveTaskCardToStatusFunc(buttonElement, statusText) {
    if (!buttonElement) {
        return;
    }

    var currentCard = buttonElement.closest(".task-item-row");
    if (!currentCard) {
        return;
    }

    var taskData = {
        taskID: currentCard.getAttribute("data-task-id"),
        homeID: typeof window !== "undefined" && window.redHomeID ? window.redHomeID : 1,
        taskTitle: currentCard.getAttribute("data-task-title"),
        description: currentCard.getAttribute("data-task-description"),
        priorityLevel: currentCard.getAttribute("data-task-priority"),
        dueDate: currentCard.getAttribute("data-task-due")
    };

    var sourceColumn = currentCard.closest(".task-status-column");
    currentCard.remove();
    appendTaskCardToStatusFunc(taskData, statusText);

    ensureTaskEmptyStateFunc(document.getElementById("taskTodoColumn"), "No tasks in To Do.");
    ensureTaskEmptyStateFunc(document.getElementById("taskInProgressColumn"), "No additional tasks are currently in progress.");
    ensureTaskEmptyStateFunc(document.getElementById("taskDoneColumn"), "No completed tasks yet.");

    if (sourceColumn) {
        if (sourceColumn.id === "taskTodoColumn") {
            ensureTaskEmptyStateFunc(sourceColumn, "No tasks in To Do.");
        } else if (sourceColumn.id === "taskInProgressColumn") {
            ensureTaskEmptyStateFunc(sourceColumn, "No additional tasks are currently in progress.");
        } else if (sourceColumn.id === "taskDoneColumn") {
            ensureTaskEmptyStateFunc(sourceColumn, "No completed tasks yet.");
        }
    }
}

function addTaskFunc(homeID) {
    var taskTitleInput = document.getElementById("taskTitleInput");
    var taskDescriptionInput = document.getElementById("taskDescriptionInput");
    var taskPriorityInput = document.getElementById("taskPriorityInput");
    var taskDueDateInput = document.getElementById("taskDueDateInput");
    if (!taskTitleInput || !taskPriorityInput || !taskDueDateInput) {
        return;
    }

    var taskTitle = taskTitleInput.value.trim();
    var taskDescription = taskDescriptionInput ? taskDescriptionInput.value.trim() : "";
    var taskPriority = taskPriorityInput.value;
    var taskDueDate = taskDueDateInput.value;

    if (taskTitle === "") {
        Swal.fire({ icon: "error", title: "Task Title Required", text: "Enter a task title before adding.", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
        return;
    }

    Swal.fire({
        title: "Add This Task?",
        text: "\"" + taskTitle + "\" will be added to your board.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Add",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (!confirmResult.isConfirmed) {
            return;
        }

        $.ajax({
            url: getTaskControllerUrlFunc(),
            type: "POST",
            dataType: "json",
            data: {
                taskAction: "addTask",
                homeID: homeID,
                taskTitle: taskTitle,
                description: taskDescription,
                priorityLevel: taskPriority,
                dueDate: taskDueDate
            },
            success: function(returnedData) {
                if (!returnedData.success) {
                    Swal.fire({ icon: "error", title: "Task Not Added", text: returnedData.message || "Unable to add task.", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
                    return;
                }

                var newTask = returnedData.task || {
                    taskID: Date.now(),
                    homeID: homeID,
                    taskTitle: taskTitle,
                    description: taskDescription,
                    priorityLevel: taskPriority,
                    dueDate: taskDueDate
                };

                appendTaskCardToStatusFunc(newTask, "To Do");
                ensureTaskEmptyStateFunc(document.getElementById("taskInProgressColumn"), "No additional tasks are currently in progress.");
                ensureTaskEmptyStateFunc(document.getElementById("taskDoneColumn"), "No completed tasks yet.");

                taskTitleInput.value = "";
                if (taskDescriptionInput) {
                    taskDescriptionInput.value = "";
                }
                taskDueDateInput.value = "";

                Swal.fire({ title: "Task Added", text: returnedData.message || "Your task has been added.", icon: "success", confirmButtonText: "OK", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
            },
            error: function(xhr) {
                showTaskRequestErrorFunc(xhr, "Task Not Added");
            }
        });
    });
}

function updateTaskStatusFunc(taskID, homeID, status, buttonElement) {
    Swal.fire({
        title: "Update Task Status?",
        text: "This task will move to \"" + status + "\".",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (!confirmResult.isConfirmed) {
            return;
        }

        $.ajax({
            url: getTaskControllerUrlFunc(),
            type: "POST",
            dataType: "json",
            data: {
                taskAction: "updateTaskStatus",
                taskID: taskID,
                homeID: homeID,
                status: status
            },
            success: function(returnedData) {
                if (returnedData.success) {
                    moveTaskCardToStatusFunc(buttonElement, status);
                    return;
                }

                Swal.fire({
                    icon: "error",
                    title: "Status Not Updated",
                    text: returnedData.message || "Unable to update status.",
                    confirmButtonColor: "#b3484e",
                    background: "#f8f9fb",
                    color: "#1f2937"
                });
            },
            error: function(xhr) {
                showTaskRequestErrorFunc(xhr, "Status Not Updated");
            }
        });
    });
}

function deleteTaskFunc(taskID, homeID, buttonElement) {
    Swal.fire({
        title: "Delete This Task?",
        text: "This task will be permanently removed.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (!confirmResult.isConfirmed) {
            return;
        }

        $.ajax({
            url: getTaskControllerUrlFunc(),
            type: "POST",
            dataType: "json",
            data: {
                taskAction: "deleteTask",
                taskID: taskID,
                homeID: homeID
            },
            success: function(returnedData) {
                if (!returnedData.success) {
                    Swal.fire({ icon: "error", title: "Task Not Deleted", text: returnedData.message || "Unable to delete task.", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
                    return;
                }

                var currentCard = buttonElement ? buttonElement.closest(".task-item-row") : document.querySelector('.task-item-row[data-task-id="' + taskID + '"]');
                if (currentCard) {
                    var sourceColumn = currentCard.closest(".task-status-column");
                    currentCard.remove();
                    if (sourceColumn) {
                        if (sourceColumn.id === "taskTodoColumn") {
                            ensureTaskEmptyStateFunc(sourceColumn, "No tasks in To Do.");
                        } else if (sourceColumn.id === "taskInProgressColumn") {
                            ensureTaskEmptyStateFunc(sourceColumn, "No additional tasks are currently in progress.");
                        } else if (sourceColumn.id === "taskDoneColumn") {
                            ensureTaskEmptyStateFunc(sourceColumn, "No completed tasks yet.");
                        }
                    }
                }
            },
            error: function(xhr) {
                showTaskRequestErrorFunc(xhr, "Task Not Deleted");
            }
        });
    });
}

function addCalendarEventFunc(homeID) {
    var calendarEventTitleInput = document.getElementById("calendarEventTitleInput");
    var calendarEventDateInput = document.getElementById("calendarEventDateInput");
    var calendarEventTimeInput = document.getElementById("calendarEventTimeInput");
    var calendarEventNoteInput = document.getElementById("calendarEventNoteInput");
    var calendarEventAllDayInput = document.getElementById("calendarEventAllDayInput");
    var calendarEventColorInput = document.getElementById("calendarEventColorInput");

    if (!calendarEventTitleInput || !calendarEventDateInput) {
        return;
    }

    var eventTitle = calendarEventTitleInput.value.trim();
    var eventDate = calendarEventDateInput.value;
    var eventTime = calendarEventTimeInput ? calendarEventTimeInput.value : "";
    var eventNote = calendarEventNoteInput ? calendarEventNoteInput.value.trim() : "";
    var isAllDay = calendarEventAllDayInput ? calendarEventAllDayInput.value : 0;
    var eventColor = calendarEventColorInput ? calendarEventColorInput.value : "#b3484e";

    if (eventTitle === "" || eventDate === "") {
        Swal.fire({ icon: "error", title: "Missing Event Data", text: "Event title and event date are required.", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
        return;
    }

    Swal.fire({
        title: "Add This Event?",
        text: "\"" + eventTitle + "\" will be added to the calendar.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Add",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#b3484e",
        cancelButtonColor: "#64748b",
        background: "#f8f9fb",
        color: "#1f2937"
    }).then((confirmResult) => {
        if (!confirmResult.isConfirmed) {
            return;
        }

        $.ajax({
            url: getTaskControllerUrlFunc(),
            type: "POST",
            dataType: "json",
            data: {
                taskAction: "addCalendarEvent",
                homeID: homeID,
                eventTitle: eventTitle,
                eventNote: eventNote,
                eventDate: eventDate,
                eventTime: eventTime,
                isAllDay: isAllDay,
                eventColor: eventColor
            },
            success: function(returnedData) {
                if (!returnedData.success) {
                    Swal.fire({ icon: "error", title: "Event Not Added", text: returnedData.message || "Unable to add event.", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
                    return;
                }

                if (window.redHomeCalendarInstance && returnedData.event) {
                    var eventRow = returnedData.event;
                    var startValue = eventRow.startDateTime ? String(eventRow.startDateTime).replace(" ", "T") : (eventRow.eventDate + "T" + (eventRow.eventTime || "00:00:00"));
                    var endValue = eventRow.endDateTime ? String(eventRow.endDateTime).replace(" ", "T") : null;
                    window.redHomeCalendarInstance.addEvent({
                        id: eventRow.eventID,
                        title: eventRow.eventTitle,
                        start: startValue,
                        end: endValue,
                        allDay: Number(eventRow.isAllDay) === 1,
                        backgroundColor: eventRow.eventColor || "#b3484e",
                        borderColor: eventRow.eventColor || "#b3484e",
                        extendedProps: {
                            eventID: eventRow.eventID,
                            eventNote: eventRow.eventNote
                        }
                    });
                }

                calendarEventTitleInput.value = "";
                calendarEventDateInput.value = "";
                if (calendarEventTimeInput) {
                    calendarEventTimeInput.value = "";
                }
                if (calendarEventNoteInput) {
                    calendarEventNoteInput.value = "";
                }

                Swal.fire({ title: "Event Added", text: returnedData.message || "Calendar event added.", icon: "success", confirmButtonText: "OK", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
            },
            error: function(xhr) {
                showTaskRequestErrorFunc(xhr, "Event Not Added");
            }
        });
    });
}

function deleteCalendarEventFunc(eventID, homeID, calendarEvent) {
    $.ajax({
        url: getTaskControllerUrlFunc(),
        type: "POST",
        dataType: "json",
        data: {
            taskAction: "deleteCalendarEvent",
            eventID: eventID,
            homeID: homeID
        },
        success: function(returnedData) {
            if (!returnedData.success) {
                Swal.fire({ icon: "error", title: "Event Not Deleted", text: returnedData.message || "Unable to delete event.", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
                return;
            }

            if (calendarEvent) {
                calendarEvent.remove();
            } else if (window.redHomeCalendarInstance) {
                var foundEvent = window.redHomeCalendarInstance.getEventById(String(eventID));
                if (foundEvent) {
                    foundEvent.remove();
                }
            }
        },
        error: function(xhr) {
            showTaskRequestErrorFunc(xhr, "Event Not Deleted");
        }
    });
}

function formatCalendarDateTimeForServerFunc(dateObj) {
    if (!dateObj) {
        return "";
    }
    var year = dateObj.getFullYear();
    var month = String(dateObj.getMonth() + 1).padStart(2, "0");
    var day = String(dateObj.getDate()).padStart(2, "0");
    var hours = String(dateObj.getHours()).padStart(2, "0");
    var mins = String(dateObj.getMinutes()).padStart(2, "0");
    var secs = String(dateObj.getSeconds()).padStart(2, "0");
    return year + "-" + month + "-" + day + " " + hours + ":" + mins + ":" + secs;
}

function persistCalendarScheduleUpdateFunc(eventObject, revertFunc) {
    var homeID = 1;
    if (typeof window !== "undefined" && window.redHomeID) {
        homeID = window.redHomeID;
    }

    $.ajax({
        url: getTaskControllerUrlFunc(),
        type: "POST",
        dataType: "json",
        data: {
            taskAction: "updateCalendarEventSchedule",
            eventID: eventObject.id,
            homeID: homeID,
            startDateTime: formatCalendarDateTimeForServerFunc(eventObject.start),
            endDateTime: formatCalendarDateTimeForServerFunc(eventObject.end),
            isAllDay: eventObject.allDay ? 1 : 0
        },
        success: function(returnedData) {
            if (!returnedData.success && typeof revertFunc === "function") {
                revertFunc();
                Swal.fire({ icon: "error", title: "Schedule Not Updated", text: returnedData.message || "Unable to save event schedule.", confirmButtonColor: "#b3484e", background: "#f8f9fb", color: "#1f2937" });
            }
        },
        error: function(xhr) {
            if (typeof revertFunc === "function") {
                revertFunc();
            }
            showTaskRequestErrorFunc(xhr, "Schedule Not Updated");
        }
    });
}

function applyTaskFilterFunc(filterType, buttonRef) {
    var taskRows = document.querySelectorAll(".task-item-row");
    var nowDate = new Date();
    var todayString = nowDate.toISOString().split("T")[0];
    var weekAfterDate = new Date(nowDate);
    weekAfterDate.setDate(nowDate.getDate() + 7);
    var weekString = weekAfterDate.toISOString().split("T")[0];

    taskRows.forEach(function(taskRow) {
        var dueDate = taskRow.getAttribute("data-task-due");
        var isVisible = true;

        if (!dueDate) {
            isVisible = filterType === "all";
        } else if (filterType === "today") {
            isVisible = (dueDate === todayString);
        } else if (filterType === "week") {
            isVisible = (dueDate >= todayString && dueDate <= weekString);
        } else if (filterType === "overdue") {
            isVisible = (dueDate < todayString);
        }

        taskRow.style.display = isVisible ? "block" : "none";
    });

    var taskFilterChips = document.querySelectorAll(".task-filter-chip[data-task-filter]");
    taskFilterChips.forEach(function(filterChip) {
        filterChip.classList.remove("task-filter-chip-active");
    });

    if (buttonRef) {
        buttonRef.classList.add("task-filter-chip-active");
    }
}

function initRedHomeCalendarFunc() {
    var calendarElement = document.getElementById("redHomeCalendar");
    if (!calendarElement || typeof FullCalendar === "undefined") {
        return;
    }

    var calendarEvents = [];
    if (typeof window !== "undefined" && window.redHomeCalendarEvents) {
        calendarEvents = window.redHomeCalendarEvents;
    }

    var calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: "dayGridMonth",
        height: 580,
        editable: true,
        eventDurationEditable: true,
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,listWeek"
        },
        events: calendarEvents,
        eventClick: function(info) {
            var noteText = "";
            if (info.event.extendedProps && info.event.extendedProps.eventNote) {
                noteText = info.event.extendedProps.eventNote;
            }

            Swal.fire({
                title: info.event.title,
                text: noteText === "" ? "No note provided." : noteText,
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Delete Event",
                cancelButtonText: "Close",
                confirmButtonColor: "#b3484e",
                cancelButtonColor: "#64748b",
                background: "#f8f9fb",
                color: "#1f2937"
            }).then((result) => {
                if (result.isConfirmed) {
                    var eventID = info.event.id;
                    var homeID = 1;
                    if (typeof window !== "undefined" && window.redHomeID) {
                        homeID = window.redHomeID;
                    }
                    deleteCalendarEventFunc(eventID, homeID, info.event);
                }
            });
        },
        eventDrop: function(info) {
            Swal.fire({
                title: "Save New Event Schedule?",
                text: "The new date/time for this event will be saved.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Save",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#b3484e",
                cancelButtonColor: "#64748b",
                background: "#f8f9fb",
                color: "#1f2937"
            }).then((result) => {
                if (result.isConfirmed) {
                    persistCalendarScheduleUpdateFunc(info.event, info.revert);
                } else {
                    info.revert();
                }
            });
        },
        eventResize: function(info) {
            Swal.fire({
                title: "Save New Event Duration?",
                text: "The updated duration will be saved.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Save",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#b3484e",
                cancelButtonColor: "#64748b",
                background: "#f8f9fb",
                color: "#1f2937"
            }).then((result) => {
                if (result.isConfirmed) {
                    persistCalendarScheduleUpdateFunc(info.event, info.revert);
                } else {
                    info.revert();
                }
            });
        }
    });

    window.redHomeCalendarInstance = calendar;
    calendar.render();
}