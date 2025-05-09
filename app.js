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

  // MAIN CONTENTS OF ROLES
  patientMainDashboard: "frontent/src/components/main/patient/dashboard.php",
  patientMainAbout: "frontent/src/components/main/patient/about.php",
};

function loadPage(pageName) {
  // Parse page name and parameters
  const [basePage, params] = pageName.split("?");
  const url = pageMapping[basePage];

  if (!url) {
    console.error("404: Page not found");
    return;
  }

  if (pageName !== "resetPasswordPage") {
    sessionStorage.setItem("lastPage", pageName);
  }

  // Add parameters to URL if they exist
  const fullUrl = params ? `${url}?${params}` : url;

  // Show loading state
  const mainContent = document.querySelector("main");
  if (mainContent) {
    mainContent.innerHTML = '<div class="loading">Loading...</div>';
  }

  fetch(fullUrl)
    .then((res) => res.text())
    .then((html) => {
      // If it's a dashboard page, load the content into the main area
      if (basePage.includes("Main")) {
        const mainContent = document.querySelector("main");
        if (mainContent) {
          mainContent.innerHTML = html;
          // Update active state in sidebar
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

        if (sidebar && toggleBtn) {
          console.log("Sidebar and toggle button found after page load");
          toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("show");
            if (mainContent) {
              if (sidebar.classList.contains("show")) {
                mainContent.style.overflow = "hidden";
                mainContent.style.pointerEvents = "none";
                mainContent.style.userSelect = "none";
                document.body.style.overflow = "hidden";
              } else {
                mainContent.style.overflow = "";
                mainContent.style.pointerEvents = "";
                mainContent.style.userSelect = "";
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
          if (sessionStorage.getItem("welcomeShown")) {
            const welcomeReveal = document.querySelector(".welcome-reveal");
            if (welcomeReveal) welcomeReveal.remove();

            const h2 = document.querySelector("#patient-page h2");
            if (h2) h2.remove();

            const dashboard = document.querySelector("#patient-dashboard");
            if (dashboard) {
              dashboard.style.display = "block";
              gsap.fromTo(
                dashboard,
                { opacity: 0 },
                {
                  opacity: 1,
                  duration: 1.5,
                  ease: "power2.out",
                }
              );
            }
            return;
          }
          sessionStorage.setItem("welcomeShown", "true");

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
              const h2 = document.querySelector("#patient-page h2");
              if (h2) h2.style.visibility = "visible";
            },
          });

          gsap.to("#patient-page h2", {
            opacity: 0,
            duration: 1,
            delay: 6,
            ease: "power2.in",
            onComplete: () => {
              const h2 = document.querySelector("#patient-page h2");
              if (h2) h2.remove();

              const welcomeReveal = document.querySelector(".welcome-reveal");
              if (welcomeReveal) welcomeReveal.remove();

              const dashboard = document.querySelector("#patient-dashboard");
              if (dashboard) {
                dashboard.style.display = "block";

                gsap.fromTo(
                  dashboard,
                  { opacity: 0 },
                  {
                    opacity: 1,
                    duration: 3,
                    ease: "power2.out",
                  }
                );
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
  // Remove active class from all sidebar items
  document.querySelectorAll(".sidebar-menu li").forEach((item) => {
    item.classList.remove("active");
  });

  // Add active class to current page
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
              const dashboardMatch = path.match(
                /^\/?(admin|doctor|patient)\/dashboard$/
              );

              if (tokenMatch && token) {
                const role = tokenMatch[1];
                sessionStorage.setItem("authToken", token);

                const newUrl = `${basePath}/auth-token?token=${encodeURIComponent(
                  token
                )}`;
                history.pushState(null, "", newUrl);

                loadPage(`${role}Dashboard`);
              } else if (dashboardMatch) {
                const role = dashboardMatch[1];
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
    nameInput.value = nameInput.value.replace(/[^a-zA-Z]/g, "");

    const value = nameInput.value.trim();
    toggleIcon(iconMap.nameCheck, value.length >= 2, value === "");
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

function attachAllListeners() {
  attachPasswordToggle();
  attachCreatePasswordToggle();
  attachFormAction();
  attachInputValidation();
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

  const lastPage = sessionStorage.getItem("lastPage") || "loginPage";
  loadPage(lastPage);
};
