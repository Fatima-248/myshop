
        const sidebarToggleBtn = document.querySelector(".sidebar-toggle-btn");
        const overlay = document.querySelector(".sidebar-overlay");
        const body = document.body;

        const closeSidebar = () => {
            body.classList.remove("sidebar-open");
        };

        sidebarToggleBtn?.addEventListener("click", () => {
            body.classList.toggle("sidebar-open");
        });

        overlay?.addEventListener("click", closeSidebar);