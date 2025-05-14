const databaseMapping = {
  userAccount: "backend/db-files/user-account.php",
};

const pageMapping = {
  loginPage: "frontend/src/pages/login/login.php",
  signupPage: "frontend/src/pages/sign-up/sign-up.php",
  forgotPasswordPage: "frontend/src/pages/forgot-password/forgot-password.php",
  resetPasswordPage: "frontend/src/pages/reset-password/reset-password.php",
  adminDashboard: "frontend/src/pages/admin/dashboard/dashboard.php",
  doctorDashboard: "frontend/src/pages/doctor/dashboard/dashboard.php",
  patientDashboard: "frontend/src/pages/patient/dashboard/dashboard.php",

  patientMainDashboard: "frontend/src/components/main/patient/dashboard.php",
  patientMainRecords: "frontend/src/components/main/patient/records.php",
  patientMainAbout: "frontend/src/components/main/patient/about.php",
  patientMainFaq: "frontend/src/components/main/patient/faq.php",
  adminMainDashboard: "frontend/src/components/main/admin/dashboard.php",
  adminMainAbout: "frontend/src/components/main/admin/about.php",
  adminMainFaq: "frontend/src/components/main/admin/faq.php",
  doctorMainDashboard: "frontend/src/components/main/doctor/dashboard.php",
  doctorMainPatient: "frontend/src/components/main/doctor/patient.php",
  doctorMainAbout: "frontend/src/components/main/doctor/about.php",
  doctorMainFaq: "frontend/src/components/main/doctor/faq.php",
};

function loadPage(pageName) {
  const [basePage, params] = pageName.split("?");
  const url = pageMapping[basePage];

  if (!url) {
    console.error("404: Page not found");
    return;
  }

  if (pageName !== "resetPasswordPage") {
    sessionStorage.setItem("lastPage", pageName);
  }

  const fullUrl = params ? `${url}?${params}` : url;

  const mainContent = document.querySelector("main");
  if (mainContent) {
    mainContent.innerHTML = '<div class="loading">Loading...</div>';
  }

  fetch(fullUrl)
    .then((res) => res.text())
    .then((html) => {
      if (basePage.includes("Main")) {
        const mainContent = document.querySelector("main");
        if (mainContent) {
          mainContent.innerHTML = html;
          updateSidebarActiveState(basePage);
        }
      } else {
        document.getElementById("app").innerHTML = html;
      }

      attachAllListeners();

      if (!window.gsap) {
        console.warn("GSAP not found");
        return;
      }

      if (
        basePage === "adminDashboard" ||
        basePage === "doctorDashboard" ||
        basePage === "patientDashboard"
      ) {
        const sidebar = document.querySelector(".sidebar");
        const toggleBtn = document.querySelector(".toggle-sidebar");
        const mainContent = document.querySelector("main");
        const sidebarOverlay = document.querySelector(".sidebar-overlay");

        if (sidebar && toggleBtn) {
          console.log("Sidebar and toggle button found after page load");
          toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("show");
            if (sidebarOverlay) {
              sidebarOverlay.classList.toggle("show");
            }
            if (mainContent) {
              if (sidebar.classList.contains("show")) {
                document.body.style.overflow = "hidden";
              } else {
                document.body.style.overflow = "";
              }
            }
          });

          if (sidebarOverlay) {
            sidebarOverlay.addEventListener("click", () => {
              sidebar.classList.remove("show");
              sidebarOverlay.classList.remove("show");
              document.body.style.overflow = "";
            });
          }

          document.addEventListener("click", (e) => {
            if (window.innerWidth <= 992) {
              if (
                !sidebar.contains(e.target) &&
                !toggleBtn.contains(e.target)
              ) {
                sidebar.classList.remove("show");
                if (sidebarOverlay) {
                  sidebarOverlay.classList.remove("show");
                }
                document.body.style.overflow = "";
              }
            }
          });
        } else {
          console.log(
            "Sidebar or toggle button not found in loaded page:",
            basePage
          );
        }
      }

      if (basePage === "patientDashboard") {
        setTimeout(() => {
          const dashboard = document.querySelector("#patient-dashboard");
          const welcomeReveal = document.querySelector(".welcome-reveal");
          const h2 = document.querySelector("#patient-page h2");

          if (dashboard && dashboard.style.display === "block") {
            if (welcomeReveal) welcomeReveal.remove();
            if (h2) h2.remove();
            return;
          }

          if (sessionStorage.getItem("welcomeShown")) {
            if (welcomeReveal) welcomeReveal.remove();
            if (h2) h2.remove();
            if (dashboard) {
              dashboard.style.display = "block";
            }
            return;
          }

          sessionStorage.setItem("welcomeShown", "true");
          dashboard.style.display = "none";

          gsap.from(".block", {
            width: "0%",
            ease: "power1.in",
            stagger: 0.04,
            duration: 0.8,
            delay: 2,
          });

          gsap.to(".welcome-loader", 1, {
            x: 2,
            opacity: 0,
            ease: Expo.easeInOut,
            delay: 1.5,
          });

          gsap.to(".welcome-reveal", {
            backgroundColor: "none",
            duration: 1,
            ease: "power1.inOut",
            delay: 2.5,
          });

          gsap.to("#patient-page h2", {
            opacity: 1,
            duration: 1,
            delay: 3,
            ease: "power2.out",
            onStart: () => {
              if (h2) h2.style.visibility = "visible";
            },
          });

          gsap.to("#patient-page h2", {
            opacity: 0,
            duration: 1,
            delay: 6,
            ease: "power2.in",
            onComplete: () => {
              if (h2) h2.remove();
              if (welcomeReveal) welcomeReveal.remove();
              if (dashboard) {
                dashboard.style.display = "block";
              }
            },
          });
        }, 100);
      } else if (basePage === "forgotPasswordPage") {
        setTimeout(() => {
          const backBtnWrapper = document.getElementById("backBtnWrapper");
          const backText = backBtnWrapper?.querySelector(".back-text");

          if (backBtnWrapper && backText) {
            backBtnWrapper.addEventListener("mouseenter", () => {
              gsap.to(backText, {
                opacity: 1,
                x: 10,
                duration: 0.3,
                ease: "power2.out",
              });
            });

            backBtnWrapper.addEventListener("mouseleave", () => {
              gsap.to(backText, {
                opacity: 0,
                x: -10,
                duration: 0.2,
                ease: "power2.in",
              });
            });
          }
        }, 100);
      } else if (basePage === "resetPasswordPage") {
        const token = sessionStorage.getItem("resetToken");
        const tokenInput = document.getElementById("reset-token");
        if (token && tokenInput) {
          tokenInput.value = token;
        }
      }
    })
    .catch((error) => {
      console.error("Error loading page:", error);
      const mainContent = document.querySelector("main");
      if (mainContent) {
        mainContent.innerHTML =
          '<div class="error">Error loading content. Please try again.</div>';
      }
    });
}

