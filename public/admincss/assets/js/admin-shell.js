(function () {
    "use strict";

    function storeTheme(theme) {
        try {
            localStorage.setItem("admin-theme", theme);
        } catch (error) {
            // The theme still works when browser storage is unavailable.
        }
    }

    function applyAdminTheme(theme) {
        const normalizedTheme = theme === "dark" ? "dark" : "light";
        const isDark = normalizedTheme === "dark";
        const themeToggle = document.getElementById("theme_toggle");

        document.documentElement.dataset.adminTheme = normalizedTheme;
        document.body.dataset.layoutMode = normalizedTheme;
        storeTheme(normalizedTheme);

        if (!themeToggle) {
            return;
        }

        themeToggle.setAttribute("aria-pressed", String(isDark));
        themeToggle.setAttribute(
            "aria-label",
            isDark ? "Enable light mode" : "Enable dark mode"
        );
        themeToggle.title = isDark
            ? "Switch to light mode"
            : "Switch to dark mode";

        const icon = themeToggle.querySelector("i");
        if (icon) {
            icon.classList.toggle("fa-moon", !isDark);
            icon.classList.toggle("fa-sun", isDark);
        }
    }

    function syncSidebarToggle() {
        const sidebarToggle = document.getElementById("toggle_btn");
        if (!sidebarToggle) {
            return;
        }

        const isCollapsed = document.body.classList.contains("mini-sidebar");
        sidebarToggle.setAttribute("aria-expanded", String(!isCollapsed));
        sidebarToggle.setAttribute(
            "aria-label",
            isCollapsed ? "Expand navigation" : "Collapse navigation"
        );
        sidebarToggle.title = isCollapsed
            ? "Expand navigation"
            : "Collapse navigation";

        const icon = sidebarToggle.querySelector("i");
        if (icon) {
            icon.classList.toggle("fa-angle-left", !isCollapsed);
            icon.classList.toggle("fa-angle-right", isCollapsed);
        }
    }

    function closeMobileSidebar() {
        document.querySelector(".main-wrapper")?.classList.remove("slide-nav");
        document.querySelector(".sidebar-overlay")?.classList.remove("opened");
        document.documentElement.classList.remove("menu-opened");
        document.getElementById("mobile_btn")?.setAttribute(
            "aria-expanded",
            "false"
        );
    }

    function syncMobileSidebarToggle() {
        const mobileToggle = document.getElementById("mobile_btn");
        const wrapper = document.querySelector(".main-wrapper");

        if (mobileToggle && wrapper) {
            mobileToggle.setAttribute(
                "aria-expanded",
                String(wrapper.classList.contains("slide-nav"))
            );
        }
    }

    function initializeAdminShell() {
        applyAdminTheme(
            document.documentElement.dataset.adminTheme || "light"
        );
        syncSidebarToggle();

        document.addEventListener("click", function (event) {
            const clickedElement = event.target.closest?.("button");

            if (clickedElement?.id === "theme_toggle") {
                const nextTheme =
                    document.documentElement.dataset.adminTheme === "dark"
                        ? "light"
                        : "dark";

                applyAdminTheme(nextTheme);
            }

            if (clickedElement?.id === "toggle_btn") {
                // The legacy layout script changes the body class first. This
                // reflects that resulting state in the icon and button text.
                syncSidebarToggle();
            }

            if (clickedElement?.id === "mobile_btn") {
                syncMobileSidebarToggle();
            }

            if (clickedElement?.id === "sidebar_close") {
                closeMobileSidebar();
            }
        });

        const sidebarStateObserver = new MutationObserver(syncSidebarToggle);
        sidebarStateObserver.observe(document.body, {
            attributes: true,
            attributeFilter: ["class"],
        });

        const wrapper = document.querySelector(".main-wrapper");
        if (wrapper) {
            const mobileSidebarObserver = new MutationObserver(
                syncMobileSidebarToggle
            );
            mobileSidebarObserver.observe(wrapper, {
                attributes: true,
                attributeFilter: ["class"],
            });
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initializeAdminShell, {
            once: true,
        });
    } else {
        initializeAdminShell();
    }
})();