function updateSidebarActiveState(pageName) {
  document.querySelectorAll(".sidebar-menu li").forEach((item) => {
    item.classList.remove("active");
  });

  const sidebarItem = document.querySelector(
    `.sidebar-menu li a[onclick*="${pageName}"]`
  );
  if (sidebarItem) {
    sidebarItem.parentElement.classList.add("active");
  }
}

function togglePasswordVisibility(checkbox, fields) {
  const type = checkbox.checked ? "text" : "password";
  fields.forEach((field) => field && (field.type = type));
}

function attachPasswordToggle() {
  const passwordToggles = document.querySelectorAll(".password-toggle");

  passwordToggles.forEach((eye) => {
    const targetId = eye.getAttribute("data-target");
    const passwordField = document.getElementById(targetId);

    if (eye && passwordField) {
      eye.addEventListener("click", () => {
        const isPassword = passwordField.type === "password";
        passwordField.type = isPassword ? "text" : "password";

        eye.classList.toggle("fa-eye", !isPassword);
        eye.classList.toggle("fa-eye-slash", isPassword);
      });
    }
  });
}

function attachCreatePasswordToggle() {
  const createPassword = document.getElementById("create-password");
  const confirmPassword = document.getElementById("confirm-password");
  const showPassword = document.getElementById("show-password");

  if (showPassword && createPassword && confirmPassword) {
    togglePasswordVisibility(showPassword, [createPassword, confirmPassword]);

    showPassword.addEventListener("change", () => {
      togglePasswordVisibility(showPassword, [createPassword, confirmPassword]);
    });
  }
}

function attachFormAction() {
  const displayErrors = (form, errors) => {
    const errorHTML = errors
      .map((msg) => `<div class="error-main"><p>${msg}</p></div>`)
      .join("");
    form.insertAdjacentHTML("beforebegin", errorHTML);
  };

  const displaySuccess = (form, message) => {
    const successHTML = `<div class="success-main"><p>${message}</p></div>`;
    form.insertAdjacentHTML("beforebegin", successHTML);
  };

  document.querySelectorAll("form[data-action-key]").forEach((form) => {
    const actionKey = form.dataset.actionKey;
    const actionUrl = databaseMapping[actionKey] ?? "";

    if (!actionUrl) {
      console.warn(`No action URL found for: ${actionKey}`);
      return;
    }

    form.action = actionUrl;

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      document
        .querySelectorAll(".error-main, .success-main")
        .forEach((el) => el.remove());

      const formData = new FormData(form);

      const isSignup =
        form.querySelector("#create-password") && form.querySelector("#name");
      const isLogin = form.querySelector("#login-password");
      const isForgotPassword = form.querySelector("#forgot-password-email");
      const isResetPassword =
        form.querySelector("#new-password") &&
        form.querySelector("#confirm-new-password");

      const errors = [];

      if (isSignup) {
        const name = form.querySelector("#name")?.value.trim() ?? "";
        const email = form.querySelector("#email")?.value.trim() ?? "";
        const password = form.querySelector("#create-password")?.value ?? "";
        const confirmPassword =
          form.querySelector("#confirm-password")?.value ?? "";

        const strongPasswordRegex =
          /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
        const emailRegex = /^[^\s@]+@(gmail|yahoo)\.com$/i;

        if (!name && !email && !password && !confirmPassword) {
          errors.push("No Information submitted.");
          displayErrors(form, errors);
          return;
        }

        if (!name) errors.push("Name is required.");
        if (!emailRegex.test(email)) errors.push("Email must be gmail/yahoo.");
        if (!strongPasswordRegex.test(password)) {
          errors.push(
            "Password must be strong (8+ chars incl. upper, lower, digit, symbol)."
          );
        }
        if (password !== confirmPassword)
          errors.push("Passwords do not match.");

        if (errors.length) {
          displayErrors(form, errors);
          return;
        }

        formData.append("signup", "1");
      } else if (isLogin) {
        const email = form.querySelector("#login-email")?.value.trim() ?? "";
        const password = form.querySelector("#login-password")?.value ?? "";
        const isAdmin = email.toLowerCase() === "admin";
        const emailRegex = /^[^\s@]+@(gmail|yahoo)\.com$/i;

        if (!email && !isAdmin) errors.push("Email is required.");
        else if (email && !isAdmin && !emailRegex.test(email))
          errors.push("Invalid email.");
        if (!password) errors.push("Password is required.");

        if (errors.length) {
          displayErrors(form, errors);
          return;
        }

        formData.append("login", "1");
      } else if (isForgotPassword) {
        const email =
          form.querySelector("#forgot-password-email")?.value.trim() ?? "";
        const emailRegex = /^[^\s@]+@(gmail|yahoo)\.com$/i;

        if (!email) errors.push("Email is required.");
        else if (!emailRegex.test(email)) errors.push("Invalid email format.");

        if (errors.length) {
          displayErrors(form, errors);
          return;
        }

        formData.append("reset-password", "1");
      } else if (isResetPassword) {
        const password = form.querySelector("#new-password")?.value ?? "";
        const confirmPassword =
          form.querySelector("#confirm-new-password")?.value ?? "";
        const token = form.querySelector("input[name='token']")?.value ?? "";

        const strongPasswordRegex =
          /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

        const submitBtn = form.querySelector('input[name="change-password"]');

        if (!token) {
          errors.push("Token is invalid or expired");

          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add("button-disabled");
          }
        } else if (!strongPasswordRegex.test(password)) {
          errors.push(
            "Password must be strong (8+ chars incl. upper, lower, digit, symbol)."
          );
        }
        if (password !== confirmPassword)
          errors.push("Passwords do not match.");

        if (errors.length) {
          displayErrors(form, errors);
          return;
        }

        formData.append("change-password", "1");
      }

      fetch(form.action, {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          document
            .querySelectorAll(".error-main, .success-main")
            .forEach((el) => el.remove());

          if (data.success) {
            if (isResetPassword) {
              displaySuccess(
                form,
                "Password changed successfully. Please login."
              );
              const submitBtn = form.querySelector(
                'input[name="change-password"]'
              );

              if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add("button-disabled");
              }
              setTimeout(() => {
                loadPage("loginPage");

                const isLocal = window.location.hostname === "localhost";
                const basePath = isLocal ? "/thesis_project" : "";

                history.replaceState(null, "", basePath + "/");
              }, 2000);
              return;
            }

            if (isForgotPassword) {
              const submitBtn = form.querySelector(
                'input[name="reset-password"]'
              );

              if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add("button-disabled");
              }
              displaySuccess(form, "Password reset link sent to your email!");
              return;
            }

            if (data.redirectTo) {
              const isLocal = window.location.hostname === "localhost";
              const basePath = isLocal ? "/thesis_project" : "";
              const fullUrl = new URL(data.redirectTo, window.location.origin);
              const path = fullUrl.pathname;
              const token = fullUrl.searchParams.get("token");

              const tokenMatch = path.match(
                /^\/?(admin|doctor|patient)\/auth-token$/
              );

              if (tokenMatch && token) {
                const role = tokenMatch[1];
                sessionStorage.setItem("authToken", token);

                history.replaceState(null, "", basePath + "/");

                loadPage(`${role}Dashboard`);
              } else {
                sessionStorage.clear();
                loadPage("loginPage");
              }
            } else {
              sessionStorage.clear();
              loadPage("loginPage");
            }
          } else if (data.errors) {
            displayErrors(form, Object.values(data.errors));
          } else {
            console.error("Unexpected response from server.");
            sessionStorage.clear();
            loadPage("loginPage");
          }
        })
        .catch((error) => {
          console.error("Form submission error:", error);
          sessionStorage.clear();
          loadPage("loginPage");
        });
    });
  });
}

function attachInputValidation() {
  const nameInput = document.getElementById("name");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("create-password");
  const confirmPasswordInput = document.getElementById("confirm-password");

  const iconMap = {
    nameCheck: document.querySelector("#name-check i"),
    emailCheck: document.querySelector("#email-check i"),
    createCheck: document.querySelector("#createpassword-check i"),
    confirmCheck: document.querySelector("#confirmpassword-check i"),
  };

  const strongPasswordRegex =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
  const emailRegex = /^[^\s@]+@(gmail|yahoo)\.com$/i;

  const toggleIcon = (icon, isValid, isEmpty) => {
    if (icon) {
      if (isEmpty) {
        icon.style.display = "none";
      } else {
        icon.style.display = "inline";
        icon.className = `fa-solid ${
          isValid ? "fa-check valid" : "fa-xmark invalid"
        }`;
      }
    }
  };

  nameInput?.addEventListener("input", () => {
    const value = nameInput.value;
    const validValue = value.replace(/[^a-zA-Z\s]/g, "");
    if (value !== validValue) {
      nameInput.value = validValue;
    }

    const trimmedValue = value.trim();
    toggleIcon(
      iconMap.nameCheck,
      trimmedValue.length >= 2,
      trimmedValue === ""
    );
  });

  emailInput?.addEventListener("input", () => {
    const value = emailInput.value.trim();
    toggleIcon(iconMap.emailCheck, emailRegex.test(value), value === "");
  });

  function validatePasswords() {
    const passwordVal = passwordInput?.value ?? "";
    const confirmPasswordVal = confirmPasswordInput?.value ?? "";
    const isPasswordValid = strongPasswordRegex.test(passwordVal);
    const isMatch = passwordVal === confirmPasswordVal;

    toggleIcon(iconMap.createCheck, isPasswordValid, passwordVal === "");
    toggleIcon(
      iconMap.confirmCheck,
      isPasswordValid && isMatch,
      confirmPasswordVal === ""
    );
  }

  passwordInput?.addEventListener("input", validatePasswords);
  confirmPasswordInput?.addEventListener("input", validatePasswords);
}

function updateSystemHealth() {
  const systemHealth = document.querySelector(".system-health-status");
  if (!systemHealth) return;

  const progressBars = document.querySelectorAll(".progress");
  progressBars.forEach((bar) => {
    const value = parseInt(bar.style.width);
    if (value > 90) {
      bar.style.backgroundColor = "#f44336";
    } else if (value > 70) {
      bar.style.backgroundColor = "#ff9800";
    } else {
      bar.style.backgroundColor = "#4CAF50";
    }
  });
}

function showAddUserModal() {
  document.getElementById("modalTitle").textContent = "Add New User";
  document.getElementById("userForm").reset();
  document.getElementById("userId").value = "";
  document.getElementById("password").required = true;
  document.querySelector(".password-group small").style.display = "none";
  document.getElementById("userModal").style.display = "block";
}

function editUser(userId) {
  document.getElementById("modalTitle").textContent = "Edit User";
  document.getElementById("userId").value = userId;
  document.getElementById("password").required = false;
  document.querySelector(".password-group small").style.display = "block";

  fetch(
    `/thesis_project/backend/db-files/user-account.php?action=get_user&id=${userId}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("name").value = data.user.name;
        document.getElementById("email").value = data.user.email;
        document.getElementById("role").value = data.user.role;
      }
    });

  document.getElementById("userModal").style.display = "block";
}

function deleteUser(userId) {
  if (confirm("Are you sure you want to delete this user?")) {
    fetch("/thesis_project/backend/db-files/user-account.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=delete_user&id=${userId}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          location.reload();
        } else {
          alert("Error deleting user: " + data.message);
        }
      });
  }
}

function closeModal() {
  const sectionModal = document.getElementById("sectionModal");
  const faqModal = document.getElementById("faqModal");
  const userModal = document.getElementById("userModal");

  if (sectionModal) {
    sectionModal.style.display = "none";
  }
  if (faqModal) {
    faqModal.style.display = "none";
  }
  if (userModal) {
    userModal.style.display = "none";
  }
}

function filterUsers() {
  const searchTerm = document.getElementById("userSearch").value.toLowerCase();
  const roleFilter = document.getElementById("roleFilter").value;
  const rows = document.querySelectorAll(".users-table tbody tr");

  rows.forEach((row) => {
    const name = row.cells[0].textContent.toLowerCase();
    const email = row.cells[1].textContent.toLowerCase();
    const role = row.cells[2].textContent.toLowerCase();

    const matchesSearch =
      name.includes(searchTerm) || email.includes(searchTerm);
    const matchesRole = !roleFilter || role.includes(roleFilter);

    row.style.display = matchesSearch && matchesRole ? "" : "none";
  });
}

function attachUserManagementListeners() {
  const searchInput = document.getElementById("userSearch");
  const roleFilter = document.getElementById("roleFilter");

  if (searchInput) {
    searchInput.addEventListener("input", filterUsers);
  }

  if (roleFilter) {
    roleFilter.addEventListener("change", filterUsers);
  }

  const userForm = document.getElementById("userForm");
  if (userForm) {
    userForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const userId = formData.get("userId");
      formData.append("action", userId ? "update_user" : "add_user");

      fetch("/thesis_project/backend/db-files/user-account.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            closeModal();
            loadPage("adminDashboard?page=users");
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while saving the user");
        });
    });
  }

  window.onclick = function (event) {
    const modal = document.getElementById("userModal");
    if (event.target === modal) {
      closeModal();
    }
  };
}

function attachAllListeners() {
  attachPasswordToggle();
  attachCreatePasswordToggle();
  attachFormAction();
  attachInputValidation();
  updateSystemHealth();
  attachUserManagementListeners();

  document.addEventListener("click", function (e) {
    const actionElement = e.target.closest("[data-action]");
    if (!actionElement) return;

    const action = actionElement.dataset.action;
    const id = actionElement.dataset.id;

    switch (action) {
      case "showAddSectionModal":
        showAddSectionModal();
        break;
      case "editSection":
        editSection(id);
        break;
      case "deleteSection":
        deleteSection(id);
        break;
      case "closeModal":
        closeModal();
        break;
      case "showAddFaqModal":
        showAddFaqModal();
        break;
      case "editFaq":
        editFaq(id, e);
        break;
      case "deleteFaq":
        deleteFaq(id, e);
        break;
      case "toggleFaq":
        toggleFaq(actionElement.closest(".faq-item"));
        break;
    }
  });

  const sectionForm = document.getElementById("sectionForm");
  if (sectionForm) {
    sectionForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const sectionId = formData.get("sectionId");
      const title = formData.get("sectionTitle");
      const contentType = formData.get("contentType");
      const content = formData.get("sectionContent");

      if (!title || !content) {
        alert("Title and content are required");
        return;
      }

      formData.append(
        "action",
        sectionId ? "update_about_section" : "add_about_section"
      );
      formData.append("title", title);
      formData.append("type", contentType);

      if (contentType === "list") {
        const listItems = content
          .split("\n")
          .map((line) => line.replace(/^•\s*/, "").trim())
          .filter((line) => line !== "");

        formData.set("content", JSON.stringify(listItems));
      } else {
        formData.set("content", content);
      }

      if (sectionId) {
        formData.append("id", sectionId);
      }

      fetch("/thesis_project/backend/db-files/content-management.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            closeModal();
            loadAboutContent();
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while saving the section");
        });
    });
  }

  const faqForm = document.getElementById("faqForm");
  if (faqForm) {
    faqForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const faqId = formData.get("faqId");
      const category = formData.get("faqCategory");
      const question = formData.get("faqQuestion");
      const answer = formData.get("faqAnswer");

      if (!category || !question || !answer) {
        alert("Category, question, and answer are required");
        return;
      }

      formData.append("action", faqId ? "update_faq" : "add_faq");
      formData.append("category", category);
      formData.append("question", question);
      formData.append("answer", answer);
      if (faqId) {
        formData.append("id", faqId);
      }

      fetch("/thesis_project/backend/db-files/content-management.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            closeModal();
            loadFaqContent();
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while saving the FAQ");
        });
    });
  }

  if (document.getElementById("aboutContent")) {
    loadAboutContent();
  }
  if (document.getElementById("faqContent")) {
    loadFaqContent();
  }

  window.addEventListener("click", function (event) {
    const sectionModal = document.getElementById("sectionModal");
    const faqModal = document.getElementById("faqModal");

    if (event.target === sectionModal) {
      closeModal();
    }
    if (event.target === faqModal) {
      closeModal();
    }
  });
}

function handleLogout() {
  sessionStorage.clear();

  const isLocal = window.location.hostname === "localhost";
  const basePath = isLocal ? "/thesis_project" : "";
  window.location.href = basePath + "/";
}

window.onload = () => {
  const pathname = window.location.pathname;
  const searchParams = new URLSearchParams(window.location.search);

  const isLocal = window.location.hostname === "localhost";
  const basePath = isLocal ? "/thesis_project" : "";

  const authToken = sessionStorage.getItem("authToken");
  const isResetPasswordPage = pathname.endsWith("/reset-password");

  if (isResetPasswordPage) {
    const errorMsg = searchParams.get("error");
    const resetToken = searchParams.get("token");

    if (errorMsg) {
      sessionStorage.setItem("loginError", decodeURIComponent(errorMsg));
      sessionStorage.clear();
      loadPage("loginPage");
      return;
    }

    if (resetToken) {
      sessionStorage.setItem("resetToken", resetToken);
      loadPage("resetPasswordPage");
      history.replaceState(
        null,
        "",
        `${basePath}/reset-password?token=${encodeURIComponent(resetToken)}`
      );
      return;
    }

    if (!sessionStorage.getItem("resetToken")) {
      sessionStorage.clear();
      loadPage("loginPage");
      return;
    }

    loadPage("resetPasswordPage");
    return;
  }

  if (!authToken) {
    loadPage("loginPage");
    return;
  }

  const roleMatch = pathname.match(/\/(admin|doctor|patient)\/auth-token$/);
  if (roleMatch) {
    const role = roleMatch[1];
    const token = searchParams.get("token");

    if (token) {
      fetch(`${basePath}/${role}/auth-token?token=${encodeURIComponent(token)}`)
        .then((response) => {
          if (!response.ok) throw new Error("Network error");
          return response.json();
        })
        .then((data) => {
          if (data.status === "success") {
            sessionStorage.setItem("authToken", token);
            loadPage(`${role}Dashboard`);
            history.replaceState(null, "", basePath + "/");
          } else {
            sessionStorage.removeItem("authToken");
            loadPage("loginPage");
          }
        })
        .catch((error) => {
          console.error("Token validation failed:", error);
          sessionStorage.removeItem("authToken");
          loadPage("loginPage");
        });

      return;
    }

    sessionStorage.removeItem("authToken");
    loadPage("loginPage");
    return;
  }

  const lastPage = sessionStorage.getItem("lastPage");
  if (lastPage && lastPage.includes("Dashboard")) {
    loadPage(lastPage);
  } else {
    const role = pathname.split("/")[1];
    if (role && ["admin", "doctor", "patient"].includes(role)) {
      loadPage(`${role}Dashboard`);
    } else {
      loadPage("loginPage");
    }
  }
};

function showAddSectionModal() {
  document.getElementById("modalTitle").textContent = "Add New Section";
  document.getElementById("sectionForm").reset();
  document.getElementById("sectionId").value = "";
  document.getElementById("sectionModal").style.display = "block";

  const contentTypeSelect = document.getElementById("contentType");
  const contentTextarea = document.getElementById("sectionContent");

  contentTypeSelect.addEventListener("change", function () {
    if (this.value === "list") {
      contentTextarea.value = "• First item\n• Second item\n• Third item";
      contentTextarea.placeholder =
        "Enter each item on a new line. Each line will be converted to a bullet point.";
      contentTextarea.addEventListener("keydown", handleListKeydown);
    } else {
      contentTextarea.value = "";
      contentTextarea.placeholder = "Enter your content here...";
      contentTextarea.removeEventListener("keydown", handleListKeydown);
    }
  });
}

function editSection(id) {
  document.getElementById("modalTitle").textContent = "Edit Section";
  document.getElementById("sectionId").value = id;
  document.getElementById("sectionModal").style.display = "block";

  const contentTypeSelect = document.getElementById("contentType");
  const contentTextarea = document.getElementById("sectionContent");

  contentTypeSelect.addEventListener("change", function () {
    if (this.value === "list") {
      let content = contentTextarea.value;
      if (!content.includes("•")) {
        content = content
          .split("\n")
          .filter((line) => line.trim() !== "")
          .map((line) => "• " + line.trim())
          .join("\n");
        contentTextarea.value = content;
      }
      contentTextarea.placeholder =
        "Enter each item on a new line. Each line will be converted to a bullet point.";
      contentTextarea.addEventListener("keydown", handleListKeydown);
    } else {
      contentTextarea.placeholder = "Enter your content here...";
      contentTextarea.removeEventListener("keydown", handleListKeydown);
    }
  });
  fetch(
    `/thesis_project/backend/db-files/content-management.php?action=get_about_sections`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const section = data.sections.find((s) => s.id === parseInt(id));
        if (section) {
          document.getElementById("sectionTitle").value = section.title;
          document.getElementById("contentType").value = section.type;

          if (section.type === "list") {
            try {
              const listItems = JSON.parse(section.content);
              contentTextarea.value = listItems
                .map((item) => "• " + item)
                .join("\n");
              contentTextarea.addEventListener("keydown", handleListKeydown);
            } catch (e) {
              contentTextarea.value = section.content;
            }
          } else {
            contentTextarea.value = section.content;
          }
        }
      }
    });
}

function handleListKeydown(e) {
  if (e.key === "Enter") {
    e.preventDefault();
    const textarea = e.target;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const value = textarea.value;
    const currentLine = value.substring(0, start).split("\n").pop();

    if (currentLine.trim() === "" || currentLine.trim() === "•") {
      textarea.value = value.substring(0, start) + "\n" + value.substring(end);
      textarea.selectionStart = textarea.selectionEnd = start + 1;
    } else {
      textarea.value =
        value.substring(0, start) + "\n• " + value.substring(end);
      textarea.selectionStart = textarea.selectionEnd = start + 3;
    }
  }
}

function deleteSection(id) {
  if (confirm("Are you sure you want to delete this section?")) {
    const formData = new FormData();
    formData.append("action", "delete_about_section");
    formData.append("id", id);

    fetch("/thesis_project/backend/db-files/content-management.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          loadAboutContent();
        } else {
          alert("Error deleting section: " + data.message);
        }
      });
  }
}

function loadAboutContent() {
  fetch(
    "/thesis_project/backend/db-files/content-management.php?action=get_about_sections"
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayAboutContent(data.sections);
      }
    });
}

function displayAboutContent(sections) {
  const container = document.getElementById("aboutContent");
  if (!container) return;

  container.innerHTML = "";

  const isAdmin =
    document.querySelector('.sidebar-menu li a[onclick*="adminDashboard"]') !==
    null;

  sections.forEach((section) => {
    const sectionElement = document.createElement("div");
    sectionElement.className = "section-item";
    sectionElement.innerHTML = `
      <div class="section-header">
        <h3 class="section-title">${section.title}</h3>
        ${
          isAdmin
            ? `
        <div class="section-actions">
          <button class="edit-btn" data-action="editSection" data-id="${section.id}">
            <i class="fas fa-edit"></i>
          </button>
          <button class="delete-btn" data-action="deleteSection" data-id="${section.id}">
            <i class="fas fa-trash"></i>
          </button>
        </div>
        `
            : ""
        }
      </div>
      <div class="section-content">
        ${
          section.type === "list"
            ? `<ul>${JSON.parse(section.content)
                .map((item) => `<li>${item}</li>`)
                .join("")}</ul>`
            : `<p>${section.content}</p>`
        }
      </div>
    `;
    container.appendChild(sectionElement);
  });
}

function showAddFaqModal() {
  document.getElementById("modalTitle").textContent = "Add New FAQ";
  document.getElementById("faqForm").reset();
  document.getElementById("faqId").value = "";
  document.getElementById("faqModal").style.display = "block";
}

function editFaq(id, event) {
  if (event) event.stopPropagation();
  document.getElementById("modalTitle").textContent = "Edit FAQ";
  document.getElementById("faqId").value = id;
  document.getElementById("faqModal").style.display = "block";

  fetch(
    `/thesis_project/backend/db-files/content-management.php?action=get_faqs`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const faq = data.faqs.find((f) => f.id === parseInt(id));
        if (faq) {
          document.getElementById("faqCategory").value = faq.category;
          document.getElementById("faqQuestion").value = faq.question;
          document.getElementById("faqAnswer").value = faq.answer;
        }
      }
    });
}

function deleteFaq(id, event) {
  if (event) event.stopPropagation();
  if (confirm("Are you sure you want to delete this FAQ?")) {
    const formData = new FormData();
    formData.append("action", "delete_faq");
    formData.append("id", id);

    fetch("/thesis_project/backend/db-files/content-management.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          loadFaqContent();
        } else {
          alert("Error deleting FAQ: " + data.message);
        }
      });
  }
}

function toggleFaq(element) {
  element.classList.toggle("active");
}

function loadFaqContent() {
  fetch(
    "/thesis_project/backend/db-files/content-management.php?action=get_faqs"
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayFaqContent(data.faqs);
      }
    });
}

function displayFaqContent(faqs) {
  const container = document.getElementById("faqContent");
  if (!container) return;

  container.innerHTML = "";

  const isAdmin =
    document.querySelector('.sidebar-menu li a[onclick*="adminDashboard"]') !==
    null;

  const groupedFaqs = faqs.reduce((acc, faq) => {
    if (!acc[faq.category]) {
      acc[faq.category] = [];
    }
    acc[faq.category].push(faq);
    return acc;
  }, {});

  Object.entries(groupedFaqs).forEach(([category, categoryFaqs]) => {
    const categoryElement = document.createElement("div");
    categoryElement.className = "faq-category";
    categoryElement.innerHTML = `
      <div class="category-header">
        <h3 class="category-title">${
          category.charAt(0).toUpperCase() + category.slice(1)
        } Questions</h3>
      </div>
      <div class="faq-cards">
        ${categoryFaqs
          .map(
            (faq) => `
          ${
            isAdmin
              ? `
          <div class="faq-item">
            <div class="faq-header" data-action="toggleFaq">
              <h4 class="faq-question">${faq.question}</h4>
              <div class="faq-actions">
                <button class="edit-btn" data-action="editFaq" data-id="${faq.id}">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="delete-btn" data-action="deleteFaq" data-id="${faq.id}">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
            <div class="faq-answer">
              <p>${faq.answer}</p>
            </div>
          </div>
          `
              : `
          <div class="faq-card">
            <div class="faq-card-header">
              <h4 class="faq-card-question">${faq.question}</h4>
            </div>
            <div class="faq-card-body">
              <p>${faq.answer}</p>
            </div>
          </div>
          `
          }
        `
          )
          .join("")}
      </div>
    `;
    container.appendChild(categoryElement);
  });

  if (!isAdmin) {
    const contactSupport = document.createElement("div");
    contactSupport.className = "contact-support";
    contactSupport.innerHTML = `
      <div class="contact-support-content">
        <h3>Still Need Help?</h3>
        <p>If you couldn't find the answer to your question, our support team is here to help.</p>
        <div class="contact-methods">
          <div class="contact-method">
            <i class="fas fa-envelope"></i>
            <span>support@beacompanion.com</span>
          </div>
          <div class="contact-method">
            <i class="fas fa-phone"></i>
            <span>+63 912 345 6789</span>
          </div>
        </div>
      </div>
    `;
    container.appendChild(contactSupport);
  }
}
